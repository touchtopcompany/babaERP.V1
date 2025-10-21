<?php

namespace Modules\Subdomain\Http\Controllers;

use App\Business;
use App\User;
use App\Utils\BusinessUtil;
use Carbon\Carbon;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Subdomain\Entities\Subdomain;
use Modules\Subdomain\Entities\SubUser;
use Modules\Subdomain\Http\Requests\StoreSubdomainRequest;
use Modules\Subdomain\Utils\Util;
use Modules\Superadmin\Entities\Package;
use Modules\Superadmin\Http\Controllers\BaseController;
use Modules\Superadmin\Notifications\NewSubscriptionNotification;
use Symfony\Component\Process\Process;
use Yajra\DataTables\Facades\DataTables;

class SubdomainController extends BaseController
{

    use Util;

    private mixed $input;
    private string $sub_domain;
    private string $sub_domain_file_name;

    public function __construct(protected BusinessUtil $businessUtil)
    {
    }

    public function index()
    {
        // dd($this->encryptDBConnection('u717332437_ub_stationary', '!JiC|FAJB1:5', 'uw6ry4km_training_babaerp'));
        //Only superadmin should be able to access this route
        if (request()->ajax()) {
            $sub_domains = Subdomain::query()
            //REMOVE THIS
            ->whereNot('db_name', 'uw6ry4km_training_babaerp')
            // ->orwhere('db_name', 'u717332437_db_photons')
                ->leftJoin('users as u', 'subdomains.registered_by', '=', 'u.id')
                ->select('subdomains.id as sub_id', 'sub_domain', 'admin_username', 'registered_by', 'active_modules',
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as registered_by")
                );

            return Datatables::of($sub_domains)
                ->addColumn('business_name', function ($row) {
                    return $this->getBusinessInfoUsingConn(admin_username: $row->admin_username)->name;
                })
                ->addColumn('active_subscription', function ($row) {
                    $active_sub = $this->getBusinessActiveSubscription(admin_username: $row->admin_username);
                    $text = __("No Active Subscription Found");
                    $html = '<span class="text-danger text-bold">' . $text . '</span>';

                    if (!empty($active_sub)) {
                        $active = $active_sub->end_date;
                        $formated = Carbon::parse($active)->format('d-m-Y');

                        $diff = Carbon::parse($active)->diffForHumans(['parts' => 2]);

                        $html = 'Expired On : <span class="text-bold">' . $formated . '</span><br>
                                         <span class="text-success text-bold">(' . $diff . ')</span>';
                    }
                    return $html;
                })
                ->addColumn('last_active', function ($row) {
                    $last_active = $this->getBusinessLastActivity(admin_username: $row->admin_username);

                    $text = __('Not Being Logged');
                    $html = '<span class="text-bold text-warning">' . $text . '</span>';
                    if (!empty($last_active)) {
                        $html = date_format(Carbon::parse($last_active), 'd-m-Y H:i');
                    }
                    return $html;
                })
                ->addColumn('account_status', function ($row) {
                    $status = $this->getBusinessInfoUsingConn(admin_username: $row->admin_username)->is_active;
                    $back = 'badge-soft-';
                    switch ($status) {
                        case 1:
                            $label = 'Active';
                            $back .= 'success';
                            break;
                        default:
                            $label = 'Disabled';
                            $back .= 'warning';
                    }
                    return '<span class="badge rounded-pill ' . $back . '">' . $label . '</span>';
                })
                ->addColumn('modules', function ($row) {
                    if (!empty(json_decode($row->active_modules))) {
                        //Without json_decode, the implode function
                        //will treat the value as string rather than array
                        return implode(",", json_decode($row->active_modules));
                    }
                    return 'Not Set';
                })
                ->addColumn(
                    'action', function ($row) {
                    $html = '';

                    $status = $this->getBusinessInfoUsingConn(admin_username: $row->admin_username)->is_active;

//                    if ($status == 1) {
//                        $html .= '<a href="' . action([SubdomainController::class, 'autoLogin'], [$row->sub_id]) . '" class="btn text-info"><i class="fa-solid fa-right-to-bracket"></i></a> ';
//                    }
                    //If no Active subscription, add new
                   $html .= '<a data-href="' . action([SubdomainController::class, 'addSubscription'], ['subdomain' => $row->sub_id]) . '" class="btn btn-modal text-warning" data-container=".view_modal"><i class="fa-solid fa-rotate"></i></a>';

                    // <a href="{{action(\'Modules\Subdomain\Http\Controllers\SubdomainController@autoLogin\', [$sub_id])}}" class="btn btn-xs text-success"><i class="fa-solid fa-toggle-on"></i></a>

                    //Delete also
//                    $html .= '<a data-href="' . action([SubdomainController::class, 'deleteSubDomain'], [$row->sub_id]) . '" class="btn btn-modal text-warning" data-container=".view_modal"><i class="fa-solid fa-rotate"></i></a>';

                    switch ($status) {
                        case 1:
                            $icon = 'fa-solid fa-ban';
                            $color = 'text-danger';
                            break;
                        default:
                            $icon = 'fa-solid fa-check-double';
                            $color = 'text-success';
                    }
                    $html .= '<a data-href="' . action([SubdomainController::class, 'enableDisableSubdomainAccount'], [$row->sub_id]) . '" class="btn btn-sm ' . $color . ' update_subdomain_btn"><i class="' . $icon . '"></i></a>';

                    return $html;
                })
                ->addColumn('package_price', function ($row) {
                    $active_sub = $this->getBusinessActiveSubscription(admin_username: $row->admin_username);
                    $package_price = 0;
                    if (!empty($active_sub)) {
                        $package_price = $active_sub->package_price;
                    }

                    return $this->businessUtil->num_f($package_price, true);
                })
                ->removeColumn('subdomains.id')
                ->rawColumns(['action', 'business_name', 'active_subscription', 'last_active', 'account_status'])
                ->make(true);
        }

        return view('subdomain::index');
    }

