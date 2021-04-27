<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Auth::routes();

Route::get('/', 'PublicController@index')->name('home');
Route::get('/news', 'NewsController@news')->name('news');
Route::get('/news/{id}', 'NewsController@newsAll')->name('newsall');
Route::get('/news-view/{id}', 'NewsController@newsDetail')->name('newsdetail');

Route::get('/info/{id}', 'InfoController@infoAll')->name('infoall');

Route::get('/profil-wilayah', 'MapBasedController@trainingProfile')->name('training.profile');
Route::post('/getpopupdata', 'MapBasedController@getPopupData')->name('getpopupdata');
Route::post('/getdashboardpopupdata', 'MapBasedController@getDashboardPopupData')->name('getdashboardpopupdata');
Route::post('/getregencypopupdata', 'MapBasedController@getRegencyPopupData')->name('getregencypopupdata');
Route::post('/getdistrictpopupdata', 'MapBasedController@getDistrictPopupData')->name('getdistrictpopupdata');

//PAKET STATISTIK
Route::get('/statistic/pkb', 'StatisticPkb@statisticPkb')->name('statistic.pkb');
Route::post('/statistic/getallstatpkbprov', 'StatisticPkb@getAllStatPkbProv')->name('getallstatpkbprov.pkb');
Route::post('/statistic/getallstatpkbregency', 'StatisticPkb@getAllStatPkbRegency')->name('getallstatpkbregency.pkb');
Route::post('/statistic/getallstatpkbdistrict', 'StatisticPkb@getAllStatPkbDistrict')->name('getallstatpkbdistrict.pkb');
Route::post('/statistic/exportpkb', 'StatisticPkb@exportPkb')->name('exportpkb.pkb');
//PAKET STATISTIK

//PAKET STATISTIK
Route::get('/statistic/ppkbd', 'StatisticPpkbd@statisticPpkbd')->name('statistic.ppkbd');
Route::post('/statistic/getallstatppkbdprov', 'StatisticPpkbd@getAllStatPpkbdProv')->name('getallstatppkbdprov.ppkbd');
Route::post('/statistic/getallstatppkbdregency', 'StatisticPpkbd@getAllStatPpkbdRegency')->name('getallstatppkbdregency.ppkbd');
Route::post('/statistic/getallstatppkbddistrict', 'StatisticPpkbd@getAllStatPpkbdDistrict')->name('getallstatppkbddistrict.ppkbd');
Route::post('/statistic/exportppkbd', 'StatisticPpkbd@exportPpkbd')->name('exportppkbd.ppkbd');
//PAKET STATISTIK

//PAKET STATISTIK
Route::get('/statistic/realize', 'StatisticRealize@statisticRealize')->name('statistic.realize');
Route::post('/statistic/getallstatrealizeprov', 'StatisticRealize@getAllStatRealizeProv')->name('getallstatrealizeprov.realize');
Route::post('/statistic/getallstatrealizeregency', 'StatisticRealize@getAllStatRealizeRegency')->name('getallstatrealizeregency.realize');
Route::post('/statistic/getallstatrealizedistrict', 'StatisticRealize@getAllStatRealizeDistrict')->name('getallstatrealizedistrict.realize');
Route::post('/statistic/getallstatrealizevillage', 'StatisticRealize@getAllStatRealizeVillage')->name('getallstatrealizevillage.realize');
Route::post('/statistic/exportrealize', 'StatisticRealize@exportRealize')->name('exportrealize.realize');
//PAKET STATISTIK

//PAKET STATISTIK
Route::get('/statistic/performed', 'StatisticPerformed@statisticPerformed')->name('statistic.performed');
Route::post('/statistic/getallstatperformedprov', 'StatisticPerformed@getAllStatPerformedProv')->name('getallstatperformedprov.performed');
Route::post('/statistic/getallstatperformedregency', 'StatisticPerformed@getAllStatPerformedRegency')->name('getallstatperformedregency.performed');
Route::post('/statistic/getallstatperformeddistrict', 'StatisticPerformed@getAllStatPerformedDistrict')->name('getallstatperformeddistrict.performed');
Route::post('/statistic/getallstatperformedvillage', 'StatisticPerformed@getAllStatPerformedVillage')->name('getallstatperformedvillage.performed');
Route::post('/statistic/exportperformed', 'StatisticPerformed@exportPerformed')->name('exportperformed.realize');
//PAKET STATISTIK

//PAKET STATISTIK
Route::get('/statistic/existance', 'StatisticExistance@statisticExistance')->name('statistic.existance');
Route::post('/statistic/getallstatexistanceprov', 'StatisticExistance@getAllStatExistanceProv')->name('getallstatexistanceprov.existance');
Route::post('/statistic/getallstatexistanceregency', 'StatisticExistance@getAllStatExistanceRegency')->name('getallstatexistanceregency.existance');
Route::post('/statistic/getallstatexistancedistrict', 'StatisticExistance@getAllStatExistanceDistrict')->name('getallstatexistancedistrict.existance');
Route::post('/statistic/getallstatexistancevillage', 'StatisticExistance@getAllStatExistanceVillage')->name('getallstatexistancevillage.existance');
Route::post('/statistic/exportexistance', 'StatisticExistance@exportExistance')->name('exportexistance.realize');
//PAKET STATISTIK

