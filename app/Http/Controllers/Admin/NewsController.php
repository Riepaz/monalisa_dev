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
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\CertificateUser;
use App\Libraries\HttpClient;
use Config\Constants;
use Auth;

class NewsController extends Controller
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
    
    public function news()
    {
        return view('admin.news');
    }
    
    public function newsById($id)
    {
        return DB::table('news as a')
        ->join('news_category as b' , 'a.category_id' , '=' ,'b.id')
        ->where(['a.id' => $id])
        ->get();
    
    }

    public function newsCategories()
    {
        $categories = NewsCategory::all()->pluck('name' , 'id');
        return json_encode($categories);
    }
    
    public function getallnews(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $province_id = $request->province_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        
        $news = DB::table('news as a')
        ->select('a.*' , 'b.name as category',
        DB::raw('(select count(id) from news_views as c where c.news_id = a.id) as views')
        )
        ->join('news_category as b' , 'a.category_id' , '=' ,'b.id')
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
        ->paginate($request->length);

        $i = 1;
        $data = array();

        foreach($news as $item){
            
            $url = "#";
            $row = array();
    
            $row[] = $i++;
            $row[] = '<a style="color:#000" href="'.route('newsdetail' , $item->slug).'">
                        <p>'.$item->title.'</p>
                    </a>';
            $row[] = $item->category;
            $row[] = $item->views;

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
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="edit_news('."'".$item->id."'".')" title="Edit Berita" data-toggle="modal" data-target="#compose_news_modal"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="publish_news('."'".$item->id."'".' , '.$status.' )" title="'.$title.'"><i class="fas '.$activate.'" ></i></button>
            <button class="btn btn-sm btn-sm btn-outline-danger mb-1" onclick="delete_news('."'".$item->id."'".')" title="Hapus Berita"><i class="fas fa-trash"></i></button>
            ';
            
            $data[] = $row;
        }
        
            $output = array(
                "draw" => $request->draw,
                "recordsTotal" => $news->total(),
                "recordsFiltered" => $news->total(),
                "data" => $data,
            );
            
            echo json_encode($output);
    }

    public function submitNews(Request $request)
    {
        if(!isset($request->news_id)){
            $id = 'news_'.date('YmdHis');
        }else{
            $id = $request->news_id;
        }
        
        $request->photo_base64;
        $filename = $id.'.png';
        $encodedFile = $request->photo_base64;
        
        if($request->photo_base64 != 'true'){
            $cek = $this->imgUpload($request , $filename, $encodedFile);
        }
        
        $data = [
            'category_id' => $request->category,
            'slug' => Str::slug($request->news_title),
            'title' => $request->news_title,
            'overview' => $request->compose_overview_news,
            'banner' => $filename,
            'created_by' => Auth::user()->first_name." ".Auth::user()->last_name,
        ];

        if(!isset($request->news_id)){
            $data['id'] = $id;
            News::insert($data);
        }else{
            News::where(['id' => $id])->update($data);
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

    public function deleteNews(Request $request)
    {
        $filePath = Storage::disk('public')->path('/news/');
        unlink($filePath.$request->id.'.png');
        News::where(['id' => $request->id])->delete();
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