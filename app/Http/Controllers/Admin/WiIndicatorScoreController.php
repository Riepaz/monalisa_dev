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
use App\Models\WiIndicator;
use App\Libraries\HttpClient;
use Illuminate\Support\Facades\Validator;
use Config\Constants;
use Auth;

class WiIndicatorScoreController extends Controller
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
    
    public function wiIndicator()
    {
        return view('admin.wiindicator');
    }
    
    public function wiIndicatorById($id)
    {
        return WiIndicator::where(['id' => $id])
        ->get();
    
    }
    
    public function getallWiIndicator()
    {
        
        $wiIndicator = WiIndicator::all();

        $i = 1;
        $data = array();

        foreach($wiIndicator as $item){
            
            $url = "#";
            $row = array();
    
            $row[] = $i++;
            $row[] = $item->code;
            $row[] = $item->name;

            //add html for action
            $row[] = '
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="edit_wiindicator('."'".$item->id."'".')" title="Edit Kontrasepsi" data-toggle="modal" data-target="#compose_wiindicator_modal"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-sm btn-outline-danger mb-1" onclick="delete_wiindicator('."'".$item->id."'".')" title="Hapus Kontrasepsi"><i class="fas fa-trash"></i></button>
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

    public function submitWiIndicator(Request $request)
    { 
        $data = [
            'name' => $request->wiindicator_name,
            'code' => $request->wiindicator_code,
        ];

        if(!isset($request->wiindicator_id)){
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
            
            WiIndicator::insert($data);
        }else{
            WiIndicator::where(['id' => $request->wiindicator_id])->update($data);
        }
    }

    public function deleteWiIndicator(Request $request)
    {
        WiIndicator::where(['id' => $request->id])->delete();
    }

    
}