Route::get('/login', 'AccountsLoginController@showLoginForm')->name('loginform');
Route::post('/login', 'AccountsLoginController@login')->name('login');

Route::get('/register', 'Auth\RegisterController@showRegisterForm')->name('registerform');
Route::post('/register', 'Auth\RegisterController@register')->name('register');

Route::post('/logout', 'AccountsLoginController@logout')->name('logout');


Route::get('typeuser', 'Auth\RegisterController@getTypeUser')->name('typeuser');
Route::get('statususer', 'Auth\RegisterController@getStatusUser')->name('statususer');
Route::get('profesi/{id}', 'Auth\RegisterController@getProfesi')->name('profesi');

Route::get('province/getallprovince', 'AddressController@getAllProvinces')->name('provinces.get');
Route::get('province/getregency/{id}', 'AddressController@getRegencies')->name('regency.get');
Route::get('regency/getdistrict/{id}', 'AddressController@getDistricts')->name('district');
Route::get('district/getvillage/{id}', 'AddressController@getVillages')->name('villages');

Route::post('province/fullprovinces', 'AddressController@getAllFullProvinces')->name('fullprovinces.get');
Route::post('province/fullregency', 'AddressController@getFullRegencies')->name('fullregency.get');
Route::post('regency/fulldistrict', 'AddressController@getFullDistrict')->name('fulldistrict.get');
Route::get('district/fullvillages/{id}', 'AddressController@getFullVillages')->name('fullvillages');

Route::post('/usernamevalidation', 'Admin\UsersManagementController@usernameValidation')->name('usernamevalidation');
Route::post('/emailvalidation', 'Admin\UsersManagementController@emailValidation')->name('emailvalidation');

//SELECT OPTION MENU
Route::get('/admin/administrative/getallfiscalyearsoption', 'Admin\FiscalYearsController@getallFiscaYearsOption')->name('admin.getallfiscalyearsoption');
Route::get('/admin/diklat/getallcontraceptionoption/{id}', 'Admin\ContraceptionController@getallContraceptionOption')->name('admin.getallcontraceptionoption');

Route::get('/admin/getallcategoryoption', 'Admin\CategoriesController@getallCategoryOption')->name('admin.getallcategoryoption');
//SELECT OPTION MENU


