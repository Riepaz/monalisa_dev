<?php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;
use Config\Constants;

use Auth;

class ComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function($view) {
            
            $view->with('url', array(
                'bima' => Constants::$BIMA_BASEURL ,
                'learning' => Constants::$LEARNING_BASEURL ,
                )
            );
        });
    }
}