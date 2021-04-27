<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Event;
use App\Libraries\HttpClient;
use Config\Constants;
use Auth;

class EventController extends Controller
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
    
    public function event()
    {
        return view('admin.event');
    }
    
    public function eventById($id)
    {
        return Event::where(['id' => $id])
        ->get();
    
    }
    
    public function getallEventOption($fiscaly_id)
    {
        return Event::
        where(['tahun_id' => $fiscaly_id , 'status' => '1' , 'deleted' => 0])
        ->orderBy('name' , 'desc')
        ->pluck("name", "id");
    }

    public function getallEvent()
    {
        
        $event = Event::select(['butir_kegiatan_mekop.*', 'c.mekop as category' , 'b.name as year'])
        ->leftjoin('kategori_mekop as c' , 'c.id' , '=' , 'butir_kegiatan_mekop.id_mekop')
        ->leftjoin('fiscal_years as b' , 'b.id' , '=' , 'c.tahun_id')
        ->where(['butir_kegiatan_mekop.deleted' => 0])
        ->get();

        $i = 1;
        $data = array();

        foreach($event as $item){
            
            $url = "#";
            $row = array();
    
            $row[] = $i++;
            $row[] = $item->category;
            $row[] = $item->deskripsiKegiatan;
            $row[] = $item->year;

            if($item->tingkat == 3 ){
                $row[] = '<span class="badge badge-info p-2">Kecamatan<span>';
            }else if($item->tingkat == 4 ){
                $row[] = '<span class="badge badge-info p-2">Desa & Kelurahan<span>';
            } 

            if($item->status){
                $row[] = '<span class="badge badge-success p-2">Aktif<span>';
                $activate = 'fa-remove';
                $title = 'Non-Aktifkan';
                $status = 0;
            }else{
                $row[] = '<span class="badge badge-danger p-2">Tidak Aktif<span>';
                $activate = 'fa-paper-plane';
                $title = 'Aktifkan';
                $status = 1;
            } 

            $row[] = '
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="edit_event('."'".$item->id."'".')" title="Edit Kategori" data-toggle="modal" data-target="#compose_event_modal"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-sm btn-outline-secondary mb-1" onclick="publish_event('."'".$item->id."'".' , '.$status.' )" title="'.$title.'"><i class="fas '.$activate.'" ></i></button>
            <button class="btn btn-sm btn-sm btn-outline-danger mb-1" onclick="delete_event('."'".$item->id."'".')" title="Hapus Kategori"><i class="fas fa-trash"></i></button>
            ';
            
            $data[] = $row;
        }
        
            $output = array(
                "data" => $data,
            );
            
            echo json_encode($output);
    }

    public function submitEvent(Request $request)
    {
        $data = [
            'deskripsiKegiatan' => $request->event_names,
            'id_mekop' => $request->category_id,
            'status' => 1,
            'nilai' => 1,
        ];

        if(!isset($request->event_id)){
            Event::insert($data);
        }else{
            $event = Event::where(['id' => $request->event_id])->first();
            $event->update($data);
        }
    }

    public function activateEvent(Request $request)
    {
        $data = [
            'status' => $request->status
        ];
        
        $event = Event::where(['id' => $request->id])->first();
        $event->update($data);
    }

    public function deleteEvent(Request $request)
    {
        $event = Event::where(['id' => $request->id])->first();
        $event->update(['deleted' => 1]);
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