    public function addSubscription()
    {
        $subdomain_id = request()->query('subdomain');
        return view('subdomain::create_edit_subscription')
            ->with(compact( 'subdomain_id'));
    }

    public function saveSubscription(Request $request)
    {
        try {
            DB::beginTransaction();

            $input = $request->only(['subdomain_id', 'subscription_duration', 'subscription_duration_type', 'subscription_amount']);
            $user_id = $request->session()->get('user.id');

            //Get the domain
            $domain = Subdomain::query()->findOrFail($input['subdomain_id']);
            $business_id = $this->getBusinessInfoUsingConn(admin_username: $domain->admin_username)->id;

            $subscription = ['start_date' => '', 'end_date' => '', 'trial_end_date' => ''];

            $start_date = $this->getBusinessEndDateSubscription(admin_username: $domain->admin_username);

            $subscription['start_date'] = $start_date->toDateString();

            //Check subscription
            //Trial time (1 Week)
            if($input['subscription_duration_type'] == 'months') {
                $subscription['end_date'] = $start_date->copy()->addMonths($input['subscription_duration'])->toDateString();
            }

            if($input['subscription_duration_type'] == 'years'){
                $subscription['end_date'] = $start_date->copy()->addYears($input['subscription_duration'])->toDateString();
            }

            $subscription['trial_end_date'] = $start_date->copy()->addDays(7)->toDateString();

            $subscription['created_id'] = $user_id;
            $subscription['status'] = 'approved';
            $subscription['business_id'] = $business_id;
            $subscription['package_id'] = 1;
            $subscription['package_price'] = $input['subscription_amount'];
            $subscription['package_details'] = json_encode(['subscription' => 'superadmin']);

            $subscription['created_at'] = now();


            $this->saveNewSubscription(admin_username: $domain->admin_username, details:  $subscription);

            DB::commit();

//            if (! empty($email) && $is_notif_enabled == 1) {
//                Notification::route('mail', $email)
//                    ->notify(new NewSubscriptionNotification($subscription));
//            }

            return $this->respondSuccess(__("subdomain::lang.subscription_successfully_saved"), true);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondWentWrong($e);
        }
    }

