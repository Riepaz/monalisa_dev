<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class AddressController extends Controller
{
    //
    public function getProvinces()
    {
        $provinces = DB::table('provinces')->pluck("name", "id");
        return view('auth.register', compact('provinces'));
    }

    public function getAllProvinces()
    {
        $provinces = DB::table("provinces")->pluck("name", "id");
        return json_encode($provinces);
    }

    public function getRegencies($id)
    {
        $regencies = DB::table("regencies")->where("province_id", $id)->pluck("name", "id");
        return json_encode($regencies);
    }

    public function getDistricts($id)
    {
        $districts = DB::table("districts")->where("regency_id", $id)->pluck("name", "id");
        return json_encode($districts);
    }

    public function getVillages($id)
    {
        $villages = DB::table("villages")->where("district_id", $id)->pluck("name", "id");
        return json_encode($villages);
    }

    
    public function getAllProvincesByAuth()
    {
        $provinces = DB::table("provinces")
        ->where(function($query) {
            return $query->where('id' , '=' , Auth::user()->province_id);
        })
        ->pluck("name", "id");
        return json_encode($provinces);
    }

    public function getRegenciesByAuth()
    {
        $regencies = DB::table("regencies")
        ->where(function($query) {
            return $query->where('id' , '=' , Auth::user()->regency_id);
        })
        ->pluck("name", "id");
        return json_encode($regencies);
    }

    public function getDistrictsByAuth()
    {
        $districts = DB::table("districts")
        ->where(function($query) {
            return $query->where('id' , '=' , Auth::user()->district_id);
        })
        ->pluck("name", "id");
        return json_encode($districts);
    }

    public function getVillagesByAuth()
    {
        $villages = DB::table("villages")
        ->where(function($query) {
            return $query->where('id' , '=' , Auth::user()->village_id);
        })
        ->pluck("name", "id");
        return json_encode($villages);
    }
    
    
    public function getAllFullProvinces(Request $request)
    {
        $province_id = $request->province_id;
        
        $qData = DB::table('dat_kegiatan_mekop as a')
        ->select(['pr.id as province_id' , 'pr.name', 'prl.latitude', 'prl.longitude',
            DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` as b JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid join kategori_mekop as d on c.id_mekop = d.id where b.kdprovinsi = a.kdprovinsi AND b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec ) as worked_count'),
            DB::raw('(select count(*) from districts as kc join regencies as ko on kc.regency_id = ko.id where ko.province_id = a.kdprovinsi ) as disctricts_count'),
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

        $provinces = DB::table('provinces as a')
        ->select(['a.id' , 'a.name' , 'b.latitude' , 'b.longitude'])
        ->leftjoin('provinces_location as b' , 'b.province_id' , '=' , 'a.id')
        ->where(function($query) use($province_id){
            if(isset($province_id) && $province_id != 'null'){
                return $query->where(['a.id' => $province_id]);
            }
            return null;
        })
        ->get();

        $i = 1;
        $iter = 0;
        foreach($provinces as $province){
            
            foreach($qData as $item){
                if( $item->province_id == $province->id){   
                    if($item->worked_count > $item->indicat){
                        $passed = $i++;
                    }
                    
                    $disctricts_count = $item->disctricts_count ?? 0;
                    $latitude = $item->latitude;
                    $longitude = $item->longitude;
                }   
            }

            $passed = $passed ?? 0;
                        
            $data[$iter] = [
                'id' =>  $province->id,
                'latitude' =>  $province->latitude,
                'longitude' =>  $province->longitude,
                'percentage' =>  isset($disctricts_count) && $disctricts_count != 0 ? round(($passed / $disctricts_count) * 100 , 2) : 0,
            ];
                        
            $iter++;
            $i = 1;
        }
        
        return $data;
    }
    
    public function getFullRegencies(Request $request)
    {
        $province_id = $request->province_id;
        $regency_id = $request->regency_id;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius;

        $regencies = DB::table('regencies as a')
        ->select(['a.id' , 'a.province_id' , 'a.name', 'b.latitude' , 'b.longitude', 
        DB::raw('(
            111.111 * DEGREES(
                    ACOS(
                        LEAST(
                            1.0, COS(RADIANS(b.Latitude)) 
                            * COS(RADIANS('.$latitude.')) 
                            * COS(RADIANS(b.Longitude - '.$longitude.'))
                            + SIN(RADIANS(b.Latitude))
                            * SIN(RADIANS('.$latitude.'))
                            )
                        )
                    ) 
        ) AS distance')])
        ->leftjoin('regencies_location as b' , 'b.regency_id' , '=' , 'a.id')
        ->where(function($query) use($province_id){
            if(isset($province_id) && $province_id != 'null'){
                return $query->where(['a.province_id' => $province_id]);
            }
            return null;
        })
        ->where(function($query) use($regency_id){
            if(isset($regency_id) && $regency_id != 'null'){
                return $query->where(['a.id' => $regency_id]);
            }
            return null;
        })
        ->havingRaw('distance < '.$radius)
        ->get();
        
        return $regencies;
    }
    
    
    public function getFullDistrict(Request $request)
    {
        $province_id = $request->province_id;
        $regency_id = $request->regency_id;
        $district_id = $request->district_id;
        
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius;

        $districts = DB::table('districts as a')
        ->select(['a.id' , 'a.regency_id' , 'c.province_id' , 'a.name', 'b.latitude' , 'b.longitude', 
        DB::raw('(
            111.111 * DEGREES(
                    ACOS(
                        LEAST(
                            1.0, COS(RADIANS(b.Latitude)) 
                            * COS(RADIANS('.$latitude.')) 
                            * COS(RADIANS(b.Longitude - '.$longitude.'))
                            + SIN(RADIANS(b.Latitude))
                            * SIN(RADIANS('.$latitude.'))
                            )
                        )
                    ) 
        ) AS distance')])
        ->leftjoin('districts_location as b' , 'b.district_id' , '=' , 'a.id')
        ->leftjoin('regencies as c' , 'a.regency_id' , '=' , 'c.id')
        ->leftjoin('provinces as d' , 'c.province_id' , '=' , 'd.id')
        ->where(function($query) use($district_id){
            if(isset($district_id) && $district_id != 'null'){
                return $query->where(['a.id' => $district_id]);
            }
            return null;
        })
        ->where(function($query) use($regency_id){
            if(isset($regency_id) && $regency_id != 'null'){
                return $query->where(['a.regency_id' => $regency_id]);
            }
            return null;
        })
        ->where(function($query) use($province_id){
            if(isset($province_id) && $province_id != 'null'){
                return $query->where(['c.province_id' => $province_id]);
            }
            return null;
        })
        ->havingRaw('distance < '.$radius)
        ->get();
        
        return $districts;
    }

    public function getFullVillages()
    {
        $villages = DB::table("villages as a")
        ->select(['a.id' , 'a.name' , 'b.latitude' , 'b.longitude'])
        ->leftjoin('villages_location as b' , 'a.id' , '=' , 'b.province_id')
        ->get();

        return $villages;
    }


}