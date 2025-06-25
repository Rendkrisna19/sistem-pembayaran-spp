<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BendaharaController extends Controller
{
    public function index()
    {
        return view('bendahara.dashboard');
    }
}