<?php

namespace App\Controllers;

class Empanadas extends BaseController
{
    public function index()
    {
        return view('empanadas_list');
    }
}