    public function enableDisableSubdomainAccount(string|int $subdomain)
    {
        DB::transaction(function () use ($subdomain) {
            $this->updateBusinessStatus(subdomain: $subdomain);
        });

        return $this->respondSuccess(__("lang_v1.updated_success"), true);
    }

    public function create()
    {
        return view('subdomain::create_edit');
    }

    public function store(StoreSubdomainRequest $request)
    {
        //Make sure it runs on linux only
        $this->input = $request->safe()->only([
            'business_name',
            'owner_name', 'owner_username', 'owner_email', 'owner_password',
            'subdomain'
        ]);

        $this->input['DB_DATABASE'] = $request->get('database');
        $this->input['DB_USERNAME'] = $request->get('username');
        $this->input['DB_PASSWORD'] = $request->get('password');


        //Environment
        $this->sub_domain = strtolower($this->input['subdomain']) . '.babaerp.live';
        $this->sub_domain_file_name = '.env.' . $this->sub_domain;

        //Check if the database has already been used
        if (Subdomain::query()->where('sub_domain', $this->sub_domain)->exists()) {
            return $this->respondWithError(__("Subdomain  " . $this->sub_domain . " has been already been registered"), false);
        }

        try {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');


            DB::beginTransaction();

            \Config::set('database.connections.sub_domain.database', $this->input['DB_DATABASE']);
            \Config::set('database.connections.sub_domain.username', $this->input['DB_USERNAME']);
            \Config::set('database.connections.sub_domain.password', $this->input['DB_PASSWORD']);

            DB::purge('sub_domain');
            $connect = DB::connection('sub_domain');

            //Run migration
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            DB::statement('SET default_storage_engine=INNODB;');

            Artisan::call('cache:clear');
            Artisan::call('migrate:fresh', ['--force' => true, '--database'=>'sub_domain', '--path' => 'database/migrations']);
            Artisan::call('module:migrate', ['--force' => true, '--database'=>'sub_domain', 'module' => 'Subdomain']);
            Artisan::call('module:migrate', ['--force' => true, '--database'=>'sub_domain', 'module' => 'Superadmin', '--subpath' => '2018_06_28_182803_create_subscriptions_table.php']);
            Artisan::call('db:seed', ['--force' => true, '--database'=>'sub_domain']);

            //Check if valid
            $connect->getPdo();

            $owner_name = explode(' ', $this->input['owner_name']);
            $first_name = $owner_name[0];

            $last_name = match (count($owner_name)) {
                1 => $owner_name[0],
                default => $owner_name[1],
            };

            $owner_details = [
                'surname' => 'Mr',
                'first_name' => $first_name,
                'last_name' => $last_name,
                'username' => $this->input['owner_username'],
                'email' => $this->input['owner_email'],
                'password' => Hash::make($this->input['owner_password']),
                'language' => ! empty($details['language']) ? $details['language'] : 'en',
            ];

            $connect->table('users')->insert($owner_details);

            //Get the user
            $user = $connect->table('users')->where('email', $owner_details['email'])->first();

            $business_details = [
                'name' => $this->input['business_name'],
                //Save as TZS by Default
                'currency_id' => 138,
                'time_zone' => 'Africa/Dar_es_Salaam',
                'default_profit_percent' => 25,
                'owner_id' => $user->id,
                'stop_selling_before' => 0,
                'start_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $business_details['sell_price_tax'] = 'includes';
            //Add POS shortcuts
            $business_details['keyboard_shortcuts'] = '{"pos":{"express_checkout":"shift+e","pay_n_ckeckout":"shift+p","draft":"shift+d","cancel":"shift+c","edit_discount":"shift+i","edit_order_tax":"shift+t","add_payment_row":"shift+r","finalize_payment":"shift+f","recent_product_quantity":"f2","add_new_product":"f4"}}';

            //Add prefixes
            $business_details['ref_no_prefixes'] = json_encode([
                'purchase' => 'PO',
                'stock_transfer' => 'ST',
                'stock_adjustment' => 'SA',
                'sell_return' => 'CN',
                'expense' => 'EP',
                'contacts' => 'CO',
                'purchase_payment' => 'PP',
                'sell_payment' => 'SP',
                'business_location' => 'BL',
            ]);

            //Enablec modules
            $business_details['enabled_modules'] = json_encode([
                "products",
                "purchases",
                "add_sale",
                "pos_sale",
                "expenses"
            ]);

            //Disable inline tax editing
            $business_details['enable_inline_tax'] = 0;

            $connect->table('business')->insert($business_details);

            $business = $connect->table('business')->where('name', $business_details['name'])->first();

            //Admin Role
            $connect->table('roles')->insert(['name' => 'Admin#'. $business->id,
                'business_id' => 1,
                'guard_name' => 'web',
                'is_default' => 1,
            ]);

            $admin_role = $connect->table('roles')->where('name', 'Admin#'. $business->id)->first();

            //Assign as an admin
            $query = SubUser::query()->where('id', $user->id)->first();
            $connect->table('model_has_roles')->insert(['role_id' => $admin_role->id, 'model_type' => 'App\User', 'model_id' => $user->id]);
            $query->business_id = $business->id;
            $query->save();

            $connect->disconnect();

            //Create env file
            if (self::createSubDomainEnvironment()) {
                //Paste subdomain DB Connection Details on env file
                if (self::writeSubDomainCredentialsOnEnv()) {
                    //Save the information on database
                    self::saveSubDomainCredentialsOnDB();
                }
            }

            DB::commit();
            return $this->respondSuccess(__("lang_v1.added_success"), true);

        } catch (\Exception $e) {
            DB::rollBack();
            $env_path = base_path($this->sub_domain_file_name);
            //Delete env file if it was created earlier
            if ($env_path && file_exists($env_path)) {
                unlink($env_path);
            }
            return $this->respondWentWrong($e);
        }
    }

    public function deleteSubDomain($id)
    {
        if (request()->ajax()) {
            try {
                $subdomain = Subdomain::query()->findOrFail($id);
                $subdomain->delete();
                $output = $this->respondSuccess(message: __("lang_v1.deleted_success"), json_response: true);

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
            $val = '"' . trim($val) . '"';
            file_put_contents($path, str_replace(
                $type . '="' . env($type) . '"', $type . '=' . $val, file_get_contents($path)
            ));
        }
    }

    private function createSubDomainEnvironment(): bool
    {

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
        $this->input['APP_DEBUG'] = 'false';

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

    private function writeSubDomainCredentialsOnEnv(): bool
    {
        self::writeEnvironmentFile('SUB_DATABASE', $this->input['DB_DATABASE']);
        self::writeEnvironmentFile('SUB_USERNAME', $this->input['DB_USERNAME']);
        self::writeEnvironmentFile('SUB_PASSWORD', $this->input['DB_PASSWORD']);

        return true;
    }

    private function encryptDBConnection($user, $pass, $db)
    {
        return encrypt(json_encode([
            'database' => $db,
            'username' => $user,
            'password' => $pass,
        ]));
    }
    private function dencryptDBConnection($encrypted)
    {
        return dencrypt($encrypted);
    }

    private function saveSubDomainCredentialsOnDB($subdomain_id = null)
    {
        $data = [
            'sub_domain' => $this->sub_domain,
            'db_connection' => $this->encryptDBConnection(user: $this->input['DB_USERNAME'], pass: $this->input['DB_PASSWORD'], db:  $this->input['DB_DATABASE']),
            'db_name' => $this->input['DB_DATABASE'],
            'admin_username' => $this->input['owner_username'],
            'registered_by' => auth()->id()
        ];

        DB::beginTransaction();

        !empty($subdomain_id)
            ? Subdomain::query()->where('id', $subdomain_id)->update($data)
            : Subdomain::query()->create($data);

        DB::commit();
    }
}
