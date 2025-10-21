<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateSubDomainRequest;
use App\Providers\RouteServiceProvider;
use App\Subdomain;
use App\User;
use Dotenv\Dotenv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\NoReturn;
use Mpdf\Tag\Sub;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Encryption\Encrypter;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SubdomainController extends Controller
{

    /**
     * @var array|mixed
     */
    private mixed $input;

    private string $sub_domain;

    private string $sub_domain_file_name;

    public function getSubDomains()
    {
        //Only superadmin should be able to access this route
        if (request()->ajax()) {
            $sub_domains = Subdomain::query()
                ->leftJoin('users as u', 'subdomains.registered_by', '=', 'u.id')
                ->select('subdomains.id as sub_id','sub_domain', 'env_file', 'registered_by', 'subdomains.created_at as registered_on', 'db_name', 'active_modules',
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as registered_by")
                );

            return Datatables::of($sub_domains)
                ->addColumn('modules', function ($row) {
                    if (!empty($row->active_modules)) {
                        //Without json_decode, the implode function
                        //will treat the value as string rather than array
                        return implode(",", json_decode($row->active_modules));
                    }
                    return 'Not Set';
                })
                //                    <button data-href="{{action(\'App\Http\Controllers\SubdomainController@editSubDomain\', [$sub_id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".registered_subdomain_modal"><i class="glyphicon glyphicon-edit"></i> </button>
//                        &nbsp;
                ->addColumn(
                    'action',
                    '
                        <button data-href="{{action(\'App\Http\Controllers\SubdomainController@deleteSubDomain\', [$sub_id])}}" class="btn btn-xs btn-danger delete_sd_button"><i class="glyphicon glyphicon-trash"></i></button>                     
                        ')
                ->editColumn('registered_on', '{{@format_datetime($registered_on)}}')
                ->removeColumn('subdomains.id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('subdomain.index');
    }

    public function createSubDomain()
    {
        return view('subdomain.create_edit');
    }

    public function postSubDomain(StoreUpdateSubDomainRequest $request)
    {
        //Make sure it runs on linux only
        if (PHP_OS !== 'Linux') {
            throw new \Exception("Unsupported Operating system");
        }

        $this->input = $request->safe()->only(['SUBDOMAIN', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD']);

        //Environment
        $this->sub_domain =  strtolower($this->input['SUBDOMAIN']) . '.babaerp.live';
        $this->sub_domain_file_name = '.env.' .$this->sub_domain;

        //Check if the database has already been used
        if (Subdomain::query()->where('db_name', $this->input['DB_DATABASE'])->exists()) {

            $output = $this->respondWithError(message:  __("Database " . $this->input['DB_DATABASE'] . " has been already been used"), res_json: false);
            // dd($output);

            return back()->with('status', $output);
        }
        try {
            self::checkDBConnection();

            //Create env file
            if (self::createSubDomainEnvironment()) {
                //Paste subdomain DB Connection Details on env file
                if (self::writeSubDomainCredentialsOnEnv()) {
                    //Save the information on database
                    self::saveSubDomainCredentialsOnDB(subdomain_id: null);
                }
            }
            $output = $this->respondSuccess(message: __("lang_v1.added_success"), res_json: false);
            // dd($output);

            return back()->with('status', $output);


        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            $env_path = base_path($this->sub_domain_file_name);
            //Delete env file if it was created earlier
            if ($env_path && file_exists($env_path)) {
                unlink($env_path);
            }
            $output = $this->respondWentWrong($e);

            return back()->with('status', $output);

        }
    }

    public function editSubDomain($id)
    {
        $subdomain = Subdomain::query()->findOrFail($id);
        return view('subdomain.create_edit', compact('subdomain'));
    }

    public function updateSubDomain(StoreUpdateSubDomainRequest $request, $id)
    {
        try {
            $sub_domain = Subdomain::query()->findOrFail($id);
            $this->input = $request->safe()->only(['DB_USERNAME', 'DB_PASSWORD']);
            $this->input['DB_DATABASE'] = $sub_domain->db_name;

            self::checkDBConnection();

            if (self::writeSubDomainCredentialsOnEnv()) {
                //Save the information on database
                self::saveSubDomainCredentialsOnDB(subdomain_id: $id);

                $output = $this->respondSuccess(message: __("lang_v1.updated_success"), res_json: false);
            }
            return back()->with('status', $output);

        } catch (\Exception $e) {
            DB::rollBack();

            $output = $this->respondWentWrong($e);

            return back()->with('status', $output);

        }
    }

    public function deleteSubDomain($id)
    {
        if (request()->ajax()) {
            try {
                $subdomain = Subdomain::query()->findOrFail($id);
                $subdomain->delete();
                $output = $this->respondSuccess( message: __("lang_v1.deleted_success"));

            } catch (\Exception $e) {
                $output = $this->respondWentWrong($e);
            }

            return $output;
        }

    }

    /*
     * PRIVATE METHODS
     */
    private function writeEnvironmentFile($type, $val): void
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $val = '"'.trim($val).'"';
            file_put_contents($path, str_replace(
                $type.'="'.env($type).'"', $type.'='.$val, file_get_contents($path)
            ));
        }
    }

    private function createSubDomainEnvironment(): bool
    {
        //Argument
        $this->sub_domain =  strtolower($this->input['SUBDOMAIN']) . '.babaerp.live';
        $this->sub_domain_file_name = '.env.' .$this->sub_domain;

        //Check if this sub_domain file exists on the path
        $env_path = base_path($this->sub_domain_file_name);
        if (!file_exists($env_path)) {
            $process = Process::fromShellCommandline('touch ' . $this->sub_domain_file_name);
            $process->run();
        }

        $env_example = base_path('.env.example');
        if (!file_exists($env_example)) {
            exit("<b>.env.example file not found in <code>$env_example</code></b> <br/><br/> - In the downloaded codebase you will find .env.example file, please upload it and run again this page.");
        }

        //Copy the env file
        $copy_env = Process::fromShellCommandline('cp ' . $env_example . ' ' . $this->sub_domain_file_name);
        $copy_env->run();

        //Generate APP_KEY ON THE BOOTSTRAP FILE AFTER DOMAIN HAS BEEN ACCESSED
        $generate_key = 'base64:' . base64_encode(string: Encrypter::generateKey(config('app.cipher')));

        $this->input['APP_KEY'] = $generate_key;

        $move_env = Process::fromShellCommandline('mv ' . $this->sub_domain_file_name . ' ../');
        $move_env->run();

        //Check if it is there, delete the existing one
        $env_lines = file($env_path);
        foreach ($this->input as $index => $value) {
            foreach ($env_lines as $key => $line) {
                //Check if present then replace it.
                if (str_contains($line, $index)) {
                    $env_lines[$key] = $index . '="' . $value . '"' . PHP_EOL;
                }
            }
        }
        file_put_contents($env_path, $env_lines);

        return true;
    }

    private function writeSubDomainCredentialsOnEnv():bool
    {
        self::writeEnvironmentFile('SUB_DB_DATABASE', $this->input['DB_DATABASE']);
        self::writeEnvironmentFile('SUB_DB_USERNAME', $this->input['DB_USERNAME']);
        self::writeEnvironmentFile('SUB_DB_PASSWORD', $this->input['DB_PASSWORD']);

        return true;
    }

    private function saveSubDomainCredentialsOnDB($subdomain_id)
    {

        $data = [
            'sub_domain' => $this->sub_domain,
            'db_connection' => json_encode([
                'database' => $this->input['DB_DATABASE'],
                'username' => $this->input['DB_USERNAME'],
                'password' => $this->input['DB_PASSWORD'],
            ]),
            'db_name' => $this->input['DB_DATABASE'],
            'env_file' => $this->sub_domain_file_name,
            'registered_by' => auth()->id()
        ];

        DB::beginTransaction();

        !empty($subdomain_id)
            ? Subdomain::query()->where('id', $subdomain_id)->update($data)
            : Subdomain::query()->create($data);

        DB::commit();
    }

    private function checkDBConnection()
    {
        //Check for database details
        Artisan::call('cache:clear');
        @mysqli_connect(env('DB_HOST'), $this->input['DB_USERNAME'], $this->input['DB_PASSWORD'], $this->input['DB_DATABASE'], env('DB_PORT'));
    }
}
