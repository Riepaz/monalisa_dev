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
use App\Models\Pusdiklat;
use App\Models\PusdiklatCategory;
use App\Models\CertificateUser;
use App\Libraries\HttpClient;
use Config\Constants;
use Auth;

class Dashboards extends Controller
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
    
    public function dashboard()
    {      
        if(Auth::user()->hasRole('superadmin')){
            $provinces = DB::table('provinces as a')
            ->select(['a.id' , 'a.name',
                DB::raw('(SELECT COUNT(*) FROM districts as kec 
                LEFT JOIN regencies as ko ON kec.regency_id = ko.id WHERE ko.province_id = a.id) as districts_count')])
            
            ->where(function($query) {
                if(Auth::user()->hasRole('adminprovinsi')){
                    return $query->where(['a.id' => Auth::user()->province_id]);
                }
                return null;
            })
            ->get();

            $iter = 0;
            foreach($provinces as $item){
                $qData = DB::table("dat_kegiatan_mekop as a")
                ->select(['a.kdprovinsi',
                    DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` b 
                    LEFT JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid 
                    LEFT JOIN kategori_mekop as d on c.id_mekop = d.id 
                    where b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec ) as worked')
                ])
                ->where(['a.kdprovinsi' => $item->id])
                ->groupBy("a.kdprovinsi")
                ->groupBy("a.kdkokab")
                ->groupBy("a.kdkec")
                ->get();
    
                $passed = 0;
                foreach($qData as $item_){
                    $passed += $item_->worked >= 5 ? 1 : 0;
                }
                
                $data[] = (object) [
                    'name' => ucwords(strtolower($item->name) , ' ') ,
                    'value' => number_format(($passed / $item->districts_count) * 100, 2, '.', ','),
                ];

                $callback['percent'] = $data;
            }
        }else if(Auth::user()->hasRole('adminprovinsi')){
            $regencies = DB::table('regencies as a')
            ->select(['a.id' , 'a.name',
                DB::raw('(SELECT COUNT(*) FROM districts as kec 
                LEFT JOIN regencies as ko ON kec.regency_id = ko.id WHERE ko.id = a.id) as districts_count')])
            
            ->where(function($query) {
                if(Auth::user()->hasRole('adminprovinsi')){
                    return $query->where(['a.province_id' => Auth::user()->province_id]);
                }
                return null;
            })
            ->get();

            foreach($regencies as $item){
                $qData = DB::table("dat_kegiatan_mekop as a")
                ->select(['a.kdprovinsi',
                    DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` b 
                    LEFT JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid 
                    LEFT JOIN kategori_mekop as d on c.id_mekop = d.id 
                    where b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec ) as worked')
                ])
                ->where(['a.kdkokab' => $item->id])
                ->groupBy("a.kdprovinsi")
                ->groupBy("a.kdkokab")
                ->groupBy("a.kdkec")
                ->get();
    
                $passed = 0;
                foreach($qData as $item_){
                    $passed += $item_->worked >= 5 ? 1 : 0;
                }

                $data[] = (object) [
                    'name' => ucwords(strtolower($item->name) , ' ') ,
                    'value' => number_format(($passed / $item->districts_count) * 100, 2, '.', ','),
                    'passed' => $passed,
                    'districts_count' => $item->districts_count,
                ];

                $callback['percent'] = $data;
            }
        }else if(Auth::user()->hasRole('admindaerah')){
            $regencies = DB::table('regencies as a')
            ->select(['a.id' , 'a.name',
                DB::raw('(SELECT COUNT(*) FROM districts as kec 
                LEFT JOIN regencies as ko ON kec.regency_id = ko.id WHERE ko.id = a.id) as districts_count')])
            
            ->where(function($query) {
                if(Auth::user()->hasRole('admindaerah')){
                    return $query->where(['a.id' => Auth::user()->regency_id , 'a.province_id' => Auth::user()->province_id]);
                }

                return null;
            })
            ->get();

            foreach($regencies as $item){
                $qData = DB::table("dat_kegiatan_mekop as a")
                ->select(['a.kdprovinsi',
                    DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` b 
                    LEFT JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid 
                    LEFT JOIN kategori_mekop as d on c.id_mekop = d.id 
                    where b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec ) as worked')
                ])
                ->where(['a.kdkokab' => $item->id])
                ->groupBy("a.kdprovinsi")
                ->groupBy("a.kdkokab")
                ->groupBy("a.kdkec")
                ->get();
    
                $passed = 0;
                foreach($qData as $item_){
                    $passed += $item_->worked >= 5 ? 1 : 0;
                }

                $data[] = (object) [
                    'name' => ucwords(strtolower($item->name) , ' ') ,
                    'value' => number_format(($passed / $item->districts_count) * 100, 2, '.', ','),
                    'passed' => $passed,
                    'districts_count' => $item->districts_count,
                ];

                $callback['percent'] = $data;
            }
        }
        

        if(Auth::user()->hasRole('adminprovinsi')){
            return view('admin.dashboard-province' ,  $callback);
        }
        
        if(Auth::user()->hasRole('admindaerah')){
            return view('admin.dashboard-regency' ,  $callback);
        }

        return view('admin.dashboard' ,  $callback);
    }


    function getKbPartRatio(Request $request){
        $province_id = $request->province_id;
        $regency_id = $request->regency_id;
        $district_id = $request->district_id;
        
        if(Auth::user()->hasRole('superadmin')){
            $qData = DB::table('dat_kegiatan_mekop as a')
            ->select(['pr.id as province_id' , 'pr.name', 'prl.latitude', 'prl.longitude',
                DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` as b JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid join kategori_mekop as d on c.id_mekop = d.id where b.kdprovinsi = a.kdprovinsi AND b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec ) as worked_count'),
                DB::raw('((select count(*) from kategori_mekop) * 0.5) as indicat')
            ])
            ->rightjoin('provinces as pr' , 'a.kdprovinsi' , '=' , 'pr.id')
            ->leftjoin('provinces_location as prl' , 'prl.province_id' , '=' , 'pr.id')
            ->where(function($query) use($province_id){
                if(isset($province_id) && $province_id != 'null'){
                    return $query->where(['pr.id' => $province_id]);
                }
                return null;
            })
            ->groupBy("a.kdprovinsi")
            ->groupBy("a.kdkokab")
            ->groupBy("a.kdkec")
            ->get();


            $result = DB::table('provinces as a')
            ->select('a.id as province_id' , 'a.name' , 'loc.latitude' , 'loc.longitude',
                DB::raw('(select count(DISTINCT b.username) from dat_kegiatan_mekop as b where b.kdprovinsi = a.id) as pkbExist'),
                DB::raw('(select sum(b.puskec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = a.id) as pus'),
                DB::raw('(select sum(b.pakec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = a.id) as pa'),
                DB::raw('(select sum(b.unkec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = a.id) as un'),
                
                
                DB::raw('(select count(*) from districts as kc 
                join regencies as ko on kc.regency_id = ko.id 
                where ko.province_id = a.id ) as districts_count'),

                DB::raw('(SELECT COUNT(*) FROM regencies as ko where ko.province_id = a.id) as regency_count'),
                DB::raw('(SELECT COUNT(*) FROM villages as vl 
                            join districts as kc on vl.district_id = kc.id 
                            join regencies as ko on kc.regency_id = ko.id
                            where ko.province_id = a.id ) as village_count'),
                            
                DB::raw('((select count(*) from kategori_mekop) * 0.5) as indicat')
            )
            ->leftjoin('provinces_location as loc' , 'a.id', '=', 'loc.province_id')
            ->where(function($query) use($province_id){
                if(isset($province_id) && $province_id != 'null'){
                    return $query->where(['a.id' => $province_id]);
                }
                return null;
            })
            ->groupBy('a.id')
            ->get();
    
            $i = 1;
            $iter = 0;
            $nationalDistrictCount = 0;
            $nationalPecentage = 0;
            foreach($result as $province){
                
                foreach($qData as $item){
                    if( $item->province_id == $province->province_id){   
                        if($item->worked_count > $item->indicat){
                            $passed = $i++;
                        }
                    }   
                }
    
                $passed = $passed ?? 0;
                            
                $nationalPecentage += round(($passed / $province->districts_count) * 100 , 2);
                $nationalDistrictCount  += $province->districts_count;

                $iter++;
                $i = 1;
            }

            $result->districts_count = $nationalDistrictCount;
            $province->worked_count = round($nationalPecentage / sizeof($result), 2);
            
        }else if(Auth::user()->hasRole('adminprovinsi') or Auth::user()->hasRole('admindaerah')){
            $result = DB::table('regencies as a')
            ->select('a.id as regency_id' , 'a.name' , 'b.name as province_name', 
                DB::raw('(select count(DISTINCT b.username) from dat_kegiatan_mekop as b where CONCAT(b.kdprovinsi , b.kdkokab) = a.id) as pkbExist'),
                DB::raw('(select sum(b.puskec) from tabus_mekop_pa_un_do as b where CONCAT(b.kdprovinsi , b.kdkokab) = a.id) as pus'),
                DB::raw('(select sum(b.pakec) from tabus_mekop_pa_un_do as b where CONCAT(b.kdprovinsi , b.kdkokab) = a.id) as pa'),
                DB::raw('(select sum(b.unkec) from tabus_mekop_pa_un_do as b where CONCAT(b.kdprovinsi , b.kdkokab) = a.id) as un'),
                DB::raw('(SELECT COUNT(*) FROM villages as vl JOIN districts as kec on vl.district_id = kec.id JOIN regencies as ko ON kec.regency_id = ko.id WHERE ko.province_id = b.id) as village_count'),
                DB::raw('(SELECT COUNT(*) FROM districts as kec JOIN regencies as ko ON kec.regency_id = ko.id WHERE ko.province_id = b.id) as districts_count'),
                DB::raw('(SELECT COUNT(*) FROM regencies as ko WHERE ko.province_id = b.id) as regency_count')
            )
            ->leftjoin('provinces as b' , 'a.province_id' , '=' , 'b.id')
            ->where(function($query) {
                if(Auth::user()->hasRole('adminprovinsi')){
                    return $query->where(['a.province_id' => Auth::user()->province_id]);
                }

                if(Auth::user()->hasRole('admindaerah')){
                    $data[] = Auth::user()->regency_id;
                    return $query->where(
                        [
                            'a.id' => Auth::user()->regency_id,
                            'a.province_id' => Auth::user()->province_id
                        ]

                    );
                }
                return null;
            })
            ->groupBy('a.id')
            ->get();
        }
        
        return sizeof($result) > 0 ? $result : 0;
    }
}