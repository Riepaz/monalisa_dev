<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Category;
use App\Models\TrainingType;
use App\Libraries\HttpClient;
use Config\Constants;
use Auth;

class UsersManagementController extends Controller
{
    
    use AuthenticatesUsers;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
    }
    
    public function users()
    {
        return view('admin.users');
    }
    
    public function usernameValidation(Request $request)
    {
        return User::where(['username' => $request->username])
        ->first() ? 0 : 1;
    
    }
 
    public function emailValidation(Request $request)
    {
        return User::where(['email' => $request->email])
        ->first() ? 0 : 1;
    
    }

    public function userById($id)
    {
        return User::select(['users.*', 'role_user.role_id'])
        ->leftjoin('role_user' , 'users.id' , '=' , 'role_user.user_id')
        ->where(['users.id' => $id])
        ->get();
    
    }

    public function userParticipantById(Request $request)
    {
        return User::select(
            ['users.*', 
            'role_user.role_id',
            'provinces.name as province',
            'regencies.name as regency',
            'districts.name as district',
            'villages.name as village',

            'type_user.name as type',
            'status_user.name as status',
            'courses_offline_register.is_verified as is_verified',
            'participant_categories.name as participant_category',
            
            ])

        ->leftjoin('role_user' , 'users.id' , '=' , 'role_user.user_id')
        ->leftjoin('provinces' , 'users.province_id' , '=' , 'provinces.id')
        ->leftjoin('regencies' , 'users.regency_id' , '=' , 'regencies.id')
        ->leftjoin('districts' , 'users.district_id' , '=' , 'districts.id')
        ->leftjoin('villages' , 'users.village_id' , '=' , 'villages.id')
        ->leftjoin('type_user' , 'users.type' , '=' , 'type_user.id')
        ->leftjoin('status_user' , 'users.status' , '=' , 'status_user.id')
        ->leftjoin('courses_offline_register' , 'users.id' , '=' , 'courses_offline_register.user_id')
        ->leftjoin('participant_categories' , 'participant_categories.id' , '=' , 'courses_offline_register.participant_cat_id')
        ->where(['users.id' => $request->user_id , 'courses_offline_register.formtwo_id' => $request->formtwo_id])
        ->get();
    
    }
    
    public function getallUsers(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $province_id = $request->province_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $datePattern = "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/"; 
        
        $trainingType = User::select(['users.*' , 'roles.name as role' , 'provinces.name as provinces' , 'regencies.name as regency'])
        ->leftjoin('provinces' , 'provinces.id' , '=' , 'users.province_id')
        ->leftjoin('regencies' , 'regencies.id' , '=' , 'users.regency_id')
        ->leftjoin('role_user' , 'role_user.user_id' , '=' , 'users.id')
        ->leftjoin('roles' , 'role_user.role_id' , '=' , 'roles.id')
        ->where(function($query){
            if(Auth::user()->hasRole('adminprovinsi')){
                return $query
                ->where(['provinces.id' => Auth::user()->province_id])
                ->where('roles.name' , '!=' , 'superadmin');
            }

            return null;
        })
        ->where(function($query) use($search , $datePattern) {
            if(isset($search)){
                return $query
                ->where('users.first_name' , 'like' ,'%'.$search.'%')
                ->orwhere('users.last_name' , 'like' ,'%'.$search.'%')
                ->orwhere('users.reg_number' , 'like' ,'%'.$search.'%')
                ->orwhere('users.birth_date' , 'like' , (preg_match($datePattern,$search) ? date("Y-m-d",strtotime($search))."%" : ''))
                ->orwhere('provinces.name' , 'like' ,'%'.$search.'%')
                ->orwhere('regencies.name' , 'like' ,'%'.$search.'%')
                ;
            }
            return null;
        })
        ->where(['is_deleted' => 0])
        ->orwhere(['is_deleted' => null])
        ->paginate($request->length);

        $i = 1;
        $data = array();

        foreach($trainingType as $item){
            
            $url = "#";
            $row = array();
    
            $row[] = '<small>'.$i++.'</small>';
            $row[] = $item->reg_number != null && $item->reg_number != '' ? '<small>'.$item->reg_number.'</small>' :'<small><i>NIP Tidak Ada</i></small>';
            $row[] = '<small>'.$item->first_name.' '.$item->last_name.'</small>';
            $row[] = '<small>'.ucfirst(strtolower($item->provinces)).'</small>';
            $row[] = '<small>'.ucfirst(strtolower($item->regency)).'</small>';
            $row[] = '<small><span class="badge badge-secondary m-1 p-2">'.$item->role.'<span></small>';

            if($item->is_active){
                $row[] = '<small><span class="badge badge-success m-1 p-2">Aktif</span></small>';
                $activate = 'fa-remove';
                $title = 'Non-Aktifkan';
                $status = 0;
            }else{
                $row[] = '<small><span class="badge badge-danger m-1 p-2">Non-Aktif</span></small>';
                $activate = 'fa-paper-plane';
                $title = 'Aktifkan';
                $status = 1;
            } 

            if($item->role != 'SuperAdmin' && $item->id != Auth::user()->id){
                $additional = '
                <button class="btn btn-sm btn-sm btn-outline-danger mb-1" onclick="delete_user('."'".$item->id."'".' )" title="Hapus Pengguna"><i class="fas fa-trash"></i></button>';
            }else{
                $additional = '';
            }

            //add html for action
            $row[] = '
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="edit_user('."'".$item->id."'".')" title="Atur Pengguna" data-toggle="modal" data-target="#compose_user_modal"><i class="fas fa-user-cog"></i></button>
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="reset_password('."'".$item->id."'".')" title="Reset Password"><i class="fas fa-key"></i></button>
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="publish_user('."'".$item->id."'".' , '.$status.' )" title="'.$title.'"><i class="fas '.$activate.'" ></i></button>
            '.$additional;
            
            $data[] = $row;
        }
        
            $output = array(
                "draw" => $request->draw,
                "recordsTotal" => $trainingType->total(),
                "recordsFiltered" => $trainingType->total(),
                "data" => $data,
            );
            
            echo json_encode($output);
    }
    
    public function getallPublicUsersRegister(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $formtwo_id = $request->formtwo_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $datePattern = "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/"; 

        $depdagri_code = DB::table('pusdiklat')
        ->select(['depdagri_code'])
        ->where(['id'=>$request->pusdiklat_id])
        ->first();
        
        $trainingType = User::select([
            'users.*' , 
            'roles.name as role' , 
            'provinces.name as province',
            DB::raw('(select count(*) from courses_offline_register where courses_offline_register.formtwo_id = '.$formtwo_id.' 
            and courses_offline_register.user_id = users.id) as registered')
            ])
        ->leftjoin('role_user' , 'role_user.user_id' , '=' , 'users.id')
        ->leftjoin('roles' , 'role_user.role_id' , '=' , 'roles.id')
        ->rightjoin('provinces' , 'users.province_id' , '=' , 'provinces.id')
        ->where(['users.is_deleted' => 0 , 'users.is_active' => 1 ])
        ->where('roles.name' , '!=' , 'superadmin')
        ->where('roles.name' , '!=' , 'admin')
        ->where('roles.name' , '!=' , 'adminprovinsi')
        ->where(['users.type' => 2])
        ->where(function($query) use($search , $datePattern) {
            if(isset($search)){
                return $query
                ->where('users.first_name' , 'like' ,'%'.$search.'%')
                ->orwhere('users.last_name' , 'like' ,'%'.$search.'%')
                ->orwhere('users.reg_number' , 'like' ,'%'.$search.'%')
                ->orwhere('users.birth_date' , 'like' , (preg_match($datePattern,$search) ? date("Y-m-d",strtotime($search))."%" : ''))
                ->orwhere('provinces.name' , 'like' ,'%'.$search.'%')
                ;
            }
            return null;
        })
        ->orderby(DB::raw('FIELD(users.province_id , '.Auth::user()->province_id.')'))
        ->paginate($request->length);

        $i = 1;
        $data = array();

        foreach($trainingType as $item){
            
            $url = "#";
            $row = array();
            $name = $item->first_name.' '.$item->last_name;

            $row[] = $i++;
            $row[] = $item->reg_number != null && $item->reg_number != ''  ? $item->reg_number : '<i>NIP tidak ada</i>';
            $row[] = $name;
            $row[] = $item->province ?? '<i>Alamat Provinsi Belum diatur</i>';
            $row[] = $item->address ?? '<i>Alamat belum diatur</i>';



            if($item->registered){
                $row[] = '<span class="badge badge-success m-1 p-2">Terdaftar</span>';
                $activate = 'fa-remove';
                $title = 'Non-Aktifkan';
                $status = 0;
                
                $row[] = '
                <button class="btn btn-sm btn-sm btn-success mb-1" title="Terdaftar"><i class="fas fa-check"></i></button>
                ';
            }else{
                $row[] = '<span class="badge badge-info m-1 p-2">Tidak Terdaftar</span>';
                $activate = 'fa-paper-plane';
                $title = 'Aktifkan';
                $status = 1;
                
                $row[] = '
                <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="choose_user('."'".$item->id."' , '".$name."'".')" title="Pilih Pengguna">Pilih</button>
                ';
            } 

            //add html for action

            $data[] = $row;
        }
        
            $output = array(
                "draw" => $request->draw,
                "recordsTotal" => $trainingType->total(),
                "recordsFiltered" => $trainingType->total(),
                "data" => $data,
            );
            
            echo json_encode($output);
    }

    public function getallPublicUsers(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $province_id = $request->province_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $datePattern = "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/"; 

        $depdagri_code = DB::table('pusdiklat')
        ->select(['depdagri_code'])
        ->where(['id'=>$request->pusdiklat_id])
        ->first();
        
        $trainingType = User::select(['users.*' , 'roles.name as role' , 'provinces.name as province'])
        ->leftjoin('role_user' , 'role_user.user_id' , '=' , 'users.id')
        ->leftjoin('roles' , 'role_user.role_id' , '=' , 'roles.id')
        ->rightjoin('provinces' , 'users.province_id' , '=' , 'provinces.id')
        ->where(['users.is_deleted' => 0 , 'users.is_active' => 1 ])
        ->where('roles.name' , '!=' , 'superadmin')
        ->where('roles.name' , '!=' , 'admin')
        ->where('roles.name' , '!=' , 'adminprovinsi')
        ->where(['users.type' => 2])
        ->where(function($query) use($search , $datePattern) {
            if(isset($search)){
                return $query
                ->where('users.first_name' , 'like' ,'%'.$search.'%')
                ->orwhere('users.last_name' , 'like' ,'%'.$search.'%')
                ->orwhere('users.reg_number' , 'like' ,'%'.$search.'%')
                ->orwhere('users.birth_date' , 'like' , (preg_match($datePattern,$search) ? date("Y-m-d",strtotime($search))."%" : ''))
                ->orwhere('provinces.name' , 'like' ,'%'.$search.'%')
                ;
            }
            return null;
        })
        ->orderby(DB::raw('FIELD(users.province_id , '.Auth::user()->province_id.')'))
        ->paginate($request->length);

        $i = 1;
        $data = array();

        foreach($trainingType as $item){
            
            $url = "#";
            $row = array();
            $name = $item->first_name.' '.$item->last_name;

            $row[] = $i++;
            $row[] = $item->reg_number != null && $item->reg_number != ''  ? $item->reg_number : '<i>NIP tidak ada</i>';
            $row[] = $name;
            $row[] = $item->province ?? '<i>Alamat Provinsi Belum diatur</i>';
            $row[] = $item->address ?? '<i>Alamat belum diatur</i>';



            if($item->is_active){
                $row[] = '<span class="badge badge-success m-1 p-2">Aktif</span>';
                $activate = 'fa-remove';
                $title = 'Non-Aktifkan';
                $status = 0;
            }else{
                $row[] = '<span class="badge badge-danger m-1 p-2">Non-Aktif</span>';
                $activate = 'fa-paper-plane';
                $title = 'Aktifkan';
                $status = 1;
            } 

            //add html for action
            $row[] = '
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="choose_user('."'".$item->id."' , '".$name."'".')" title="Pilih Pengguna">Pilih</button>
            ';
            
            $data[] = $row;
        }
        
            $output = array(
                "draw" => $request->draw,
                "recordsTotal" => $trainingType->total(),
                "recordsFiltered" => $trainingType->total(),
                "data" => $data,
            );
            
            echo json_encode($output);
    }

    public function submitModUser(Request $request)
    {
        $data = [
            'role_id' => $request->role_id,
        ];

        if(!isset($request->mod_user_id)){
            RoleUser::insert($data);
        }else{
            RoleUser::where(['user_id' => $request->mod_user_id])->update($data);
        }
        return $data;
    }

    public function activateUser(Request $request)
    {
        $data = [
            'is_active' => $request->status
        ];
        
        $user = User::where(['id' => $request->id])->first();
        User::where(['id' => $request->id])->update($data);

        $body = [
            'email' => $user['email'],
            'is_active' => $request->status
        ];

        return $data;
    }

    public function resetUser(Request $request)
    {
        $user = User::where(['id' => $request->id])->first();
        $data = [
            'password' => bcrypt($user['username'])
        ];
        $user->update($data);


    }

    public function deleteUser(Request $request)
    {
        $data = [
            'is_deleted' => 1
        ];
        
        User::where(['id' => $request->id])->update($data);
    }
    
}