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
use App\Models\Category;
use App\Libraries\HttpClient;
use Config\Constants;
use Auth;

class CategoriesController extends Controller
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
    
    public function categories()
    {
        return view('admin.categories');
    }
    
    public function categoryById($id)
    {
        return Category::where(['id' => $id])
        ->get();
    
    }
    
    public function getallCategoryOption()
    {
        return Category::
        where(['status' => '1' , 'deleted' => 0])
        ->orderBy('mekop' , 'desc')
        ->pluck("mekop", "id");
    }

    public function getallCategories()
    {
        
        $categories = Category::select(['kategori_mekop.*' , 'b.name as year'])
        ->leftjoin('fiscal_years as b' , 'b.id' , '=' , 'kategori_mekop.tahun_id')
        ->where(['deleted' => 0])
        ->get();

        $i = 1;
        $data = array();

        foreach($categories as $item){
            
            $url = "#";
            $row = array();
    
            $row[] = $i++;
            $row[] = $item->mekop;
            $row[] = $item->year;

            if($item->tingkat == 3 ){
                $row[] = '<span class="badge badge-info p-2">Kecamatan<span>';
            }else if($item->tingkat == 4 ){
                $row[] = '<span class="badge badge-info p-2">Desa & Kelurahan<span>';
            } 

            if($item->status){
                $row[] = '<span class="badge badge-success p-2">Aktif<span>';
                $activate = 'fa-remove';
                $title = 'Non-Aktifkan';
                $status = 0;
            }else{
                $row[] = '<span class="badge badge-danger p-2">Tidak Aktif<span>';
                $activate = 'fa-paper-plane';
                $title = 'Aktifkan';
                $status = 1;
            } 

            $row[] = '
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="edit_category('."'".$item->id."'".')" title="Edit Kategori" data-toggle="modal" data-target="#compose_category_modal"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="publish_category('."'".$item->id."'".' , '.$status.' )" title="'.$title.'"><i class="fas '.$activate.'" ></i></button>
            <button class="btn btn-sm btn-sm btn-outline-danger mb-1" onclick="delete_category('."'".$item->id."'".')" title="Hapus Kategori"><i class="fas fa-trash"></i></button>
            ';
            
            $data[] = $row;
        }
        
            $output = array(
                "data" => $data,
            );
            
            echo json_encode($output);
    }

    public function submitCategory(Request $request)
    {
        $data = [
            'mekop' => $request->category_names,
            'tahun_id' => $request->fiscaly_id,
            'tingkat' => $request->level_id,
            'status' => 1,
        ];

        if(!isset($request->category_id)){
            Category::insert($data);
        }else{
            $category = Category::where(['id' => $request->category_id])->first();
            $category->update($data);
        }
    }

    public function activateCategory(Request $request)
    {
        $data = [
            'status' => $request->status
        ];
        
        $category = Category::where(['id' => $request->id])->first();
        $category->update($data);
    }

    public function deleteCategory(Request $request)
    {
        $category = Category::where(['id' => $request->id])->first();
        $category->update(['deleted' => 1]);
    }

    public function imgUpload($request , $filename, $encodedFile ){
        $filePath = Storage::disk('public')->path('/news/');
        
        if($encodedFile != ''){
            
            $dataImage = file_get_contents($encodedFile);
            $imageSource = $filePath. $filename;

            return file_put_contents($imageSource, $dataImage);
        }else{
            return false;
        }

	}
    
}