<?php

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



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');  

Route::get('/', 'HomeController@index')->name('home');
 

Route::group(array('before' => 'auth'), function() 
{
    Route::get('user/logout', array(
        'uses' => 'UserController@doLogout',
    ));

   

    Route::get('videocon',  array(
        'uses' => 'PagesController@getVideocon',
    ));

  	 Route::get('videochat',  array(
        'uses' => 'PagesController@getVideochat',
    ));

  	  Route::get('livestream',  array(
        'uses' => 'PagesController@getLivestream',
    ));

  	   Route::get('broadcast',  array(
        'uses' => 'PagesController@getBroadcast',
    ));

  	      Route::get('about',  array(
        'uses' => 'PagesController@getAbout',
    ));

  	         Route::get('contact',  array(
        'uses' => 'PagesController@getContact',
    ));
  	            Route::get('features',  array(
        'uses' => 'PagesController@getFeatures',
    ));
});







// Route::get('/', function () {
//     return view('home');
// });
// Route::get('/about', function () {
//     return view('about');
// });
// Route::get('/contact', function () {
//     return view('contact');
// });

// Route::get('/features', function () {
//     return view('features');
// });

