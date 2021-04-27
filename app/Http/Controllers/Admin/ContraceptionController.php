<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Contraception;
use App\Libraries\HttpClient;
use Illuminate\Support\Facades\Validator;
use Config\Constants;
use Auth;

class ContraceptionController extends Controller
{
    
    use AuthenticatesUsers;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    public function contraception()
    {
        return view('admin.contraception');
    }
    
    public function contraceptionById($id)
    {
        return Contraception::where(['id' => $id])
        ->get();
    
    }
    
    public function getallPublicContraceptionOption()
    {
        return Contraception::pluck("name", "code");
    }

    public function getallContraceptionOption($fiscaly_id)
    {
        return Contraception::pluck("name", "id");
    }

    public function getallContraceptions()
    {
        
        $contraception = Contraception::all();

        $i = 1;
        $data = array();

        foreach($contraception as $item){
            
            $url = "#";
            $row = array();
    
            $row[] = $i++;
            $row[] = $item->code;
            $row[] = $item->name;

            //add html for action
            $row[] = '
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="edit_contraception('."'".$item->id."'".')" title="Edit Kontrasepsi" data-toggle="modal" data-target="#compose_contraception_modal"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-sm btn-outline-danger mb-1" onclick="delete_contraception('."'".$item->id."'".')" title="Hapus Kontrasepsi"><i class="fas fa-trash"></i></button>
            ';
            
            $data[] = $row;
        }
        
            $output = array(
                "data" => $data,
            );
            
            echo json_encode($output);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'code' => 'required|string|max:255|unique:contraception'
        ]);
    }

    public function submitContraception(Request $request)
    { 
        $data = [
            'name' => $request->contraception_name,
            'code' => $request->contraception_code,
            'spec' => 1,
        ];

        if(!isset($request->contraception_id)){
            $validator = $this->validator($data);
            if ($validator->fails()) {
                
                $msgData = [
                    'alert' => true,
                    'title' => 'Gagal Memasukan Data',
                    'message' => 'Kode Sudah Digunakan.',
                    'color' => 'danger'
                ];
    
                return json_encode(['error' => $validator->errors()]);  
            }
            
            Contraception::insert($data);
        }else{
            Contraception::where(['id' => $request->contraception_id])->update($data);
        }
    }

    public function deleteContraception(Request $request)
    {
        Contraception::where(['id' => $request->id])->delete();
    }

    
}