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

class StatisticPkb extends Controller
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


    public function statisticPkb()
    {   
        $prequisite['title'] = "Rasio PKB/PLKB";
        return view('statisticpkb' , $prequisite);
    }

    public function getAllStatPkbProv(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $province_id = $request->province_id;
        
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
    
        $builder = DB::table('dat_pkb_jml_prov as b');
        $builder->select(['b.*', 'a.name' , 'a.id as province_id']);

        $builder->rightjoin('provinces as a' , 'a.id', '=' , 'b.kdprovinsi');
        $builder->groupBy('a.id');

        $builder->where(function($query) use($search) {
            if(isset($search)){
                return $query
                ->orwhere('a.id' , 'like' , '%'.$search.'%')
                ->orwhere('a.name' , 'like' , '%'.$search.'%')
                ;
            }
            return null;
        });
        
        $builder->where(function($query) use($province_id){
            if(isset($province_id) && $province_id != 'null'){
                return $query->where(['a.id' => $province_id]);
            }
            return null;
        });

        $list =  $builder->paginate($request->length);
        
        $data = array();
        $no = 1;
        foreach ($list as $item) {
            $jumlahKelurahan = DB::table('provinces as a')
                ->select(DB::raw('count(*) as villages_count'))
                ->leftjoin('regencies as b' ,'a.id' , '=' , 'b.province_id')
                ->leftjoin('districts as c' ,'b.id' , '=' , 'c.regency_id')
                ->rightjoin('villages as d' ,'c.id' , '=' , 'd.district_id')
                ->where(['a.id' => $item->province_id])
                ->first()->villages_count;

            $row = array();
            $row[] = $no++;
            $row[] = $item->province_id;
            $row[] = '<a href="#" onclick="openTblRegency('.$item->province_id.','."'".$item->name."'".')">'.$item->name.'</a>';
            $row[] = number_format($jumlahKelurahan, 0, ',', '.');
            $row[] = number_format($item->jumlahPkb, 0, ',', '.');
            $row[] = number_format($item->jumlahPlkb, 0, ',', '.');
            $row[] = number_format($item->jumlahPkbNonPns, 0, ',', '.');
            $row[] = number_format($item->totalPkb, 0, ',', '.');
            $row[] = number_format(($jumlahKelurahan / max($jumlahKelurahan, $item->totalPkb)) * 100, 0, ',', '.') . " : " . number_format(($item->totalPkb / max($jumlahKelurahan, $item->totalPkb)) * 100, 0, ',', '.');

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
    
    public function getAllStatPkbRegency(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $province_id = $request->province_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
    
        $builder = DB::table('dat_pkb_jml_kokab as b');
        $builder->select(['b.*', 'a.name' , 'a.id as regency_id']);

        $builder->rightjoin('regencies as a' , 'a.id', '=' , 'b.kdkokab');
        $builder->groupBy('a.id');

        $builder->where(function($query) use($province_id) {
            if(isset($province_id)){
                return $query->where('a.province_id' , '=' , $province_id);
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
            
            $jumlahKelurahan = DB::table('regencies as b')
                ->select(DB::raw('count(*) as villages_count'))
                ->leftjoin('districts as c' ,'b.id' , '=' , 'c.regency_id')
                ->rightjoin('villages as d' ,'c.id' , '=' , 'd.district_id')
                ->where('b.id' , '=' , $item->regency_id)
                ->first()->villages_count;

            $row = array();
            $row[] = $no++;
            $row[] = $item->regency_id;
            $row[] = '<a href="#" onclick="openTblDistrict('.$item->regency_id.','."'".$item->name."'".')">'.$item->name.'</a>';
            $row[] = number_format($jumlahKelurahan, 0, ',', '.');
            $row[] = number_format($item->jumlahPkb, 0, ',', '.');
            $row[] = number_format($item->jumlahPlkb, 0, ',', '.');
            $row[] = number_format($item->jumlahPkbNonPns, 0, ',', '.');
            $row[] = number_format($item->totalPkb, 0, ',', '.');
            $row[] = number_format(($jumlahKelurahan / max($jumlahKelurahan, $item->totalPkb)) * 100, 0, ',', '.') . " : " . number_format(($item->totalPkb / max($jumlahKelurahan, $item->totalPkb)) * 100, 0, ',', '.');

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

    public function getAllStatPkbDistrict(Request $request)
    {
        $page = $request->start/$request->length + 1;
        $search = $request->search['value'];
        $regency_id = $request->regency_id;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
    
        $builder = DB::table('dat_pkb_jml_kec as b');
        $builder->select(['b.*', 'a.name' , 'a.id as district_id']);

        $builder->rightjoin('districts as a' , 'a.id', '=' , 'b.kdkec');
        $builder->groupBy('a.id');

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
            $row[] = $item->name;
            $row[] = number_format($item->jumlahPkb, 0, ',', '.');
            $row[] = number_format($item->jumlahPlkb, 0, ',', '.');
            $row[] = number_format($item->jumlahPkbNonPns, 0, ',', '.');
            $row[] = number_format($item->totalPkb, 0, ',', '.');

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

    function exportPkb(Request $request){
        
        if($request->province_id != 'null'){
            if($request->regency_id != 'null'){
                return $this->districtExport($request);
            }
            return $this->regencyExport($request);
        }
        return $this->provinceExport($request);
    }

    function provinceExport($request){
        $builder = DB::table('dat_pkb_jml_prov as b');
        $builder->select(['b.*', 'a.name' , 'a.id as province_id']);

        $builder->rightjoin('provinces as a' , 'a.id', '=' , 'b.kdprovinsi');
        $builder->groupBy('a.id');
        
        $list =  $builder->get();

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
                $jumlahKelurahan = DB::table('provinces as a')
                ->select(DB::raw('count(*) as villages_count'))
                ->leftjoin('regencies as b' ,'a.id' , '=' , 'b.province_id')
                ->leftjoin('districts as c' ,'b.id' , '=' , 'c.regency_id')
                ->rightjoin('villages as d' ,'c.id' , '=' , 'd.district_id')
                ->where(['a.id' => $item->province_id])
                ->first()->villages_count;
                
                $sheet
                    ->setCellValue('A' . $column, $row)
                    ->setCellValue('B' . $column, $item->province_id)
                    ->setCellValue('C' . $column, $item->name)
                    ->setCellValue('D' . $column, number_format($jumlahKelurahan, 0, ',', '.'))
                    ->setCellValue('E' . $column,  number_format($item->jumlahPkb, 0, ',', '.'))
                    ->setCellValue('F' . $column, number_format($item->jumlahPlkb, 0, ',', '.'))
                    ->setCellValue('G' . $column, number_format($item->jumlahPkbNonPns, 0, ',', '.'))
                    ->setCellValue('H' . $column, number_format($item->totalPkb, 0, ',', '.'))
                    ->setCellValue('I' . $column, number_format(($jumlahKelurahan / max($jumlahKelurahan, $item->totalPkb)) * 100, 0, ',', '.') . " : " . number_format(($item->totalPkb / max($jumlahKelurahan, $item->totalPkb)) * 100, 0, ',', '.'));
                    

                    $sheet->getStyle('A'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('B'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('C'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('D'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('E'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('F'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('G'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('H'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('I'. $column)->getAlignment()->setHorizontal('center')->setVertical('center');

                $column++;
                $row++;
            }

            $sheet
                ->setCellValue('A1' , 'DATA PROVINSI TAHUN '.date("Y"))->mergeCells('A1:I1')
                ->setCellValue('A2' , "Rasio PKB/PLKB Semua Provinsi")->mergeCells('A2:I2')
                ->setCellValue('A4' , '')->mergeCells('A4:I4')

                ->setCellValue('A5', 'No')
                ->setCellValue('B5', 'Kode')
                ->setCellValue('C5', 'Provinsi')
                ->setCellValue('D5', 'Jumlah Desa')
                ->setCellValue('E5' , 'Jumlah PKB')
                ->setCellValue('F5' , 'Jumlah PLKB')
                ->setCellValue('G5' , 'Jumlah PKB Non PNS')
                ->setCellValue('H5' , 'Total')
                ->setCellValue('I5' , 'Rasio Terhadap Desa')
                
                ->setCellValue('A6', '(1)')
                ->setCellValue('B6', '(2)')
                ->setCellValue('C6', '(3)')
                ->setCellValue('D6', '(4)')
                ->setCellValue('E6', '(5)')
                ->setCellValue('F6', '(6)')
                ->setCellValue('G6', '(7)')
                ->setCellValue('H6', '(8)')
                ->setCellValue('I6', '(9 = 4 : 8)');
                
            $sheet->getStyle('A')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A2:I2')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A3:I3')->getAlignment()->setHorizontal('center')->setVertical('center');

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->getColumnDimension('I')->setAutoSize(true);

            $sheet->getStyle('A')->getAlignment()->setVertical('top');
            $sheet->getStyle('B')->getAlignment()->setVertical('top');
            $sheet->getStyle('C')->getAlignment()->setVertical('top');
            $sheet->getStyle('D')->getAlignment()->setVertical('top');
            $sheet->getStyle('E')->getAlignment()->setVertical('top');
            $sheet->getStyle('F')->getAlignment()->setVertical('top');
            $sheet->getStyle('G')->getAlignment()->setVertical('top');
            $sheet->getStyle('H')->getAlignment()->setVertical('top');
            $sheet->getStyle('I')->getAlignment()->setHorizontal('center')->setVertical('top');
                    
            // download spreadsheet dalam bentuk excel .xlsx
            
            date_default_timezone_set("Asia/Jakarta");

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="MEKOP RASIO PKB_PLKB PROVINSI  ('. date("d-m-Y").').xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            return $writer->save('php://output');
        }
        
        return $list ?? ['result' => 0];
    }
    
    function regencyExport($request){
        $builder = DB::table('dat_pkb_jml_kokab as b');
        $builder->select(['b.*', 'a.name' , 'c.name as province_name' , 'a.id as regency_id']);
        $builder->rightjoin('regencies as a' , 'a.id', '=' , 'b.kdkokab');
        $builder->leftjoin('provinces as c' , 'c.id', '=' , 'a.province_id');
        $builder->where(['a.province_id' => $request->province_id]);
        $builder->groupBy('a.id');
        
        $list =  $builder->get();

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
                $jumlahKelurahan = DB::table('regencies as b')
                ->select(DB::raw('count(*) as villages_count'))
                ->leftjoin('districts as c' ,'b.id' , '=' , 'c.regency_id')
                ->rightjoin('villages as d' ,'c.id' , '=' , 'd.district_id')
                ->where(['b.id' => $item->regency_id])
                ->first()->villages_count;
                
                $sheet
                    ->setCellValue('A' . $column, $row)
                    ->setCellValue('B' . $column, $item->regency_id)
                    ->setCellValue('C' . $column, $item->name)
                    ->setCellValue('D' . $column, number_format($jumlahKelurahan, 0, ',', '.'))
                    ->setCellValue('E' . $column, number_format($item->jumlahPkb, 0, ',', '.'))
                    ->setCellValue('F' . $column, number_format($item->jumlahPlkb, 0, ',', '.'))
                    ->setCellValue('G' . $column, number_format($item->jumlahPkbNonPns, 0, ',', '.'))
                    ->setCellValue('H' . $column, number_format($item->totalPkb, 0, ',', '.'))
                    ->setCellValue('I' . $column, number_format(($jumlahKelurahan / max($jumlahKelurahan, $item->totalPkb)) * 100, 0, ',', '.') . " : " . number_format(($item->totalPkb / max($jumlahKelurahan, $item->totalPkb)) * 100, 0, ',', '.'));
                    

                    $sheet->getStyle('A'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('B'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('C'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('D'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('E'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('F'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('G'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('H'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('I'. $column)->getAlignment()->setHorizontal('center')->setVertical('center');

                $column++;
                $row++;

                $province_name = $item->province_name; 
            }

            $sheet
                ->setCellValue('A1' , 'DATA KOTA/KABUPATEN PROVINSI '.$province_name.' TAHUN '.date("Y"))->mergeCells('A1:I1')
                ->setCellValue('A2' , "Rasio PKB/PLKB Semua Kota/Kabupaten Provinsi ".ucwords(strtolower($province_name)," "))->mergeCells('A2:I2')
                ->setCellValue('A4' , '')->mergeCells('A4:I4')

                ->setCellValue('A5', 'No')
                ->setCellValue('B5', 'Kode')
                ->setCellValue('C5', 'Kota / Kabupaten')
                ->setCellValue('D5', 'Jumlah Desa')
                ->setCellValue('E5' , 'Jumlah PKB')
                ->setCellValue('F5' , 'Jumlah PLKB')
                ->setCellValue('G5' , 'Jumlah PKB Non PNS')
                ->setCellValue('H5' , 'Total')
                ->setCellValue('I5' , 'Rasio Terhadap Desa')
                
                ->setCellValue('A6', '(1)')
                ->setCellValue('B6', '(2)')
                ->setCellValue('C6', '(3)')
                ->setCellValue('D6', '(4)')
                ->setCellValue('E6', '(5)')
                ->setCellValue('F6', '(6)')
                ->setCellValue('G6', '(7)')
                ->setCellValue('H6', '(8)')
                ->setCellValue('I6', '(9 = 4 : 8)');
                
            $sheet->getStyle('A')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A2:I2')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A3:I3')->getAlignment()->setHorizontal('center')->setVertical('center');

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->getColumnDimension('I')->setAutoSize(true);

            $sheet->getStyle('A')->getAlignment()->setVertical('top');
            $sheet->getStyle('B')->getAlignment()->setVertical('top');
            $sheet->getStyle('C')->getAlignment()->setVertical('top');
            $sheet->getStyle('D')->getAlignment()->setVertical('top');
            $sheet->getStyle('E')->getAlignment()->setVertical('top');
            $sheet->getStyle('F')->getAlignment()->setVertical('top');
            $sheet->getStyle('G')->getAlignment()->setVertical('top');
            $sheet->getStyle('H')->getAlignment()->setVertical('top');
            $sheet->getStyle('I')->getAlignment()->setHorizontal('center')->setVertical('top');
                    
            // download spreadsheet dalam bentuk excel .xlsx
            
            date_default_timezone_set("Asia/Jakarta");

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="MEKOP RASIO PKB_PLKB KOTA_KABUPATEN PROVINSI '.$province_name.' ('. date("d-m-Y").').xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            return $writer->save('php://output');
        }
        
        return $list ?? ['result' => 0];
    }

    function districtExport($request){
        $builder = DB::table('dat_pkb_jml_kec as b');
        $builder->select(['b.*', 'a.name' , 'c.name as regency_name' , 'a.id as district_id']);
        $builder->rightjoin('districts as a' , 'a.id', '=' , 'b.kdkec');
        $builder->leftjoin('regencies as c' , 'c.id', '=' , 'a.regency_id');
        $builder->where(['a.regency_id' => $request->regency_id]);
        $builder->groupBy('a.id');
        $list =  $builder->get();

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
            
            $sheet->getStyle('A6')->applyFromArray($styleArray);
            $sheet->getStyle('B6')->applyFromArray($styleArray);
            $sheet->getStyle('C6')->applyFromArray($styleArray);
            $sheet->getStyle('D6')->applyFromArray($styleArray);
            $sheet->getStyle('E6')->applyFromArray($styleArray);
            $sheet->getStyle('F6')->applyFromArray($styleArray);
            $sheet->getStyle('G6')->applyFromArray($styleArray);
            
            $sheet->getStyle('D5:G6')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A5:G6')->applyFromArray([
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
                
                $sheet
                    ->setCellValue('A' . $column, $row)
                    ->setCellValue('B' . $column, $item->district_id)
                    ->setCellValue('C' . $column, $item->name)
                    ->setCellValue('D' . $column, number_format($item->jumlahPkb, 0, ',', '.'))
                    ->setCellValue('E' . $column, number_format($item->jumlahPlkb, 0, ',', '.'))
                    ->setCellValue('F' . $column, number_format($item->jumlahPkbNonPns, 0, ',', '.'))
                    ->setCellValue('G' . $column, number_format($item->totalPkb, 0, ',', '.'));
                    
                    $sheet->getStyle('A'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('B'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('C'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('D'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('E'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('F'. $column)->applyFromArray($styleArray);
                    $sheet->getStyle('G'. $column)->applyFromArray($styleArray);

                $column++;
                $row++;

                $regency_name = $item->regency_name; 
            }

            $sheet
                ->setCellValue('A1' , 'DATA KECAMATAN '.$regency_name.' TAHUN '.date("Y"))->mergeCells('A1:G1')
                ->setCellValue('A2' , "Rasio PKB/PLKB Semua Kecamatan ".ucwords(strtolower($regency_name)," "))->mergeCells('A2:G2')
                ->setCellValue('A4' , '')->mergeCells('A4:G4')

                ->setCellValue('A5', 'No')
                ->setCellValue('B5', 'Kode')
                ->setCellValue('C5', 'Kecamatan')
                ->setCellValue('D5' , 'Jumlah PKB')
                ->setCellValue('E5' , 'Jumlah PLKB')
                ->setCellValue('F5' , 'Jumlah PKB Non PNS')
                ->setCellValue('G5' , 'Total')
                
                ->setCellValue('A6', '(1)')
                ->setCellValue('B6', '(2)')
                ->setCellValue('C6', '(3)')
                ->setCellValue('D6', '(4)')
                ->setCellValue('E6', '(5)')
                ->setCellValue('F6', '(6)')
                ->setCellValue('G6', '(7)');
                
            $sheet->getStyle('A')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal('center')->setVertical('center');

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->getColumnDimension('G')->setAutoSize(true);

            $sheet->getStyle('A')->getAlignment()->setVertical('top');
            $sheet->getStyle('B')->getAlignment()->setVertical('top');
            $sheet->getStyle('C')->getAlignment()->setVertical('top');
            $sheet->getStyle('D')->getAlignment()->setVertical('top');
            $sheet->getStyle('E')->getAlignment()->setVertical('top');
            $sheet->getStyle('F')->getAlignment()->setVertical('top');
            $sheet->getStyle('G')->getAlignment()->setVertical('top');
                    
            // download spreadsheet dalam bentuk excel .xlsx
            
            date_default_timezone_set("Asia/Jakarta");

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="MEKOP RASIO PKB_PLKB KECAMATAN '.$regency_name.' ('. date("d-m-Y").').xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            return $writer->save('php://output');
        }
        
        return $list ?? ['result' => 0];
    }
}