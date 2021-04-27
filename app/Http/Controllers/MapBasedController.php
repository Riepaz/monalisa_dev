<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Config\Constants;
use App\Models\News;
use App\Models\Info;
use App\Models\ConfigModel;

use App\Libraries\HttpClient;

class MapBasedController extends Controller
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

    public function trainingProfile()
    {
        return view('mapview');
    }
    
    public function getPopupData(Request $request)
    {
        $province_id = $request->province_id;
        
        $builder = DB::table('dat_kegiatan_mekop as a');
        $builder->select(['pr.id' , 'pr.name',
            DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` as b JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid join kategori_mekop as d on c.id_mekop = d.id where b.kdprovinsi = a.kdprovinsi AND b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec ) as worked_count'),
            DB::raw('(select count(*) from districts as kc join regencies as ko on kc.regency_id = ko.id where ko.province_id = '.$province_id.') as disctricts_count'),
            DB::raw('((select count(*) from kategori_mekop) * 0.5) as indicat')
        ])
        ->rightjoin('provinces as pr' , 'a.kdprovinsi' , '=' , 'pr.id');

        $builder->where(function($query) use($province_id){
            if(isset($province_id) && $province_id != 'null'){
                return $query->where(['pr.id' => $province_id]);
            }
            return null;
        });
        
        $builder->groupBy("a.kdprovinsi");
        $builder->groupBy("a.kdkokab");
        $builder->groupBy("a.kdkec");
        $qData = $builder->get();

        $i = 1;
        $on = 1;
        foreach($qData as $item){
            if($item->worked_count > $item->indicat){
                $passed = $i++;
            }else if($item->worked_count > 0){
                $ongoing = $on++;
            }
            
            $province = $item->name;
            $disctricts_count = $item->disctricts_count;
        }

        $passed = $passed ?? 0;
        $ongoing = $ongoing ?? 0;
        $disctricts_count = $disctricts_count ?? 0;
        $province = $province ?? 'Tidak Dikenali';
        
        $data = [
            'id' =>  $province_id,
            'province' =>  $province,
            'passed' => $passed,
            'ongoing' => $ongoing,
            'disctricts_count' => $disctricts_count,
            'percentage' =>  $disctricts_count != 0 ? round(($passed / $disctricts_count) * 100 , 2) : 0,
        ];

        return $data;

    }
    
    public function getDashboardPopupData(Request $request)
    {
        $province_id = $request->province_id;
        
        $builder = DB::table('dat_kegiatan_mekop as a');
        $builder->select(['pr.id' , 'pr.name',
            DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` as b JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid join kategori_mekop as d on c.id_mekop = d.id where b.kdprovinsi = a.kdprovinsi AND b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec ) as worked_count'),
            DB::raw('(select count(*) from districts as kc join regencies as ko on kc.regency_id = ko.id where ko.province_id = '.$province_id.') as disctricts_count'),
            DB::raw('(select sum(b.puskec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = pr.id) as pus'),
            DB::raw('((select count(*) from kategori_mekop) * 0.5) as indicat')
        ])
        ->rightjoin('provinces as pr' , 'a.kdprovinsi' , '=' , 'pr.id');

        $builder->where(function($query) use($province_id){
            if(isset($province_id) && $province_id != 'null'){
                return $query->where(['pr.id' => $province_id]);
            }
            return null;
        });
        
        $builder->groupBy("a.kdprovinsi");
        $builder->groupBy("a.kdkokab");
        $builder->groupBy("a.kdkec");
        $qData = $builder->get();

        $i = 1;
        $on = 1;
        foreach($qData as $item){
            if($item->worked_count > $item->indicat){
                $passed = $i++;
            }else if($item->worked_count > 0){
                $ongoing = $on++;
            }
            
            $province = $item->name;
            $disctricts_count = $item->disctricts_count;
            $pus = $item->pus;
        }

        $passed = $passed ?? 0;
        $ongoing = $ongoing ?? 0;
        $disctricts_count = $disctricts_count ?? 0;
        $province = $province ?? 'Tidak Dikenali';
        $pus = $pus ?? 'Tidak Ada';
        
        $data = [
            'id' =>  $province_id,
            'province' =>  $province,
            'passed' => $passed,
            'ongoing' => $ongoing,
            'pus' => $pus,
            'disctricts_count' => $disctricts_count,
            'percentage' =>  $disctricts_count != 0 ? round(($passed / $disctricts_count) * 100 , 2) : 0,
        ];

        return $data;

    }

    public function getRegencyPopupData(Request $request)
    {
        $province_id = $request->province_id;
        $regency_id = $request->regency_id;
        
        $builder = DB::table('dat_kegiatan_mekop as a');
        $builder->select(['a.kdkokab' , 'rg.name',
            DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` as b JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid join kategori_mekop as d on c.id_mekop = d.id where b.kdprovinsi = a.kdprovinsi AND b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec) as worked_count'),
            DB::raw('(select count(*) from districts as kc join regencies as ko on kc.regency_id = ko.id where ko.province_id = '.$province_id.' and kc.regency_id = '.$regency_id.' ) as disctricts_count'),
            DB::raw('((select count(*) from kategori_mekop) * 0.5) as indicat')
        ])
        ->rightjoin('provinces as pr' , 'a.kdprovinsi' , '=' , 'pr.id')
        ->leftjoin('regencies as rg' , 'rg.province_id' , '=' , 'pr.id');

        $builder->where(function($query) use($province_id){
            if(isset($province_id) && $province_id != 'null'){
                return $query->where(['pr.id' => $province_id]);
            }
            return null;
        });
        
        $builder->where(function($query) use($regency_id){
            if(isset($regency_id) && $regency_id != 'null'){
                return $query->where(['rg.id' => $regency_id]);
            }
            return null;
        });

        $builder->groupBy("a.kdprovinsi");
        $builder->groupBy("a.kdkokab");
        $builder->groupBy("a.kdkec");
        $qData = $builder->get();

        $i = 1;
        $on = 1;
        foreach($qData as $item){
            if($item->kdkokab == $regency_id){
                if($item->worked_count > $item->indicat){
                    $passed = $i++;
                }else if($item->worked_count > 0){
                    $ongoing = $on++;
                }
            }
            
            $regency = $item->name;
            $disctricts_count = $item->disctricts_count;
        }

        $passed = $passed ?? 0;
        $ongoing = $ongoing ?? 0;
        $disctricts_count = $disctricts_count ?? 0;
        $regency = $regency ?? 'Tidak Dikenali';
        
        $data = [
            'regency' =>  $regency,
            'passed' => $passed,
            'ongoing' => $ongoing,
            'disctricts_count' => $disctricts_count,
            'percentage' =>  $disctricts_count != 0 ? round(($passed / $disctricts_count) * 100 , 2) : 0,
        ];

        return $data;

    }
    
    public function getDistrictPopupData(Request $request)
    {
        $province_id = $request->province_id;
        $regency_id = $request->regency_id;
        $district_id = $request->district_id;
        
        $builder = DB::table('dat_kegiatan_mekop as a');
        $builder->select(['a.kdkec' , 'dt.name',
            DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` as b JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid join kategori_mekop as d on c.id_mekop = d.id where b.kdprovinsi = '.$province_id.' AND b.kdkokab = '.$regency_id.' AND b.kdkec = '.$district_id.') as worked_count'),
            DB::raw('(select count(*) from villages as vl join districts as kc on vl.district_id = kc.id join regencies as ko on kc.regency_id = ko.id where ko.province_id = '.$province_id.' and kc.regency_id = '.$regency_id.' and kc.id = '.$district_id.' ) as villages_count'),
            DB::raw('((select count(*) from kategori_mekop) * 0.5) as indicat')
        ])
        ->rightjoin('provinces as pr' , 'a.kdprovinsi' , '=' , 'pr.id')
        ->leftjoin('regencies as rg' , 'rg.province_id' , '=' , 'pr.id')
        ->leftjoin('districts as dt' , 'dt.regency_id' , '=' , 'rg.id');

        $builder->where(function($query) use($province_id){
            if(isset($province_id) && $province_id != 'null'){
                return $query->where(['pr.id' => $province_id]);
            }
            return null;
        });
        
        $builder->where(function($query) use($regency_id){
            if(isset($regency_id) && $regency_id != 'null'){
                return $query->where(['rg.id' => $regency_id]);
            }
            return null;
        });
        
        $builder->where(function($query) use($district_id){
            if(isset($district_id) && $district_id != 'null'){
                return $query->where(['dt.id' => $district_id]);
            }
            return null;
        });

        $builder->groupby("a.kdprovinsi");
        $builder->groupby("a.kdkokab");
        $builder->groupby("a.kdkec");
        $qData = $builder->get();

        $i = 1;
        $on = 1;
        foreach($qData as $item){
            if($item->worked_count > $item->indicat && $item->kdkec == $district_id){
                $passed = $i++;
            }else if($item->worked_count > 0 && $item->kdkec == $district_id){
                $ongoing = $on++;
            }
            
            $district = $item->name;
            $villages_count = $item->villages_count;
        }

        $passed = $passed ?? 0;
        $ongoing = $ongoing ?? 0;
        $villages_count = $villages_count ?? 0;
        $district = $district ?? 'Tidak Dikenali';
        
        $data = [
            'district' =>  $district,
            'passed' => $passed,
            'ongoing' => $ongoing,
            'villages_count' => $villages_count,
            'percentage' =>  $villages_count != 0 ? round(($passed / 1) * 100 , 2) : 0,
        ];

        return $data;

    }
}