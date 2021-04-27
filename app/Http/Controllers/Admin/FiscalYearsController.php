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
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\CertificateUser;
use App\Libraries\HttpClient;
use Config\Constants;
use Auth;

class FiscalYearsController extends Controller
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
    
    public function fiscalYears()
    {
        return view('admin.fiscalyears');
    }
    
    public function fiscalYById($id)
    {
        return DB::table('fiscal_years')
        ->where(['id' => $id])
        ->get();
    
    }
    
    public function getallFiscaYearsOption()
    {
        return DB::table('fiscal_years')
        ->orderBy('name' , 'desc')
        ->pluck("name", "id");
    }
    
    public function getallFiscaYears()
    {
        
        $fiscalYears = DB::table('fiscal_years as a')
        ->orderBy('a.name' , 'desc')
        ->get();

        $i = 1;
        $data = array();

        foreach($fiscalYears as $item){
            
            $url = "#";
            $row = array();
            
            $totalf1 = 0;

            $row[] = $i++;
            $row[] = $item->name;
            
            
            $row[] = '<button class="btn btn-sm btn-sm btn-outline-info m-1" onclick="edit_fiscalY('."'".$item->id."'".')" title="Edit Tahun" data-toggle="modal" data-target="#compose_fiscalY_modal"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-sm btn-outline-danger m-1" onclick="deleteFiscalY('."'".$item->id."'".')" title="Hapus Tahun"><i class="fas fa-trash"></i></button>';
            
            $data[] = $row;
        }
        
            $output = array(
                "data" => $data,
            );
            
            echo json_encode($output);
    }

    public function submitYear(Request $request)
    {
        
        $data = [
            'name' => $request->year_name
        ];

        if(!isset($request->fiscalY_id)){
            DB::table('fiscal_years')->insert($data);
        }else{
            DB::table('fiscal_years')->where(['id' => $request->fiscalY_id])->update($data);
        }
    }

    public function activateNews(Request $request)
    {
        $data = [
            'is_active' => $request->status,
            'publish_at' => date('Y-m-d H:i:s')
        ];
        
        News::where(['id' => $request->id])->update($data);
    }

    public function deleteFiscalYears(Request $request)
    {
        DB::table('fiscal_years')->where(['id' => $request->id])->delete();
    }
    
}