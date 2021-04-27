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
use App\Models\FtIndicator;
use App\Libraries\HttpClient;
use Illuminate\Support\Facades\Validator;
use Config\Constants;
use Auth;

class FormtwoIndicatorScoreController extends Controller
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
    
    public function ftIndicator()
    {
        return view('admin.ftindicator');
    }
    
    public function ftIndicatorById($id)
    {
        return FtIndicator::where(['id' => $id])
        ->get();
    
    }
    
    public function getallFtIndicator()
    {
        
        $ftIndicator = FtIndicator::all();

        $i = 1;
        $data = array();

        foreach($ftIndicator as $item){
            
            $url = "#";
            $row = array();
    
            $row[] = $i++;
            $row[] = $item->code;
            $row[] = $item->name;

            //add html for action
            $row[] = '
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="edit_ftindicator('."'".$item->id."'".')" title="Edit Kontrasepsi" data-toggle="modal" data-target="#compose_ftindicator_modal"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-sm btn-outline-danger mb-1" onclick="delete_ftindicator('."'".$item->id."'".')" title="Hapus Kontrasepsi"><i class="fas fa-trash"></i></button>
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
            'code' => 'required|string|max:255|unique:formtwo_session_wi_indicator'
        ]);
    }

    public function submitFtIndicator(Request $request)
    { 
        $data = [
            'name' => $request->ftindicator_name,
            'code' => $request->ftindicator_code,
        ];

        if(!isset($request->ftindicator_id)){
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
            
            FtIndicator::insert($data);
        }else{
            FtIndicator::where(['id' => $request->ftindicator_id])->update($data);
        }
    }

    public function deleteFtIndicator(Request $request)
    {
        FtIndicator::where(['id' => $request->id])->delete();
    }

    
}