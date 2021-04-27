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

class StatisticExistance extends Controller
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

    public function statisticExistance()
    {   
        $prequisite['title'] = "Keberadaan Peserta KB";
        return view('statisticexistance' , $prequisite);
    }

    public function getAllStatExistanceProv(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $province_id = $request->province_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        
        $builder = DB::table('provinces as a');
        $builder->select('a.id as province_id' , 'a.name' , 
            DB::raw('(select sum(b.puskec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = a.id) as pus'),
            DB::raw('(select sum(b.pakec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = a.id) as pa'),
            DB::raw('(select sum(b.pbkec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = a.id) as pb'),
            DB::raw('(select sum(b.dokec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = a.id) as do'),
            DB::raw('(select sum(b.unkec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = a.id) as un'),
            DB::raw('(SELECT COUNT(*) FROM districts as kec JOIN regencies as ko ON kec.regency_id = ko.id WHERE ko.province_id = a.id) as districts_count')
        );
    
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
                $strBuild = '<span class="badge badge-success p-2">'.number_format($passed > 0 ? (($passed / $item->districts_count) * 100) : 0 , 2, ',', '.').'%</span>';
            }else{
                $strBuild = number_format($passed > 0 ? (($passed / $item->districts_count) * 100) : 0 , 2, ',', '.');
            }
            $row[] = $strBuild;

            $row[] =  number_format($item->pus , 0 ,  ',', '.');
            $row[] =  number_format($item->pa , 0 , ',', '.');
            $row[] =  number_format($item->pb , 0 , ',', '.');
            $row[] =  number_format($item->do , 0 , ',', '.');
            $row[] =  number_format($item->un , 0 , ',', '.');
    
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

    public function getAllStatExistanceRegency(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $province_id = $request->province_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        
        $builder = DB::table('provinces as a');
        $builder->select('b.id as regency_id' , 'b.name' , 
            DB::raw('(select sum(dt.puskec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab) = b.id) as pus'),
            DB::raw('(select sum(dt.pakec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab) = b.id) as pa'),
            DB::raw('(select sum(dt.pbkec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab) = b.id) as pb'),
            DB::raw('(select sum(dt.dokec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab) = b.id) as do'),
            DB::raw('(select sum(dt.unkec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab) = b.id) as un'),
            DB::raw('(SELECT COUNT(*) FROM districts as kec JOIN regencies as ko ON kec.regency_id = ko.id WHERE ko.id = b.id) as districts_count')
        )->rightjoin('regencies as b' , 'a.id' , '=' , 'b.province_id');

        $list = $builder->where(function($query) use($search){
            if(isset($search)){
                return $query
                ->orwhere('b.id' , 'like' , '%'.$search.'%')
                ->orwhere('b.name' , 'like' , '%'.$search.'%')
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
                $strBuild = '<span class="badge badge-success p-2">'.number_format($passed > 0 ? (($passed / $item->districts_count) * 100) : 0 , 2, ',', '.').'%</span>';
            }else{
                $strBuild = number_format($passed > 0 ? (($passed / $item->districts_count) * 100) : 0 , 2, ',', '.');
            }
            $row[] = $strBuild;

            $row[] =  number_format($item->pus , 0 ,  ',', '.');
            $row[] =  number_format($item->pa , 0 , ',', '.');
            $row[] =  number_format($item->pb , 0 , ',', '.');
            $row[] =  number_format($item->do , 0 , ',', '.');
            $row[] =  number_format($item->un , 0 , ',', '.');
    
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
    
    public function getAllStatExistanceDistrict(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $regency_id = $request->regency_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        
        $builder = DB::table('regencies as a');
        $builder->select('b.id as district_id' , 'b.name' , 
            DB::raw('(select sum(dt.puskec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab , dt.kdkec) = b.id) as pus'),
            DB::raw('(select sum(dt.pakec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab , dt.kdkec) = b.id) as pa'),
            DB::raw('(select sum(dt.pbkec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab , dt.kdkec) = b.id) as pb'),
            DB::raw('(select sum(dt.dokec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab , dt.kdkec) = b.id) as do'),
            DB::raw('(select sum(dt.unkec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab , dt.kdkec) = b.id) as un'),
            DB::raw('(SELECT COUNT(*) FROM districts as kec JOIN regencies as ko ON kec.regency_id = ko.id WHERE kec.id = b.id) as districts_count')
        )->rightjoin('districts as b' , 'a.id' , '=' , 'b.regency_id');

        $list = $builder->where(function($query) use($search){
            if(isset($search)){
                return $query
                ->orwhere('b.id' , 'like' , '%'.$search.'%')
                ->orwhere('b.name' , 'like' , '%'.$search.'%')
                ;
            }
            return null;
        })
        ->where(function($query) use($regency_id){
            if(isset($regency_id) && $regency_id != 'null'){
                return $query->where(['a.id' => $regency_id]);
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
            $row[] = $item->district_id;
            $row[] = $item->name;
            $value = DB::table("dat_kegiatan_mekop as a")
            ->select(['a.kdprovinsi',
                DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` b 
                LEFT JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid 
                LEFT JOIN kategori_mekop as d on c.id_mekop = d.id 
                where b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec ) as worked')
            ])
            ->where(["a.kdkec" => $item->district_id])
            ->groupBy("a.kdprovinsi")
            ->groupBy("a.kdkokab")
            ->groupBy("a.kdkec")
            ->get();

            $passed = 0;
            foreach($value as $item_){
                $passed += $item_->worked >= 5 ? 1 : 0;
            }
            
            if($passed > 0){
                $strBuild = '<span class="badge badge-success p-2"><i class="fas fa-check mr-2"></i>Tercapai</span>';
            }else{
                $strBuild = '<span class="badge badge-danger p-2"><i class="fas fa-times mr-2"></i>Belum Tercapai</span>';
            }
            $row[] = $strBuild;

            $row[] =  number_format($item->pus , 0 ,  ',', '.');
            $row[] =  number_format($item->pa , 0 , ',', '.');
            $row[] =  number_format($item->pb , 0 , ',', '.');
            $row[] =  number_format($item->do , 0 , ',', '.');
            $row[] =  number_format($item->un , 0 , ',', '.');
    
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
    
    function exportExistance(Request $request){
        if($request->province_id != 'null'){
            if($request->regency_id != 'null'){
                return $this->districtExport($request);
            }
            return $this->regencyExport($request);
        }
        return $this->provinceExport($request);
    }

    function provinceExport($request){
        $builder = DB::table('provinces as a');
        $list = $builder->select('a.id as province_id' , 'a.name' , 
            DB::raw('(select sum(b.puskec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = a.id) as pus'),
            DB::raw('(select sum(b.pakec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = a.id) as pa'),
            DB::raw('(select sum(b.pbkec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = a.id) as pb'),
            DB::raw('(select sum(b.dokec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = a.id) as do'),
            DB::raw('(select sum(b.unkec) from tabus_mekop_pa_un_do as b where b.kdprovinsi = a.id) as un'),
            DB::raw('(SELECT COUNT(*) FROM districts as kec JOIN regencies as ko ON kec.regency_id = ko.id WHERE ko.province_id = a.id) as districts_count')
        )->groupBy('a.id')
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
        
            foreach ($list as $item) {
                $sheet
                    ->setCellValue('A' . $column, $row)
                    ->setCellValue('B' . $column, $item->province_id)
                    ->setCellValue('C' . $column, $item->name);

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
                    
                $sheet
                ->setCellValue('D' . $column, number_format($passed > 0 ? (($passed / $item->districts_count) * 100) : 0 , 2, ',', '.').'%')
                ->setCellValue('E' . $column, $item->pus)
                ->setCellValue('F' . $column, $item->pa)
                ->setCellValue('G' . $column, $item->pb)
                ->setCellValue('H' . $column, $item->do)
                ->setCellValue('I' . $column, $item->un);


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
                ->setCellValue('A1' , 'DATA KEBERADAAN PESERTA KB PROVINSI TAHUN '.date("Y"))->mergeCells('A1:I1')
                ->setCellValue('A2' , "Keberadaan Peserta KB Semua Provinsi")->mergeCells('A2:I2')
                ->setCellValue('A4' , '')->mergeCells('A4:F4')

                ->setCellValue('A5', 'No')
                ->setCellValue('B5', 'Kode')
                ->setCellValue('C5', 'Provinsi')
                ->setCellValue('D5', 'Pelaksanaan Mekop')
                ->setCellValue('E5', 'Pasangan Usia Subur (PUS)')
                ->setCellValue('F5' , 'Peserta KB Aktif (PA)')
                ->setCellValue('G5' , 'Peserta KB Baru (PB)')
                ->setCellValue('H5' , 'Putus Pakai (DO)')
                ->setCellValue('I5' , 'Tidak Terlayani (UN)')
                
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
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(20);

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
            header('Content-Disposition: attachment;filename="DATA KEBERADAAN PESERTA KB PROVINSI ('. date("d-m-Y").').xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            return $writer->save('php://output');
        }
        
        return $list ?? ['result' => 0];
    }

    function regencyExport($request){
        $builder = DB::table('provinces as a');
        $list = $builder->select('b.id as regency_id' , 'b.name' , 'a.name as province_name', 
            DB::raw('(select sum(dt.puskec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab) = b.id) as pus'),
            DB::raw('(select sum(dt.pakec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab) = b.id) as pa'),
            DB::raw('(select sum(dt.pbkec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab) = b.id) as pb'),
            DB::raw('(select sum(dt.dokec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab) = b.id) as do'),
            DB::raw('(select sum(dt.unkec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab) = b.id) as un'),
            DB::raw('(SELECT COUNT(*) FROM districts as kec JOIN regencies as ko ON kec.regency_id = ko.id WHERE ko.id = b.id) as districts_count')
        )
        ->rightjoin('regencies as b' , 'a.id' , '=' , 'b.province_id')
        ->where(['a.id' => $request->province_id])
        ->groupBy('b.id')
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
        
            $province_name;
            foreach ($list as $item) {
                $province_name = $item->province_name;
                
                $sheet
                    ->setCellValue('A' . $column, $row)
                    ->setCellValue('B' . $column, $item->regency_id)
                    ->setCellValue('C' . $column, $item->name);

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
                    
                $sheet
                ->setCellValue('D' . $column, number_format($passed > 0 ? (($passed / $item->districts_count) * 100) : 0 , 2, ',', '.').'%')
                ->setCellValue('E' . $column, $item->pus)
                ->setCellValue('F' . $column, $item->pa)
                ->setCellValue('G' . $column, $item->pb)
                ->setCellValue('H' . $column, $item->do)
                ->setCellValue('I' . $column, $item->un);


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
            ->setCellValue('A1' , 'DATA KEBERADAAN PESERTA KB KOTA KABUPATEN PROVINSI '.$province_name." TAHUN ".date("Y"))->mergeCells('A1:I1')
            ->setCellValue('A2' , "Keberadaan Peserta KB Provinsi ".ucwords(strtolower($province_name) , " "))->mergeCells('A2:I2')
            ->setCellValue('A4' , '')->mergeCells('A4:I4')

                ->setCellValue('A5', 'No')
                ->setCellValue('B5', 'Kode')
                ->setCellValue('C5', 'Provinsi')
                ->setCellValue('D5', 'Pelaksanaan Mekop')
                ->setCellValue('E5', 'Pasangan Usia Subur (PUS)')
                ->setCellValue('F5' , 'Peserta KB Aktif (PA)')
                ->setCellValue('G5' , 'Peserta KB Baru (PB)')
                ->setCellValue('H5' , 'Putus Pakai (DO)')
                ->setCellValue('I5' , 'Tidak Terlayani (UN)')
                
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
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(20);

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
            header('Content-Disposition: attachment;filename="DATA KEBERADAAN PESERTA KB KOTA KABUPATEN PROVINSI '.$province_name.'  ('. date("d-m-Y").').xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            return $writer->save('php://output');
        }
        
        return $list ?? ['result' => 0];
    }

    function districtExport($request){
        $builder = DB::table('regencies as a');
        $list = $builder->select('b.id as district_id' , 'b.name' , 'a.name as regency_name' ,
            DB::raw('(select sum(dt.puskec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab , dt.kdkec) = b.id) as pus'),
            DB::raw('(select sum(dt.pakec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab , dt.kdkec) = b.id) as pa'),
            DB::raw('(select sum(dt.pbkec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab , dt.kdkec) = b.id) as pb'),
            DB::raw('(select sum(dt.dokec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab , dt.kdkec) = b.id) as do'),
            DB::raw('(select sum(dt.unkec) from tabus_mekop_pa_un_do as dt where CONCAT(dt.kdprovinsi , dt.kdkokab , dt.kdkec) = b.id) as un'),
            DB::raw('(SELECT COUNT(*) FROM districts as kec JOIN regencies as ko ON kec.regency_id = ko.id WHERE kec.id = b.id) as districts_count')
        )->rightjoin('districts as b' , 'a.id' , '=' , 'b.regency_id')
        ->where(['a.id' => $request->regency_id])
        ->groupBy('b.id')
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
            foreach ($list as $item) {
                $regency_name = $item->regency_name;
                
                $sheet
                    ->setCellValue('A' . $column, $row)
                    ->setCellValue('B' . $column, $item->district_id)
                    ->setCellValue('C' . $column, $item->name);

                    $value = DB::table("dat_kegiatan_mekop as a")
                    ->select(['a.kdprovinsi',
                        DB::raw('(SELECT COUNT(DISTINCT b.mekop) FROM `dat_kegiatan_mekop` b 
                        LEFT JOIN butir_kegiatan_mekop as c on c.id = b.kegiatanid 
                        LEFT JOIN kategori_mekop as d on c.id_mekop = d.id 
                        where b.kdkokab = a.kdkokab AND b.kdkec = a.kdkec ) as worked')
                    ])
                    ->where(["a.kdkec" => $item->district_id])
                    ->groupBy("a.kdprovinsi")
                    ->groupBy("a.kdkokab")
                    ->groupBy("a.kdkec")
                    ->get();

                    $passed = 0;
                    foreach($value as $item_){
                        $passed += $item_->worked >= 5 ? 1 : 0;
                    }
                    
                    if($passed > 0){
                        $strBuild = '✓';
                    }else{
                        $strBuild = '✖';
                    }
                    
                $sheet
                    ->setCellValue('D' . $column, $strBuild)
                    ->setCellValue('E' . $column, $item->pus)
                    ->setCellValue('F' . $column, $item->pa)
                    ->setCellValue('G' . $column, $item->pb)
                    ->setCellValue('H' . $column, $item->do)
                    ->setCellValue('I' . $column, $item->un);


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
            ->setCellValue('A1' , 'DATA KEBERADAAN PESERTA KB KECAMATAN '.$regency_name." TAHUN ".date("Y"))->mergeCells('A1:I1')
            ->setCellValue('A2' , "Jumlah Keberadaan Peserta KB ".ucwords(strtolower($regency_name), " "))->mergeCells('A2:I2')
            ->setCellValue('A4' , '')->mergeCells('A4:I4')

                ->setCellValue('A5', 'No')
                ->setCellValue('B5', 'Kode')
                ->setCellValue('C5', 'Provinsi')
                ->setCellValue('D5', 'Pelaksanaan Mekop')
                ->setCellValue('E5', 'Pasangan Usia Subur (PUS)')
                ->setCellValue('F5' , 'Peserta KB Aktif (PA)')
                ->setCellValue('G5' , 'Peserta KB Baru (PB)')
                ->setCellValue('H5' , 'Putus Pakai (DO)')
                ->setCellValue('I5' , 'Tidak Terlayani (UN)')
                
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
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(20);

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
            header('Content-Disposition: attachment;filename="DATA KEBERADAAN PESERTA KB KECAMATAN '.$regency_name.'  ('. date("d-m-Y").').xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            return $writer->save('php://output');
        }
        
        return $list ?? ['result' => 0];
    }
}