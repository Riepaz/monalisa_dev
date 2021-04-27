<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Role;
use App\Libraries\HttpClient;
use App\Libraries\Encryption;
use Config\Constants;
use Auth;
use Lang;


class AccountsLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        //$this->middleware('guest')->except('logout')->except('index');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function username()
    {
            return 'username';
    }

    /* protected function guard()
    {
        return Auth::guard('widyaiswara');
    } */

    public function login(Request $request)
    {   
        //LOGIN 
        $this->validateLogin($request);
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
    
        if ( $this->guard()->validate($this->credentials($request))) {
            return $this->authentication($request);
        
        }else{
            $user = $this->requestExternalSource();
            
            if ( $this->guard()->validate($this->credentials($request))) {
                return $this->authentication($request);
            }
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }


    
    public function authentication(Request $request)
    {
        $user = $this->guard()->getLastAttempted();
        
        $userPass = $user->toArray();
        $userPass['password'] = $request->password;
        
        if ($user->is_active && $this->attemptLogin($request)) {
            
            if(!$user->is_deleted){
                $sessionData = [
                    
                    'email'=> $user->email,
                    'username'=> $request->username,
                    'password'=> Encryption::encrypt($request->password),
    
                ];
    
                Session::put('monalisa_session' , $sessionData);
                
                return $this->sendLoginResponse($request);
            }else {
                        
                $this->incrementLoginAttempts($request);
                return redirect()
                    ->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors(['active' => 'Akun Telah Dihapus..']);
            }
            
                    
        } else {
                    
            $this->incrementLoginAttempts($request);
            return redirect()
                ->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors(['active' => Lang::get('auth.active')]);
        }
    }

    public function authenticated($request, $user)
    {
        if ($user->hasRole('instructor')) {
            return redirect()->route('instructor.dashboard');
        } elseif ($user->hasRole('admin') || $user->hasRole('superadmin') || $user->hasRole('adminprovinsi')) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('home');
        }
    }

    public function requestExternalSource(){
        $url = Constants::$simsdm_url_login;

        $body = '{"' .
            Constants::$apiSDM_trig_key . '" : "' . Constants::$apiSDM_key . '" , "' .
            Constants::$tokenhit 		. '" : "" , "' .
            Constants::$username 		. '" : "' . $_POST['username'] . '" , "' .
            Constants::$password    	. '" : "' . md5($_POST['password'])
            . '" }';

        $result = json_decode(HttpClient::httpPostOnRaw($url, $body), true);
        
        $msgData = [
            'alert' => true,
            'title' => 'Gagal Masuk',
            'message' => 'Pengguna tidak terdaftar !!',
            'color' => 'danger'
        ];


        if (isset($result['status']) && !isset($result['NIP'])) {

            $msgData = [
                'alert' => true,
                'title' => 'Gagal Masuk',
                'message' => 'Username dan Katasandi tidak cocok !!',
                'color' => 'danger'
            ];

        } else if(isset($result['NIP'])){
            $firstName = explode(" ", $result['namalengkap'])[0];
            $lastName = str_replace($firstName , "", $result['namalengkap']);

            if(file_exists($result['foto'])){
                $photo = $result['foto'];
            }else{
                $photo = asset('assets/upload/user_photo_profile/default.png');
            }
            $email = isset($result['email']) ? $result['email'] : 'mail_'.$result['NIP'].'@monalisa.com';
            $sessionData = [
                'username'      => $_POST['username'],
                'password'      => $_POST['password'],
                'reg_number'    => $result['NIP'],
                'email'         => $email,
                'phone'         => '+62',
                'first_name'    => $firstName ,
                'last_name'     => $lastName,
                'age'           => 0,
                'gender'        => '',
                'job'           => $result['namabiro'],
                'address_street'=> 0,
                'province_id'   => 0,
                'district_id'   => 0,
                'regency_id'    => 0,
                'village_id'    => 0,
                'type'          => 2,
                'photo'         => $photo ,
                'provider_id'   => Constants::$BIMA_TOKEN,
                'provider'      => 'monalisa',
                'status'        => 1,
            ];
            
            $data = $sessionData;
            $data['password'] = bcrypt($data['password']);

            $user = User::create($data);
            $user->roles()
            ->attach(Role::where('name', 'Partisipan')->first());
            
            $msgData = [
                'alert' => true,
                'title' => 'Selamat Datang',
                'message' => 'Hai '.$firstName.', anda berada di zona administrator.',
                'color' => 'success'
            ];

            return $data;

        }
        
        return redirect()->back()->with($msgData);
    }
}