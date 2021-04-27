<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Config\Constants;
use App\Models\News;
use App\Models\Info;
use App\Models\ConfigModel;

use App\Libraries\HttpClient;

class PublicController extends Controller
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
    public function index()
    {
        
        $prequisite['yt_display'] = ConfigModel::select('value')->where(['parameter' => 'yt_display'])->first();
        
        $prequisite['info'] = Info::where(['is_active' => 1])->offset(0)->limit(4)->orderBy('publish_at' , 'desc')->orderBy('category_id' , 'asc')->get();
        $prequisite['news'] = News::where(['is_active' => 1])->offset(0)->limit(4)->orderBy('publish_at' , 'desc')->get();
        
        return view('home' , $prequisite);
    }
}