Route::group(['middleware' => ['auth']], function () {
    
    Route::get('province/getallprovincebyauth', 'AddressController@getAllProvincesByAuth')->name('provinces.getbyauth');
    Route::get('province/getregencybyauth', 'AddressController@getRegenciesByAuth')->name('regency.getbyauth');
    Route::get('regency/getdistrictbyauth', 'AddressController@getDistrictsByAuth')->name('district.getbyauth');
    Route::get('district/getvillagebyauth', 'AddressController@getVillagesByAuth')->name('villages.getbyauth');    

    Route::group(['middleware' => 'role:superadmin,admin,adminprovinsi,admindaerah'] , function(){ 
        
        Route::get('/admin/dashboard', 'Admin\Dashboards@dashboard')->name('admin.dashboard');
        Route::post('/admin/getkbpartratio', 'Admin\Dashboards@getKbPartRatio')->name('getkbpartratio.dashboard');
        
        Route::post('/admin/diklat/getallinfo', 'Admin\InfoController@getallinfo')->name('admin.getallinfo');
        
        Route::group(['middleware' => 'role:superadmin,admin,adminprovinsi'] , function(){ 
            Route::get('/admin/info', 'Admin\InfoController@info')->name('admin.info');
            Route::post('/admin/submitinfo', 'Admin\InfoController@submitinfo')->name('admin.submitinfo');
            Route::get('/admin/infobyid/{id}', 'Admin\InfoController@infoById')->name('admin.infobyid');
            Route::get('/admin/deleteinfo/{id}', 'Admin\InfoController@deleteinfo')->name('admin.deleteinfo');
            Route::post('/admin/activateinfo', 'Admin\InfoController@activateinfo')->name('admin.activateinfo');
            Route::get('/admin/infocategories', 'Admin\InfoController@infoCategories')->name('admin.infocategories');
            
        });
        

        Route::post('/admin/userparticipantbyid', 'Admin\UsersManagementController@userParticipantById')->name('admin.userparticipantbyid');
            
        Route::group(['middleware' => 'role:superadmin,adminprovinsi'] , function(){ 
            
            //PAKET CRUD
            Route::get('/admin/users/users', 'Admin\UsersManagementController@users')->name('admin.users');
            Route::post('/admin/users/getallusers', 'Admin\UsersManagementController@getallUsers')->name('admin.getallusers');
            Route::post('/admin/users/deleteuser', 'Admin\UsersManagementController@deleteUser')->name('admin.deleteuser');
            Route::post('/admin/users/submitmoduser', 'Admin\UsersManagementController@submitModUser')->name('admin.submitmoduser');
            Route::get('/admin/userbyid/{id}', 'Admin\UsersManagementController@userById')->name('admin.userbyid');
            Route::post('/admin/activateuser', 'Admin\UsersManagementController@activateUser')->name('admin.activateuser');
            Route::post('/admin/resetpassword', 'Admin\UsersManagementController@resetUser')->name('admin.resetpassword');
            //PAKET CRUD

            //PAKET CRUD
            Route::get('/admin/administrative/fiscal', 'Admin\FiscalYearsController@fiscalYears')->name('admin.fiscal');
            Route::get('/admin/administrative/getallfiscalyears', 'Admin\FiscalYearsController@getallFiscaYears')->name('admin.getallfiscalyears');
            Route::get('/admin/administrative/deletefiscalyears/{id}', 'Admin\FiscalYearsController@deleteFiscalYears')->name('admin.deletefiscal');
            Route::post('/admin/administrative/submityear', 'Admin\FiscalYearsController@submitYear')->name('admin.submityear');
            Route::get('/admin/fiscalybyid/{id}', 'Admin\FiscalYearsController@fiscalYById')->name('admin.fiscalybyid');
            //PAKET CRUD

            //PAKET CRUD
            Route::get('/admin/administrative/categories', 'Admin\CategoriesController@categories')->name('admin.categories');
            Route::get('/admin/administrative/getallcategories', 'Admin\CategoriesController@getallCategories')->name('admin.getallcategories');
            Route::get('/admin/administrative/deletecategory/{id}', 'Admin\CategoriesController@deleteCategory')->name('admin.deletecategory');
            Route::post('/admin/administrative/submitcategory', 'Admin\CategoriesController@submitCategory')->name('admin.submitcategory');
            Route::get('/admin/categorybyid/{id}', 'Admin\CategoriesController@categoryById')->name('admin.categorybyid');
            Route::post('/admin/activatecategory', 'Admin\CategoriesController@activateCategory')->name('admin.activatecategory');
            //PAKET CRUD
            
            //PAKET CRUD
            Route::get('/admin/administrative/event', 'Admin\EventController@event')->name('admin.event');
            Route::get('/admin/administrative/getallevent', 'Admin\EventController@getallEvent')->name('admin.getallevent');
            Route::get('/admin/administrative/deleteevent/{id}', 'Admin\EventController@deleteEvent')->name('admin.deleteevent');
            Route::post('/admin/administrative/submitevent', 'Admin\EventController@submitEvent')->name('admin.submitevent');
            Route::get('/admin/eventbyid/{id}', 'Admin\EventController@eventById')->name('admin.eventbyid');
            Route::post('/admin/activateevent', 'Admin\EventController@activateEvent')->name('admin.activateevent');
            //PAKET CRUD
        });
        
        Route::group(['middleware' => 'role:superadmin,admin'] , function(){ 
            
            //PAKET CRUD
            Route::post('/admin/diklat/getallnews', 'Admin\NewsController@getallnews')->name('admin.getallnews');
            Route::get('/admin/news', 'Admin\NewsController@news')->name('admin.news');
            Route::post('/admin/submitnews', 'Admin\NewsController@submitNews')->name('admin.submitnews');
            Route::get('/admin/newsbyid/{id}', 'Admin\NewsController@newsById')->name('admin.newsbyid');
            Route::get('/admin/deletenews/{id}', 'Admin\NewsController@deleteNews')->name('admin.deletenews');
            Route::post('/admin/activatenews', 'Admin\NewsController@activateNews')->name('admin.activatenews');
            Route::get('/admin/newscategories', 'Admin\NewsController@newsCategories')->name('admin.newscategories');
            //PAKET CRUD
        });
        
        Route::post('/admin/users/getallpublicusers', 'Admin\UsersManagementController@getallPublicUsers')->name('admin.getallpublicusers');
        Route::post('/admin/users/getallpublicusersregister', 'Admin\UsersManagementController@getallPublicUsersRegister')->name('admin.getallpublicusersregister');

    });
    
    Route::get('/config/account', 'Configuration@configuration')->name('panel.config');
    Route::get('/config/apperrances', 'Configuration@Apperrances')->name('panel.apperrances');
    Route::post('/config/apperrances', 'Configuration@updateYTvideo')->name('panel.updateytvideo');
    Route::get('/user-config', 'Configuration@updateUser')->name('panel.config.user');
    Route::get('/account-config', 'Configuration@updateAccount')->name('panel.config.account');
    Route::get('/getall-role', 'RolesController@getallRolesOption')->name('panel.getallrole');
    
});