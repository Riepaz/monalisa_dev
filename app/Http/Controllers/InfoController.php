<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Config\Constants;
use App\Models\Info;
use App\Models\InfoCategory;
use App\Models\ConfigModel;

use App\Libraries\HttpClient;

class InfoController extends Controller
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
    public function info()
    {   
        
        $callback['info'] = Info::where(['is_active' => 1])->offset(0)->limit(5)->orderBy('publish_at' , 'desc')->get();   
        $callback['hot_info'] = Info::select(
            [
                'info.*' , 
                DB::raw('(select count(*) from info_views as view where view.info_id = info.id) as viws')
            ])
        ->where(['is_active' => 1])
        ->offset(0)
        ->limit(7)->orderBy('viws' , 'desc')
        ->get();   
        
        $callback['tags'] = InfoCategory::all();   
        
        return view('info' , $callback);
    }
    
    public function infoAll($request)
    {   
        if(isset($request) && isset($_GET['page'])){
            
            $page = $_GET['page'];
            $request = urldecode($request);
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });

            $callback['tags'] = InfoCategory::all(); 
            
            if(isset($_GET['province_id'])){
                $provinces = DB::table('provinces')->where(['id' => $_GET['province_id']])->first();
            }
            
            $callback['title'] = "Informasi ".($request != 'all-info' ? $request : '') ." ".ucfirst(strtolower($provinces->name ?? '')); 
            $callback['info'] = InfoCategory::rightjoin('info' , 'info_category.id' , '=' , 'info.category_id' )
                ->where(function($query) use($request){
                    if($request != 'all-info'){
                        return $query->where(['name' => $request]);
                    }
                    return null;
                })
                ->where(function($query){
                    if(isset($_GET['province_id'])){
                        return $query->where(['province_id' => $_GET['province_id']]);
                    }
                    return null;
                })
                ->paginate(10);

                $callback['info']->total();
        }else{
            return redirect()->route('infoall' , ['id' => 'all-info', 'page' => 1]);   
        }
        
        return view('infoall' , $callback);
    }

    public function infoDetail($request)
    {   
        if(isset($request)){
            
            $callback['tags'] = InfoCategory::all(); 
            $callback['info'] = Info::limit(1)->orderBy('created_at' , 'desc')->get(); 
            
            $callback['title'] = urldecode($request); 
            $callback['info'] = Info::select(['info.id as info_id' ,'info.*' , 'info_category.id as category_id' , 'info_category.name'])->where(['slug' => $request])
                ->rightjoin('info_category' , 'info_category.id' , '=' , 'info.category_id' )
                ->get();

            $data = ['info_id' => $callback['info'][0]->info_id];
            DB::table('info_views')->insert($data);
            
            $callback['suggest'] = Info::select(['info.slug' , 'info.title' , 'info.created_by'])
                ->rightjoin('info_category' , 'info_category.id' , '=' , 'info.category_id' )
                ->where('info.title' , 'like' ,  '%'.$callback['info'][0]->title.'%')
                ->orwhere('info_category.name' , '=' ,  $callback['info'][0]->name)
                ->orderBy('publish_at' , 'desc')
                ->limit(15)
                ->get();
        }else{
            return redirect()->route('info');   
        }
        
        return view('infodetail' , $callback);
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