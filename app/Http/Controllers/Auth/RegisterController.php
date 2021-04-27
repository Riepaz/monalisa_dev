<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Role;
use Config\Constants;

use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use App\Libraries\HttpClient;

use Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(Request $request)
    {
        $data = $request->all();
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => 'required|string|max:255|unique:users',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'alpha_num'],
            'c_password' => ['required', 'string', 'min:6', 'alpha_num' , 'same:password'],
            'province_id' => ['required'],
            'regency_id' => ['required'],
            'district_id' => ['required'],
            'village_id' => ['required'],
        ]);
    }

    public function showRegisterForm()
    {
        $provinces = DB::table('provinces')->pluck('name', 'id');
        $type_user = DB::table('type_user')->pluck("name", "id");

        return view('auth.register', ['provinces' => $provinces, 'type_user' => $type_user, 'provider' => "web_monalisa", 'provider_id' => "1"]);
    }
    
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request);
        if ($validator->fails()) {
            
            $msgData = [
                'alert' => true,
                'title' => 'Gagal Mendaftar',
                'message' => 'Perikasa kembali data masukan anda.',
                'color' => 'danger'
            ];

            return redirect()->back()->with($msgData)->withErrors($validator->errors());  
        }

        $data = $request->all();
        $now = \Carbon\Carbon::now()->toDateTimeString();
        
        if(isset($data['job_free'])){
            $data['job'] = $data['job_free'];
        }

        if($data['status'] != "-" || $data['status'] != null){
            $data['job'] = null;
        }

        $data['email_verified_at'] = $now;
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'birth_place' => $data['birth_place'],
            'birth_date' => $data['birth_date'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'provider' => $data['provider'],
            'provider_id' => $data['provider_id'],
            'phone' => $data['phone'],
            'address_street' => $data['address_street'],
            'province_id' => $data['province_id'],
            'regency_id' => $data['regency_id'],
            'district_id' => $data['district_id'],
            'village_id' => $data['village_id'],
            'type' => $data['type'],
            'age' => $data['age'],
            'email_verified_at' => $data['email_verified_at'],
            'status' => $data['status'],
            'jenjang_pkb' => null,
            'gender' => $data['gender'],
            'job' => $data['job'],
            'reg_number' => $data['reg_number']
        ]);
        
        $user->roles()
        ->attach(Role::where('name', 'Partisipan')->first());
        
        $msgData = [
            'alert' => true,
            'title' => 'Anda Berhasil Terdaftar',
            'message' => 'Hai '.$data['first_name'].', anda berhasil terdaftar, silahkan lakukan login.',
            'color' => 'success'
        ];

        return redirect()->route('registerform')->with($msgData);
    }

    public function getTypeUser()
    {
        $type_user = DB::table("type_user")->get()->pluck("name", "id");
        return json_encode($type_user);
    }

    public function getStatusUser()
    {
        $status_user = DB::table("status_user")->where('type','BKKBN')->get()->pluck("name", "id");
        return json_encode($status_user);
    }

    public function getProfesi($typeID)
    {
        $profesi = DB::table("status_user")->where('type',$typeID)->get()->pluck("name", "id");
        return json_encode($profesi);
    }

}