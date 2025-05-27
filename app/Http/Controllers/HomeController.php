<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    //
    public function index()
    {
        // $districts = json_decode(
        //     file_get_contents(storage_path('app/tainan_districts.json')),
        //     true
        // );

        // return view('home')->with([
        //     'districts' => $districts,
        // ]);
    }

    public function getList()
    {
        $districts = json_decode(
            file_get_contents(storage_path('app/tainan_districts.json')),
            true
        );

        return view('list')->with([
            'districts' => $districts,
        ]);
    }
}
