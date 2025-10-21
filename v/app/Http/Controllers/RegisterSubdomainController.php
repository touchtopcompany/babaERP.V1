<?php

namespace App\Http\Controllers;

use App\Subdomains;
use Illuminate\Database\ConfigurationUrlParser;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RegisterSubdomainController extends Controller
{
    public function getSubDomains()
    {
        //Only superadmin should be able to access this route
        abort_unless(auth()->user()->can('superadmin'),403);
        if (request()->ajax()) {
            $tax_rates = Subdomains::query()
                ->select('*');

            // return Datatables::of($tax_rates)
            //     // ->addColumn(
            //     //     'action',
            //     //     '<button data-href="{{action(\'App\Http\Controllers\TypesOfServiceController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".type_of_service_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
            //     //         &nbsp;
            //     //     <button data-href="{{action(\'App\Http\Controllers\TypesOfServiceController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_type_of_service"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
            //     // )
            //     // ->editColumn('packing_charge', function ($row) {
            //     //     $html = '<span class="display_currency" data-currency_symbol="false">' . $row->packing_charge . '</span>';

            //     //     if ($row->packing_charge_type == 'percent') {
            //     //         $html .= '%';
            //     //     }

            //     //     return $html;
            //     // })
            //     ->removeColumn('id')
            //     ->rawColumns(['action', 'packing_charge'])
            //     ->make(true);
        }

        return view('subdomain.index');
    }

    public function create()
    {
        dd('po');
        abort_unless(auth()->user()->can('superadmin'),403);
        return view('subdomain.create');
    }

    public function postSubDomains(Request $request)
    {
        //Make sure it runs on linux only
        if (PHP_OS !== 'Linux') {
            throw new \Exception("Unsupported Operating system");
        }
        $request->validate(
            [
                'SUBDOMAIN' => 'required',
                'DB_DATABASE' => 'required',
                'DB_USERNAME' => 'required',
                'DB_PASSWORD' => 'required',

            ],
            [
                'DB_DATABASE.required' => 'Database Name is required',
                'DB_USERNAME.required' => 'Database Username is required',
                'DB_PASSWORD.required' => 'Database Password is required',
            ]
        );

        $input = $request->only(['SUBDOMAIN', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD']);

        //Check for database details
     //@mysqli_connect(env('DB_HOST'), $input['DB_USERNAME'], $input['DB_PASSWORD'], $input['DB_DATABASE'], env('DB_PORT'));


        //Argument
        $sub_domain = '.env.' . strtolower($input['SUBDOMAIN']) . '.babaerp.live';

        //Check if this sub_domain file exists on the path
        $env_path = base_path($sub_domain);
        if (!file_exists($env_path)) {
            $process = Process::fromShellCommandline('touch ' . $sub_domain);
            $process->run();

            // executes after the command finishes
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }

        $env_example = base_path('.env.example');
        if (!file_exists($env_example)) {
            exit("<b>.env.example file not found in <code>$env_example</code></b> <br/><br/> - In the downloaded codebase you will find .env.example file, please upload it and run again this page.");
        }

        //Copy the env file
        $copy_env = Process::fromShellCommandline('cp ' . $env_example . ' ' . $sub_domain);
        $copy_env->run();

        //Generate APP_KEY ON THE BOOTSTRAP FILE AFTER DOMAIN HAS BEEN ACCESSED
        $generate_key = 'base64:' . base64_encode(string: Encrypter::generateKey(config('app.cipher')));

        $input['APP_KEY'] = $generate_key;

        $move_env = Process::fromShellCommandline('mv ' . $sub_domain . ' ../');
        $move_env->run();

        //Check if it is there, delete the existing one
        $env_lines = file($env_path);
        foreach ($input as $index => $value) {
            foreach ($env_lines as $key => $line) {
                //Check if present then replace it.
                if (str_contains($line, $index)) {
                    $env_lines[$key] = $index . '="' . $value . '"' . PHP_EOL;
                }
            }
        }
        file_put_contents($env_path, $env_lines);

        //Paste subdomain DB Connection Details on env file

        $this->writeEnvironmentFile('SUB_DB_DATABASE', $input['DB_DATABASE']);
        $this->writeEnvironmentFile('SUB_DB_USERNAME', $input['DB_USERNAME']);
        $this->writeEnvironmentFile('SUB_DB_PASSWORD', $input['DB_PASSWORD']);


        $output = ['success' => true,
            'msg' => __('lang_v1.added_success'),
        ];
        return $output;
    }

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
}
