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
use App\Models\info;
use App\Models\InfoCategory;
use App\Models\CertificateUser;
use App\Libraries\HttpClient;
use Config\Constants;
use Auth;

class InfoController extends Controller
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
    
    public function info()
    {
        return view('admin.info');
    }
    
    public function infoById($id)
    {
        return DB::table('info as a')
        ->join('info_category as b' , 'a.category_id' , '=' ,'b.id')
        ->where(['a.id' => $id , 'is_deleted' => 0])
        ->get();
    
    }

    public function infoCategories()
    {
        $categories = InfoCategory::all()->pluck('name' , 'id');
        return json_encode($categories);
    }
    
    public function getallinfo(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $province_id = $request->province_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        
        $info = DB::table('info as a')->select('a.*' , 'b.name as category', 'c.name as province',
        DB::raw('(select count(id) from info_views as c where c.info_id = a.id) as views'))
        ->join('info_category as b' , 'a.category_id' , '=' ,'b.id')
        ->join('provinces as c' , 'a.province_id' , '=' ,'c.id')
        ->where(function($query) {
            if(Auth::user()->hasRole('adminprovinsi')){
                return $query->where('a.province_id' , '=' , Auth::user()->province_id);
            }
            return null;
        })
        ->where(function($query) use($search) {
            if(isset($search)){
                return $query
                ->where('a.title' , 'like' , '%'.$search.'%')
                ->orwhere('a.overview' , 'like' , '%'.$search.'%')
                ->orwhere('b.name' , 'like' , '%'.$search.'%')
                ;
            }
            return null;
        })
        ->where(['is_deleted' => 0])
        ->paginate($request->length);

        
        $i = 1;
        $data = array();

        foreach($info as $item){
            
            $url = "#";
            $row = array();
    
            $row[] = $i++;
            $row[] = $item->title;
            $row[] = $item->category;
            
            if(Auth::user()->hasRole('adminprovinsi')){
                $row[] = $item->views;
            }else if(Auth::user()->hasRole('superadmin') or Auth::user()->hasRole('admin')){
                $row[] = $item->province;
            }

            if($item->is_active){
                $row[] = '<span class="badge badge-success p-2">Aktif<span>';
                $activate = 'fa-remove';
                $title = 'Non-Aktifkan';
                $status = 0;
            }else{
                $row[] = '<span class="badge badge-danger p-2">Non-Aktif<span>';
                $activate = 'fa-paper-plane';
                $title = 'Aktifkan';
                $status = 1;
            } 

            //add html for action
            $row[] = '
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="edit_info('."'".$item->id."'".')" title="Edit Berita" data-toggle="modal" data-target="#compose_info_modal"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="publish_info('."'".$item->id."'".' , '.$status.' )" title="'.$title.'"><i class="fas '.$activate.'" ></i></button>
            <button class="btn btn-sm btn-sm btn-outline-danger mb-1" onclick="delete_info('."'".$item->id."'".')" title="Hapus Berita"><i class="fas fa-trash"></i></button>
            ';
            
            $data[] = $row;
        }
        
            $output = array(
                "draw" => $request->draw,
                "recordsTotal" => $info->total(),
                "recordsFiltered" => $info->total(),
                "data" => $data,
            );
            
            echo json_encode($output);
    }

    public function submitinfo(Request $request)
    {
        if(!isset($request->info_id)){
            $id = 'info_'.date('YmdHis');
        }else{
            $id = $request->info_id;
        }

        $data = [
            'category_id' => $request->category,
            'slug' => Str::slug($request->info_title),
            'title' => $request->info_title,
            'overview' => $request->compose_overview_info,
            'created_by' => Auth::user()->first_name." ".Auth::user()->last_name,
        ];

        if(Auth::user()->hasRole('adminprovinsi')){
            $data['province_id'] = Auth::user()->province_id;
        }else{
            $data['province_id'] = $request->province_id;
        }

        if(!isset($request->info_id)){
            $data['id'] = $id;
            Info::insert($data);
        }else{
            Info::where(['id' => $id])->update($data);
        }

    }

    public function activateinfo(Request $request)
    {
        $data = [
            'is_active' => $request->status,
            'publish_at' => date('Y-m-d H:i:s')
        ];
        
        Info::where(['id' => $request->id])->update($data);
    }

    public function deleteinfo(Request $request)
    {
        /* $filePath = Storage::disk('public')->path('/info/');
        unlink($filePath.$request->id.'.png');
        Info::where(['id' => $request->id])->delete(); */

        Info::where(['id' => $request->id])->update(['is_deleted' => 1]);
    }
    
}