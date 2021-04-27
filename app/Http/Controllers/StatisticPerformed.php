<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Libraries\HttpClient;
use Config\Constants;
use Auth;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StatisticPerformed extends Controller
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

    public function statisticPerformed()
    {   
        $prequisite['title'] = "PKB dan PLKB Terlaksana";
        $prequisite['subtitle'] = "Jumlah PKB dan PLKB yang Melaksanakan";
        return view('statisticperformed' , $prequisite);
    }

    public function getAllStatPerformedProv(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $province_id = $request->province_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        
        $builder = DB::table('provinces as a');
        $builder->select(['a.id as province_id','a.name',
        DB::raw('(select count(DISTINCT b.username) from dat_kegiatan_mekop as b where b.kdprovinsi = a.id) as pkbExist'),
        DB::raw('(select count(DISTINCT b.username) from dat_kegiatan_mekop as b where b.kdprovinsi = a.id) as plkbExist'),
        DB::raw('(SELECT COUNT(*) FROM districts as kec 
                    LEFT JOIN regencies as ko ON kec.regency_id = ko.id WHERE ko.province_id = a.id) as districts_count')]);
    
        $list = $builder->where(function($query) use($search){
            if(isset($search)){
                return $query
                ->orwhere('a.id' , 'like' , '%'.$search.'%')
                ->orwhere('a.name' , 'like' , '%'.$search.'%')
                ;
            }
            return null;
        })
        ->where(function($query) use($province_id){
            if(isset($province_id) && $province_id != 'null'){
                return $query->where(['a.id' => $province_id]);
            }
            return null;
        })
        ->groupBy('a.id')
        ->paginate($request->length);

        $data = array();
        $no = 1;
        foreach ($list as $item) {
            $row = array();
            $row[] = $no++;
            $row[] = $item->province_id;
            $row[] = '<a href="#" onclick="openTblRegency('.$item->province_id.','."'".$item->name."'".')">'.$item->name.'</a>';
            $row[] = number_format($item->pkbExist, 0, ',', '.');
            $row[] = number_format(0 , 0, ',', '.');
            $value = DB::table("dat_kegiatan_mekop as a")
            ->select(['a.kdprovinsi',
                DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` b 
                LEFT JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid 
                LEFT JOIN kategori_mekop as d on c.id_mekop = d.id 
                where b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec ) as worked')
            ])
            ->where(["a.kdprovinsi" => $item->province_id])
            ->groupBy("a.kdprovinsi")
            ->groupBy("a.kdkokab")
            ->groupBy("a.kdkec")
            ->get();

            $passed = 0;
            foreach($value as $item_){
                $passed += $item_->worked >= 5 ? 1 : 0;
            }
            
            if($passed > 0){
                $strBuild = '<h6><span class="badge badge-success p-2">'.number_format($passed > 0 ? (($passed / $item->districts_count) * 100) : 0 , 2, ',', '.').'%</span></h6>';
            }else{
                $strBuild = number_format($passed > 0 ? (($passed / $item->districts_count) * 100) : 0 , 2, ',', '.');
            }
            $row[] = $strBuild;
    
            $data[] = $row;
        }

        $output = array(
            "draw" => $request->draw,
            "recordsTotal" => $list->total(),
            "recordsFiltered" => $list->total(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function getAllStatPerformedRegency(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $province_id = $request->province_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        
        $builder = DB::table('provinces as a');
        $builder->select(['b.id as regency_id','b.name',
        DB::raw('(select count(DISTINCT dt.username) from dat_kegiatan_mekop as dt where dt.kdkokab = b.id) as pkbExist'),
        DB::raw('(select count(DISTINCT dt.username) from dat_kegiatan_mekop as dt where dt.kdkokab = b.id) as plkbExist'),
        DB::raw('(SELECT COUNT(*) FROM districts as kec 
                    LEFT JOIN regencies as ko ON kec.regency_id = ko.id WHERE ko.id = b.id) as districts_count')])
        
                    ->rightjoin('regencies as b' , 'a.id' , '=' , 'b.province_id');
    
        $list = $builder->where(function($query) use($search){
            if(isset($search)){
                return $query
                ->orwhere('a.id' , 'like' , '%'.$search.'%')
                ->orwhere('a.name' , 'like' , '%'.$search.'%')
                ;
            }
            return null;
        })
        ->where(function($query) use($province_id){
            if(isset($province_id) && $province_id != 'null'){
                return $query->where(['a.id' => $province_id]);
            }
            return null;
        })
        ->groupBy('b.id')
        ->paginate($request->length);

        $data = array();
        $no = 1;
        foreach ($list as $item) {
            $row = array();
            $row[] = $no++;
            $row[] = $item->regency_id;
            $row[] = '<a href="#" onclick="openTblDistrict('.$item->regency_id.','."'".$item->name."'".')">'.$item->name.'</a>';
            $row[] = number_format($item->pkbExist, 0, ',', '.');
            $row[] = number_format(0 , 0, ',', '.');
            $value = DB::table("dat_kegiatan_mekop as a")
            ->select(['a.kdprovinsi',
                DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` b 
                LEFT JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid 
                LEFT JOIN kategori_mekop as d on c.id_mekop = d.id 
                where b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec ) as worked')
            ])
            ->where(["a.kdkokab" => $item->regency_id])
            ->groupBy("a.kdprovinsi")
            ->groupBy("a.kdkokab")
            ->groupBy("a.kdkec")
            ->get();

            $passed = 0;
            foreach($value as $item_){
                $passed += $item_->worked >= 5 ? 1 : 0;
            }

            if($passed > 0){
                $strBuild = '<h6><span class="badge badge-success p-2">'.number_format($passed > 0 ? (($passed / $item->districts_count) * 100) : 0 , 2, ',', '.').'%</span></h6>';
            }else{
                $strBuild = number_format($passed > 0 ? (($passed / $item->districts_count) * 100) : 0 , 2, ',', '.');
            }
            $row[] = $strBuild;
    
            $data[] = $row;
        }

        $output = array(
            "draw" => $request->draw,
            "recordsTotal" => $list->total(),
            "recordsFiltered" => $list->total(),
            "data" => $data,
        );

        echo json_encode($output);
    }
    
    public function getAllStatPerformedDistrict(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $regency_id = $request->regency_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
    
        $builder = DB::table('districts as a');
        $builder->select(['a.id as district_id' , 'a.name']);
	
        $builder->where(function($query) use($regency_id) {
            if(isset($regency_id)){
                return $query->where('a.regency_id' , '=' , $regency_id);
            }
            return null;
        });

        $builder->where(function($query) use($search) {
            if(isset($search)){
                return $query
                ->orwhere('a.id' , 'like' , '%'.$search.'%')
                ->orwhere('a.name' , 'like' , '%'.$search.'%')
                ;
            }
            return null;
        });

        $list =  $builder->paginate($request->length);
        
        $data = array();
        $no = 1;
        foreach ($list as $item) {
            $row = array();
            $row[] = $no++;
            $row[] = $item->district_id;
            $row[] = '<a href="#" onclick="openTblVillage('.$item->district_id.','."'".$item->name."'".')">'.$item->name.'</a>';
            
            $builder =  DB::table('kategori_mekop as a');
            $mekopValue = $builder->select(['a.id' , 
            DB::raw('(SELECT COUNT(*) from butir_kegiatan_mekop as b 
            JOIN dat_kegiatan_mekop as c ON b.id = c.kegiatanid 
            WHERE b.id_mekop = a.id AND c.kdkec = '.$item->district_id.') as worked')])
            ->where('tingkat', 3)
            ->get();

            foreach ($mekopValue as $item) {
                $status = $item->worked > 0 ? '<i class="text-success bounce fas fa-check"></i>' : '<i class="text-danger fas fa-times"></i>';
                array_push($row, $status);
            }
            
            $data[] = $row;
        }

        $output = array(
            "draw" => $request->draw,
            "recordsTotal" => $list->total(),
            "recordsFiltered" => $list->total(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function getAllStatPerformedVillage(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $district_id = $request->district_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
    
        $builder = DB::table('villages as a');
        $builder->select(['a.id as village_id' , 'a.name']);
	
        $builder->where(function($query) use($district_id) {
            if(isset($district_id)){
                return $query->where('a.district_id' , '=' , $district_id);
            }
            return null;
        });

        $builder->where(function($query) use($search) {
            if(isset($search)){
                return $query
                ->orwhere('a.id' , 'like' , '%'.$search.'%')
                ->orwhere('a.name' , 'like' , '%'.$search.'%')
                ;
            }
            return null;
        });

        $list =  $builder->paginate($request->length);
        
        $data = array();
        $no = 1;
        foreach ($list as $item) {
            $row = array();
            $row[] = $no++;
            $row[] = $item->village_id;
            $row[] = $item->name;

            $builder =  DB::table('kategori_mekop as a');
            $mekopValue = $builder->select(['a.id' , 
            DB::raw('(SELECT COUNT(*) from butir_kegiatan_mekop as b 
            JOIN dat_kegiatan_mekop as c ON b.id = c.kegiatanid 
            WHERE b.id_mekop = a.id AND c.kdkel = '.$item->village_id.') as worked')])
            ->where('tingkat', 4)
            ->get();

            foreach ($mekopValue as $item) {
                $status = $item->worked > 0 ? '<i class="text-success bounce fas fa-check"></i>' : '<i class="text-danger fas fa-times"></i>';
                array_push($row, $status);
            }
            
            $data[] = $row;
        }

        $output = array(
            "draw" => $request->draw,
            "recordsTotal" => $list->total(),
            "recordsFiltered" => $list->total(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    function exportPerformed(Request $request){
        if($request->province_id != 'null'){
            if($request->regency_id != 'null'){
                if($request->district_id != 'null'){
                    return $this->villageExport($request);
                }
                return $this->districtExport($request);
            }
            return $this->regencyExport($request);
        }
        return $this->provinceExport($request);
    }

    function provinceExport($request){
        $builder = DB::table('provinces as a');
        $list = $builder->select([
            'a.id as province_id' , 'a.name' , 
            DB::raw('(select count(DISTINCT b.username) from dat_kegiatan_mekop as b where b.kdprovinsi = a.id) as pkbExist'),
            DB::raw('(select count(DISTINCT b.username) from dat_kegiatan_mekop as b where b.kdprovinsi = a.id) as plkbExist'),
            DB::raw('(SELECT COUNT(*) FROM districts as kec 
                        LEFT JOIN regencies as ko ON kec.regency_id = ko.id WHERE ko.province_id = a.id) as districts_count')
        ])
        ->groupBy('a.id')
        ->get();

        if(isset($list)){
            $spreadsheet = new Spreadsheet;
            $column = 7;
            $row = 1;

            $sheet = $spreadsheet->setActiveSheetIndex(0);

            $styleArray = array(
                'borders' => array(
                    'outline' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('argb' => '#000'),
                    ),
                ),
            );
            
            $sheet->getStyle('A5')->applyFromArray($styleArray);
            $sheet->getStyle('B5')->applyFromArray($styleArray);
            $sheet->getStyle('C5')->applyFromArray($styleArray);
            $sheet->getStyle('D5')->applyFromArray($styleArray);
            $sheet->getStyle('E5')->applyFromArray($styleArray);
            $sheet->getStyle('F5')->applyFromArray($styleArray);
            
            $sheet->getStyle('A6')->applyFromArray($styleArray);
            $sheet->getStyle('B6')->applyFromArray($styleArray);
            $sheet->getStyle('C6')->applyFromArray($styleArray);
            $sheet->getStyle('D6')->applyFromArray($styleArray);
            $sheet->getStyle('E6')->applyFromArray($styleArray);
            $sheet->getStyle('F6')->applyFromArray($styleArray);
            
            $sheet->getStyle('D5:F6')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A5:F6')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => array('argb' => 'FFFFFF'),
                    'size'  => 13,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => '4287F5',
                    ],
                ]
            ]);
        
            foreach ($list as $item) {
                $sheet
                    ->setCellValue('A' . $column, $row)
                    ->setCellValue('B' . $column, $item->province_id)
                    ->setCellValue('C' . $column, $item->name)
                    ->setCellValue('D' . $column, number_format($item->pkbExist, 0, ',', '.'))
                    ->setCellValue('E' . $column, number_format(0 , 0, ',', '.'));

                    $value = DB::table("dat_kegiatan_mekop as a")
                    ->select(['a.kdprovinsi',
                        DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` b 
                        LEFT JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid 
                        LEFT JOIN kategori_mekop as d on c.id_mekop = d.id 
                        where b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec ) as worked')
                    ])
                    ->where(["a.kdprovinsi" => $item->province_id])
                    ->groupBy("a.kdprovinsi")
                    ->groupBy("a.kdkokab")
                    ->groupBy("a.kdkec")
                    ->get();

                    $passed = 0;
                    foreach($value as $item_){
                        $passed += $item_->worked >= 5 ? 1 : 0;
                    }
                    
                $sheet->setCellValue('F' . $column, number_format($passed > 0 ? (($passed / $item->districts_count) * 100) : 0 , 2, ',', '.'));


                    $sheet->getStyle('A'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('B'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('C'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('D'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('E'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('F'. $column)->applyFromArray($styleArray);

                $column++;
                $row++;
            }

            $sheet
                ->setCellValue('A1' , 'DATA PROVINSI TAHUN '.date("Y"))->mergeCells('A1:F1')
                ->setCellValue('A2' , "Persentase Pelaksanaan Mekop Semua Provinsi")->mergeCells('A2:F2')
                ->setCellValue('A4' , '')->mergeCells('A4:F4')

                ->setCellValue('A5', 'No')
                ->setCellValue('B5', 'Kode')
                ->setCellValue('C5', 'Provinsi')
                ->setCellValue('D5', 'Jumlah PKB')
                ->setCellValue('E5' , 'Jumlah PLKB')
                ->setCellValue('F5' , 'Persentase Pelaksanaan')
                
                ->setCellValue('A6', '(1)')
                ->setCellValue('B6', '(2)')
                ->setCellValue('C6', '(3)')
                ->setCellValue('D6', '(4)')
                ->setCellValue('E6', '(5)')
                ->setCellValue('F6', '(6)');
                
            $sheet->getStyle('A')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal('center')->setVertical('center');

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setAutoSize(true);

            $sheet->getStyle('A')->getAlignment()->setVertical('top');
            $sheet->getStyle('B')->getAlignment()->setVertical('top');
            $sheet->getStyle('C')->getAlignment()->setVertical('top');
            $sheet->getStyle('D')->getAlignment()->setVertical('top');
            $sheet->getStyle('E')->getAlignment()->setVertical('top');
            $sheet->getStyle('F')->getAlignment()->setHorizontal('center')->setVertical('top');
                    
            // download spreadsheet dalam bentuk excel .xlsx
            
            date_default_timezone_set("Asia/Jakarta");

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="MEKOP PERSENTASE PELAKSANAAN MEKOP PROVINSI  ('. date("d-m-Y").').xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            return $writer->save('php://output');
        }
        
        return $list ?? ['result' => 0];
    }

    function regencyExport($request){
        $builder = DB::table('regencies as a');
        $list = $builder->select([
            'a.id as regency_id' , 'a.name' , 'b.name as province_name' ,
            DB::raw('(select count(DISTINCT b.username) from dat_kegiatan_mekop as b where b.kdkokab = a.id) as pkbExist'),
            DB::raw('(select count(DISTINCT b.username) from dat_kegiatan_mekop as b where b.kdkokab = a.id) as plkbExist'),
            DB::raw('(SELECT COUNT(*) FROM districts as kec 
                        LEFT JOIN regencies as ko ON kec.regency_id = ko.id WHERE ko.id = a.id) as districts_count')
        ])
        ->leftjoin('provinces as b' , 'a.province_id' , '=' , 'b.id')
        ->where(['province_id' => $request->province_id])
        ->groupBy('a.id')
        ->get();

        if(isset($list)){
            $spreadsheet = new Spreadsheet;
            $column = 7;
            $row = 1;

            $sheet = $spreadsheet->setActiveSheetIndex(0);

            $styleArray = array(
                'borders' => array(
                    'outline' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('argb' => '#000'),
                    ),
                ),
            );
            
            $sheet->getStyle('A5')->applyFromArray($styleArray);
            $sheet->getStyle('B5')->applyFromArray($styleArray);
            $sheet->getStyle('C5')->applyFromArray($styleArray);
            $sheet->getStyle('D5')->applyFromArray($styleArray);
            $sheet->getStyle('E5')->applyFromArray($styleArray);
            $sheet->getStyle('F5')->applyFromArray($styleArray);
            
            $sheet->getStyle('A6')->applyFromArray($styleArray);
            $sheet->getStyle('B6')->applyFromArray($styleArray);
            $sheet->getStyle('C6')->applyFromArray($styleArray);
            $sheet->getStyle('D6')->applyFromArray($styleArray);
            $sheet->getStyle('E6')->applyFromArray($styleArray);
            $sheet->getStyle('F6')->applyFromArray($styleArray);
            
            $sheet->getStyle('D5:F6')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A5:F6')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => array('argb' => 'FFFFFF'),
                    'size'  => 13,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => '4287F5',
                    ],
                ]
            ]);
        
            $province_name;
            foreach ($list as $item) {
                $province_name = $item->province_name;
                
                $sheet
                    ->setCellValue('A' . $column, $row)
                    ->setCellValue('B' . $column, $item->regency_id)
                    ->setCellValue('C' . $column, $item->name)
                    ->setCellValue('D' . $column, number_format($item->pkbExist, 0, ',', '.'))
                    ->setCellValue('E' . $column, number_format(0, 0, ',', '.'));

                    $value = DB::table("dat_kegiatan_mekop as a")
                    ->select(['a.kdprovinsi',
                        DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` b 
                        LEFT JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid 
                        LEFT JOIN kategori_mekop as d on c.id_mekop = d.id 
                        where b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec ) as worked')
                    ])
                    ->where(["a.kdkokab" => $item->regency_id])
                    ->groupBy("a.kdprovinsi")
                    ->groupBy("a.kdkokab")
                    ->groupBy("a.kdkec")
                    ->get();

                    $passed = 0;
                    foreach($value as $item_){
                        $passed += $item_->worked >= 5 ? 1 : 0;
                    }
                    
                    $sheet->setCellValue('F' . $column, number_format($passed > 0 ? (($passed / $item->districts_count) * 100) : 0 , 2, ',', '.'));



                    $sheet->getStyle('A'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('B'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('C'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('D'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('E'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('F'. $column)->applyFromArray($styleArray);

                $column++;
                $row++;
            }

            $sheet
                ->setCellValue('A1' , 'DATA KOTA KABUPATEN PROVINSI '.$province_name." TAHUN ".date("Y"))->mergeCells('A1:F1')
                ->setCellValue('A2' , "Persentase Pelaksanaan Mekop Provinsi ".ucwords(strtolower($province_name) , " "))->mergeCells('A2:F2')
                ->setCellValue('A4' , '')->mergeCells('A4:F4')

                ->setCellValue('A5', 'No')
                ->setCellValue('B5', 'Kode')
                ->setCellValue('C5', 'Kota Kabupaten')
                ->setCellValue('D5', 'Jumlah PKB')
                ->setCellValue('E5' , 'Jumlah PLKB')
                ->setCellValue('F5' , 'Persentase Pelaksanaan')
                
                ->setCellValue('A6', '(1)')
                ->setCellValue('B6', '(2)')
                ->setCellValue('C6', '(3)')
                ->setCellValue('D6', '(4)')
                ->setCellValue('E6', '(5)')
                ->setCellValue('F6', '(6)');
                
            $sheet->getStyle('A')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal('center')->setVertical('center');

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setAutoSize(true);

            $sheet->getStyle('A')->getAlignment()->setVertical('top');
            $sheet->getStyle('B')->getAlignment()->setVertical('top');
            $sheet->getStyle('C')->getAlignment()->setVertical('top');
            $sheet->getStyle('D')->getAlignment()->setVertical('top');
            $sheet->getStyle('E')->getAlignment()->setVertical('top');
            $sheet->getStyle('F')->getAlignment()->setHorizontal('center')->setVertical('top');
                    
            // download spreadsheet dalam bentuk excel .xlsx
            
            date_default_timezone_set("Asia/Jakarta");

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="MEKOP PERSENTASE PELAKSANAAN MEKOP KOTA KABUPATEN PROVINSI '.$province_name.'  ('. date("d-m-Y").').xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            return $writer->save('php://output');
        }
        
        return $list ?? ['result' => 0];
    }
    
    function districtExport($request){
        $builder = DB::table('districts as a');
        $list = $builder->select([
            'a.id as district_id' , 'a.name' , 'b.name as regency_name' ,
        ])
        ->leftjoin('regencies as b' , 'a.regency_id' , '=' , 'b.id')
        ->where(['a.regency_id' => $request->regency_id])
        ->groupBy('a.id')
        ->get();

        if(isset($list)){
            $spreadsheet = new Spreadsheet;
            $column = 7;
            $row = 1;

            $sheet = $spreadsheet->setActiveSheetIndex(0);

            $styleArray = array(
                'borders' => array(
                    'outline' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('argb' => '#000'),
                    ),
                ),
            );
            
            $sheet->getStyle('A5')->applyFromArray($styleArray);
            $sheet->getStyle('B5')->applyFromArray($styleArray);
            $sheet->getStyle('C5')->applyFromArray($styleArray);
            $sheet->getStyle('D5')->applyFromArray($styleArray);
            $sheet->getStyle('E5')->applyFromArray($styleArray);
            $sheet->getStyle('F5')->applyFromArray($styleArray);
            
            $sheet->getStyle('A6')->applyFromArray($styleArray);
            $sheet->getStyle('B6')->applyFromArray($styleArray);
            $sheet->getStyle('C6')->applyFromArray($styleArray);
            $sheet->getStyle('D6')->applyFromArray($styleArray);
            $sheet->getStyle('E6')->applyFromArray($styleArray);
            $sheet->getStyle('F6')->applyFromArray($styleArray);
            
            $sheet->getStyle('D5:F6')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A5:F6')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => array('argb' => 'FFFFFF'),
                    'size'  => 13,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => '4287F5',
                    ],
                ]
            ]);
        
            $regency_name;
            $columnTrigger = ['D' , 'E' , 'F'];
            foreach ($list as $item) {
                $regency_name = $item->regency_name;
                
                $sheet
                    ->setCellValue('A' . $column, $row)
                    ->setCellValue('B' . $column, $item->district_id)
                    ->setCellValue('C' . $column, $item->name);

                    $builder =  DB::table('kategori_mekop as a');
                    $mekopValue = $builder->select(['a.id' , 
                    DB::raw('(SELECT COUNT(*) from butir_kegiatan_mekop as b 
                    JOIN dat_kegiatan_mekop as c ON b.id = c.kegiatanid 
                    WHERE b.id_mekop = a.id AND c.kdkec = '.$item->district_id.') as worked')])
                    ->where('tingkat', 3)
                    ->get();

                    $iter = 0;
                    foreach ($mekopValue as $item) {
                        $status = $item->worked > 0 ? '✓' : '✖';
                        $sheet->setCellValue($columnTrigger[$iter] . $column, $status);
                        $iter++;
                    }

                    $sheet->getStyle('A'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('B'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('C'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('D'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('E'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('F'. $column)->applyFromArray($styleArray);

                $column++;
                $row++;
            }

            $sheet
                ->setCellValue('A1' , 'DATA KECAMATAN '.$regency_name." TAHUN ".date("Y"))->mergeCells('A1:F1')
                ->setCellValue('A2' , "Persentase Pelaksanaan Mekop ".ucwords(strtolower($regency_name), " "))->mergeCells('A2:F2')
                ->setCellValue('A4' , '')->mergeCells('A4:F4')

                ->setCellValue('A5', 'No')
                ->setCellValue('B5', 'Kode')
                ->setCellValue('C5', 'Kecamatan')
                ->setCellValue('D5', 'Staff Meeting')
                ->setCellValue('E5' , 'Rakor Kecamatan')
                ->setCellValue('F5' , 'Minilok')
                
                ->setCellValue('A6', '(1)')
                ->setCellValue('B6', '(2)')
                ->setCellValue('C6', '(3)')
                ->setCellValue('D6', '(4)')
                ->setCellValue('E6', '(5)')
                ->setCellValue('F6', '(6)');
                
            $sheet->getStyle('A')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal('center')->setVertical('center');

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setAutoSize(true);

            $sheet->getStyle('A')->getAlignment()->setVertical('top');
            $sheet->getStyle('B')->getAlignment()->setVertical('top');
            $sheet->getStyle('C')->getAlignment()->setVertical('top');
            $sheet->getStyle('D')->getAlignment()->setHorizontal('center')->setVertical('top');
            $sheet->getStyle('E')->getAlignment()->setHorizontal('center')->setVertical('top');
            $sheet->getStyle('F')->getAlignment()->setHorizontal('center')->setVertical('top');
                    
            // download spreadsheet dalam bentuk excel .xlsx
            
            date_default_timezone_set("Asia/Jakarta");

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="MEKOP PERSENTASE PELAKSANAAN MEKOP KECAMATAN '.$regency_name.'  ('. date("d-m-Y").').xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            return $writer->save('php://output');
        }
        
        return $list ?? ['result' => 0];
    }

    function villageExport($request){
        $builder = DB::table('villages as a');
        $list = $builder->select([
            'a.id as village_id' , 'a.name' , 'c.name as regency_name' , 'b.name as district_name' ,
        ])
        ->leftjoin('districts as b' , 'a.district_id' , '=' , 'b.id')
        ->leftjoin('regencies as c' , 'b.regency_id' , '=' , 'c.id')
        ->where(['a.district_id' => $request->district_id])
        ->groupBy('a.id')
        ->get();

        if(isset($list)){
            $spreadsheet = new Spreadsheet;
            $column = 7;
            $row = 1;

            $sheet = $spreadsheet->setActiveSheetIndex(0);

            $styleArray = array(
                'borders' => array(
                    'outline' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('argb' => '#000'),
                    ),
                ),
            );
            
            $sheet->getStyle('A5')->applyFromArray($styleArray);
            $sheet->getStyle('B5')->applyFromArray($styleArray);
            $sheet->getStyle('C5')->applyFromArray($styleArray);
            $sheet->getStyle('D5')->applyFromArray($styleArray);
            $sheet->getStyle('E5')->applyFromArray($styleArray);
            $sheet->getStyle('F5')->applyFromArray($styleArray);
            $sheet->getStyle('G5')->applyFromArray($styleArray);
            $sheet->getStyle('H5')->applyFromArray($styleArray);
            $sheet->getStyle('I5')->applyFromArray($styleArray);
            
            $sheet->getStyle('A6')->applyFromArray($styleArray);
            $sheet->getStyle('B6')->applyFromArray($styleArray);
            $sheet->getStyle('C6')->applyFromArray($styleArray);
            $sheet->getStyle('D6')->applyFromArray($styleArray);
            $sheet->getStyle('E6')->applyFromArray($styleArray);
            $sheet->getStyle('F6')->applyFromArray($styleArray);
            $sheet->getStyle('G6')->applyFromArray($styleArray);
            $sheet->getStyle('H6')->applyFromArray($styleArray);
            $sheet->getStyle('I6')->applyFromArray($styleArray);
            
            $sheet->getStyle('D5:I6')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A5:I6')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => array('argb' => 'FFFFFF'),
                    'size'  => 13,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => '4287F5',
                    ],
                ]
            ]);
        
            $regency_name;
            $district_name;
            $columnTrigger = ['D' , 'E' , 'F' , 'G' , 'H' , 'I'];
            foreach ($list as $item) {
                $regency_name = $item->regency_name;
                $district_name = $item->district_name;
                
                $sheet
                    ->setCellValue('A' . $column, $row)
                    ->setCellValue('B' . $column, $item->village_id)
                    ->setCellValue('C' . $column, $item->name);

                    $builder =  DB::table('kategori_mekop as a');
                    $mekopValue = $builder->select(['a.id' , 
                    DB::raw('(SELECT COUNT(*) from butir_kegiatan_mekop as b 
                    JOIN dat_kegiatan_mekop as c ON b.id = c.kegiatanid 
                    WHERE b.id_mekop = a.id AND c.kdkel = '.$item->village_id.') as worked')])
                    ->where('tingkat', 4)
                    ->get();

                    $iter = 0;
                    foreach ($mekopValue as $item) {
                        $status = $item->worked > 0 ? '✓' : '✖';
                        $sheet->setCellValue($columnTrigger[$iter] . $column, $status);
                        $iter++;
                    }

                    $sheet->getStyle('A'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('B'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('C'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('D'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('E'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('F'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('G'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('H'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('I'. $column)->applyFromArray($styleArray);

                $column++;
                $row++;
            }

            $sheet
                ->setCellValue('A1' , 'DATA DESA KECAMATAN '.strtoupper($district_name)." ".$regency_name." TAHUN ".date("Y"))->mergeCells('A1:I1')
                ->setCellValue('A2' , "Rasio Pelaksanaan Mekop Kecamanatan ".ucwords(strtolower($district_name), " "))->mergeCells('A2:I2')
                ->setCellValue('A4' , '')->mergeCells('A4:F4')

                ->setCellValue('A5', 'No')
                ->setCellValue('B5', 'Kode')
                ->setCellValue('C5', 'Kecamatan')
                ->setCellValue('D5', 'Rakordes')
                ->setCellValue('E5' , 'Pertemuan Pokja Kamp KB')
                ->setCellValue('F5' , 'Pembinaan IMP')
                ->setCellValue('G5' , 'Pencatatan dan Pelaporan')
                ->setCellValue('H5' , 'KIE')
                ->setCellValue('I5' , 'Pelayanan')
                
                ->setCellValue('A6', '(1)')
                ->setCellValue('B6', '(2)')
                ->setCellValue('C6', '(3)')
                ->setCellValue('D6', '(4)')
                ->setCellValue('E6', '(5)')
                ->setCellValue('F6', '(6)')
                ->setCellValue('G6', '(7)')
                ->setCellValue('H6', '(8)')
                ->setCellValue('I6', '(9)');
                
            $sheet->getStyle('A')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A2:I2')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A3:I3')->getAlignment()->setHorizontal('center')->setVertical('center');

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->getColumnDimension('I')->setAutoSize(true);

            $sheet->getStyle('A')->getAlignment()->setVertical('top');
            $sheet->getStyle('B')->getAlignment()->setVertical('top');
            $sheet->getStyle('C')->getAlignment()->setVertical('top');
            $sheet->getStyle('D')->getAlignment()->setHorizontal('center')->setVertical('top');
            $sheet->getStyle('E')->getAlignment()->setHorizontal('center')->setVertical('top');
            $sheet->getStyle('F')->getAlignment()->setHorizontal('center')->setVertical('top');
            $sheet->getStyle('G')->getAlignment()->setHorizontal('center')->setVertical('top');
            $sheet->getStyle('H')->getAlignment()->setHorizontal('center')->setVertical('top');
            $sheet->getStyle('I')->getAlignment()->setHorizontal('center')->setVertical('top');
                    
            // download spreadsheet dalam bentuk excel .xlsx
            
            date_default_timezone_set("Asia/Jakarta");

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="MEKOP RASIO PELAKSANAAN MEKOP DESA KECAMATAN '.strtoupper($district_name)." ".$regency_name.'  ('. date("d-m-Y").').xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            return $writer->save('php://output');
        }
        
        return $list ?? ['result' => 0];
    }
}