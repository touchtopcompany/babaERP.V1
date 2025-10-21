<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Utils\BusinessUtil;
use App\Transaction;

use App\Utils\ModuleUtil;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * All Utils instance.
     */
    protected $businessUtil;

    protected $moduleUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->middleware('guest')->except('logout');
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
   
        // $this->middleware('cacheResponse:600', ['only' => ['showLoginForm']]);
  
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Change authentication from email to username
     *
     * @return void
     */
    public function username()
    {
        return 'username';
    }

    public function logout()
    {
        // if(!$this->existingDraftTransactions()){
        //     $output = ['warning' => 1,
        //         'msg' => __('messages.existing_draft')
        //     ];
        //     return redirect()->to('/sells/drafts')->with('status', $output);
        // }
        // else {
            $this->businessUtil->activityLog(auth()->user(), 'logout');

            request()->session()->flush();
            \Auth::logout();

            return redirect('/login');
        // }
    }

    /**
     * The user has been authenticated.
     * Check if the business is active or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $this->businessUtil->activityLog($user, 'login', null, [], false, $user->business_id);

        if (! $user->business->is_active) {
            \Auth::logout();

            return redirect('/login')
              ->with(
                  'status',
                  ['success' => 0, 'msg' => __('lang_v1.business_inactive')]
              );
        } elseif ($user->status != 'active') {
            \Auth::logout();

            return redirect('/login')
              ->with(
                  'status',
                  ['success' => 0, 'msg' => __('lang_v1.user_inactive')]
              );
        } elseif (! $user->allow_login) {
            \Auth::logout();

            return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.login_not_allowed')]
                );
        } elseif (($user->user_type == 'user_customer') && ! $this->moduleUtil->hasThePermissionInSubscription($user->business_id, 'crm_module')) {
            \Auth::logout();

            return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.business_dont_have_crm_subscription')]
                );
        }
    }

    protected function redirectTo()
    {
        $user = \Auth::user();
       if($this->existingDraftTransactions()){
            return '/sells/drafts';
        }
        else {

            if (!$user->can('dashboard.data') && $user->can('sell.create')) {
                return '/pos/create';
            }

            if ($user->user_type == 'user_customer') {
                return 'contact/contact-dashboard';
            }

            return '/home';
        }
    }
        protected function existingDraftTransactions(){
        $user_id = \Auth::user()->id;
        //Check for draft sells
        //Draft POS Bill for this user if any
        $draft_transaction = Transaction::query()
            ->where([
                'type' => 'sell',
                'status' => 'draft',
                'created_by' => $user_id
            ]);
        $exists = false;
        if($draft_transaction->exists()){
            $exists = true;
        }
        return $exists;
        
    }

}
