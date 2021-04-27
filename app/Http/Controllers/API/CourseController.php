<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Validator;

class CourseController extends Controller
{

    public $successStatus = 200;

    public function getAllCategories(){

        $categories = array();
        try {
            $categories = Category::where(['is_active' => 1])->get();
        } catch (\Illuminate\Database\QueryException $exception) {
    
        }
            
        return response()->json([
            'status' => true ,
            'data' => $categories
        ], $this->successStatus);
    }
}