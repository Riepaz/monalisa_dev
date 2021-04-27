<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Config\Constants;
use App\Models\News;
use App\Models\Info;
use App\Models\NewsCategory;
use App\Models\ConfigModel;

use App\Libraries\HttpClient;

class NewsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function news()
    {   
        
        $callback['news'] = News::where(['is_active' => 1])->offset(0)->limit(5)->orderBy('publish_at' , 'desc')->get();   
        $callback['related_news'] = News::select(
            [
                'news.*' , 
                DB::raw('(select count(*) from news_views as view where view.news_id = news.id) as viws')
            ])
        ->where(['is_active' => 1])
        ->offset(0)
        ->limit(6)->orderBy('viws' , 'desc')
        ->get();   
        
        $callback['hot_news'] = News::select(
            [
                'news.*' , 
                DB::raw('(select count(*) from news_views as view where view.news_id = news.id) as viws')
            ])
        ->where(['is_active' => 1])
        ->offset(0)
        ->limit(8)->orderBy('viws' , 'desc')
        ->get();   
        
        $callback['tags'] = NewsCategory::all();   
        
        return view('news' , $callback);
    }
    
    public function newsAll($request)
    {   
        if(isset($request) && isset($_GET['page'])){
            
            $page = $_GET['page'];
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });

            $callback['tags'] = NewsCategory::all(); 
            
            if($request == 'live-news'){
                $callback['title'] = 'Berita Terbaru'; 
                $callback['news'] = News::where(['is_active' => 1])->orderBy('publish_at' , 'desc')->paginate(10);   
            }
            else if($request == 'hot-news'){
                $callback['title'] = 'Berita Terhangat'; 
                $callback['news'] = News::select(
                    [
                        'news.*' , 
                        DB::raw('(select count(*) from news_views as view where view.news_id = news.id) as viws')
                    ])
                ->where(['is_active' => 1])
                ->paginate(10);    
            }
            else
            {
                $callback['title'] = urldecode($request); 
                $callback['news'] = NewsCategory::where(['name' => urldecode($request)])
                ->rightjoin('news' , 'news_category.id' , '=' , 'news.category_id' )
                ->paginate(10);

                $callback['news']->total();

            }
        }else{
            return redirect()->route('news');   
        }
        
        return view('newsall' , $callback);
    }

    public function newsDetail($request)
    {   
        if(isset($request)){
            
            $callback['tags'] = NewsCategory::all(); 
            
            $callback['title'] = urldecode($request); 
            $callback['news'] = News::select(['news.id as news_id' ,'news.*' , 'news_category.id as category_id' , 'news_category.name'])->where(['slug' => $request])
                ->rightjoin('news_category' , 'news_category.id' , '=' , 'news.category_id' )
                ->get();

            if(sizeof($callback['news']) > 0){
                $callback['info'] = Info::limit(1)->orderBy('created_at' , 'desc')->get(); 
                
                $data = ['news_id' => $callback['news'][0]->news_id];
                DB::table('news_views')->insert($data);
                
                $callback['suggest'] = News::select(['news.slug' , 'news.title' , 'news.created_by'])
                    ->rightjoin('news_category' , 'news_category.id' , '=' , 'news.category_id' )
                    ->where('news.title' , 'like' ,  '%'.$callback['news'][0]->title.'%')
                    ->orwhere('news_category.name' , '=' ,  $callback['news'][0]->name)
                    ->orderBy('publish_at' , 'desc')
                    ->limit(15)
                    ->get();
            }else{
                $callback['suggest'] = [];
                $callback['info'] = [];
            }
            
        }else{
            return redirect()->route('news');   
        }
        
        return view('newsdetail' , $callback);
    }

    public function trainingProfile()
    {
        return view('underconstruction');
    }

    public function trainingStatistic()
    {
        return view('underconstruction');
    }

    public function trainingRecap()
    {
        return view('underconstruction');
    }
}