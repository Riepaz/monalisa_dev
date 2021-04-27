<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use Config\Constants;
use App\Http\Controllers\Controller;
use App\Libraries\Currency;
use App\Libraries\Date;
use App\Libraries\Dateduration;
use App\Libraries\Encryption;
use App\Libraries\HttpClient;

use App\Models\CertificateUser;
use App\Models\ConfigModel;
use App\Models\User;
use Auth;

class Configuration extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $course_ROLE = 1;
    

    public function __construct()
    {
        //$this->middleware('auth');

        date_default_timezone_set('Asia/Jakarta');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function configuration()
    {
            $prequisite['bima_total_course'] = "";

            $prequisite['provinces'] = DB::table('provinces')->pluck('name', 'id');
            $prequisite['type_user'] = DB::table('type_user')->pluck("name", "id");

            return view('configuration' ,  $prequisite);
    }


    protected function validator(Request $request)
    {
        $data = $request->all();
        return Validator::make($data, [
            'first_name' => ['string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'email' => ['email', 'max:255'],
            'phone' => ['numeric' , 'digits_between:6,16']
        ]);
    }

    protected function accountValidator(Request $request)
    {
        $data = $request->all();
        return Validator::make($data, [
            'password' => ['required', 'string', 'min:6', 'alpha_num'],
            'c_password' => ['required', 'string', 'min:6', 'alpha_num' , 'same:password'],
        ]);
    }

    public function updateUser(Request $request){
        $validator = $this->validator($request);
        if ($validator->fails()) {
            
            $msgData = [
                'alert' => true,
                'title' => 'Perubahan Gagal',
                'message' => 'Perikasa kembali data masukan anda.',
                'color' => 'danger'
            ];

            return redirect()->back()->with($msgData)->withErrors($validator->errors());  
        }
        
        $data = $request->all();
        
        if(isset($data['job_free'])){
            if($data['job_free'] == "" || $data['job_free'] == null){
                $data['job'] = isset($data['job_options']) ? $data['job_options'] : '';
            }else{
                $data['job'] = $data['job_free'];
            }
        }

        if(!isset($data['last_name'])){
            $data['last_name'] = "";
        }
        
        if(!isset($data['job'])){
            $data['job'] = "";
        }

        $user = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'age' => $data['age'],
            'gender' => $data['gender'],
            'job' => $data['job'],
            'address_street' => $data['address_street'],
            'reg_number' => isset($data['reg_number'])?$data['reg_number']:"",

        ];

        if(isset($data['province_id'])){
            $user['province_id'] = $data['province_id'];
            $user['regency_id'] = $data['regency_id'];
            $user['district_id'] = $data['district_id'];
            $user['village_id'] = $data['village_id'];
        }

        User::where(['username'  => $request->username])->update($user);
        
        $msgData = [
            'alert' => true,
            'title' => 'Perubahan Berhasil',
            'message' => 'Anda berhasil melakukan perubahan pada data anda.',
            'color' => 'success'
        ];
        
        return redirect()->route('panel.config')->with($msgData);
    }
    
    public function updateAccount(Request $request){
        $validator = $this->accountValidator($request);
        if ($validator->fails()) {
            
            $msgData = [
                'alert' => true,
                'title' => 'Perubahan Gagal',
                'message' => 'Perikasa kembali data masukan anda.',
                'color' => 'danger'
            ];

            return redirect()->back()->with($msgData)->withErrors($validator->errors());  
        }
        
        $data = $request->all();

        $user = [
            'password' => bcrypt($data['password']),
        ];

        User::where(['username'  => $request->username])->update($user);
    
        $msgData = [
            'alert' => true,
            'title' => 'Perubahan Berhasil',
            'message' => 'Anda berhasil melakukan perubahan pada data anda.',
            'color' => 'success'
        ];
        
        return redirect()->route('panel.config')->with($msgData);
    }

    public function Apperrances()
    {
            $prequisite['yt_display'] = ConfigModel::select('value')->where(['parameter' => 'yt_display'])->first();

            return view('apperrance' ,  $prequisite);
    }

    public function updateYTvideo(Request $request)
    {
        if(strpos($request->ytlinkvideo, 'youtube') || strpos($request->ytlinkvideo, 'youtu.be')){
            if(strpos($request->ytlinkvideo, 'embed')){
                ConfigModel::where(['parameter' => 'yt_display'])
                ->update([ 'value' => $request->ytlinkvideo]);
            }else{
                return redirect()
                ->back()
                ->withErrors(['error' => 'Isikan Link Embed youtube']);
            }
        }else{
            return redirect()
            ->back()
            ->withErrors(['error' => 'Link bukan youtube']);
        }

        $prequisite['yt_display'] = ConfigModel::select('value')->where(['parameter' => 'yt_display'])->first();
        return view('apperrance' ,  $prequisite);
    }
}