<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AllocationCalculatorController extends Controller
{
    public function index()
    {
        return view('kalkulator');
    }
}