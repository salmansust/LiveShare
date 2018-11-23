<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }







    public function getLivestream(){

    	return view ('livestream');
    }

    public function getBroadcast(){

    	return view ('broadcast');
    }
 
 public function getVideocon(){

    	return view ('videocon');
    }
 public function getVideochat(){

    	return view ('videochat');

    }

    public function getContact(){

    	return view ('contact');


    }

 public function getAbout(){

    	return view ('about');

    }

public function getFeatures(){

    	return view ('features');

    }

 
 

 




}

