<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use Config\Constants;
use App\Http\Controllers\Controller;
use App\Libraries\Currency;
use App\Libraries\Date;
use App\Libraries\Dateduration;
use App\Libraries\Encryption;
use App\Libraries\HttpClient;

use App\Models\CertificateUser;
use App\Models\ConfigModel;
use App\Models\User;
use App\Models\Role;
use App\Models\RoleUser;
use Auth;

class RolesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $course_ROLE = 1;
    

    public function __construct()
    {
        //$this->middleware('auth');

        date_default_timezone_set('Asia/Jakarta');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function getallRolesOption()
    {
        return Role::orderBy('name' , 'desc')
        ->where(function($query){

            if(!Auth::user()->hasRole('superadmin')){
                return $query
                ->where('name' , '!=' , 'admin')
                ->where('name' , '!=' , 'superadmin');
            }

            return null;
        })
        ->pluck("name", "id");
    }

}