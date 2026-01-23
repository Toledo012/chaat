<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeptoViewController extends Controller
{
        public function dashboard()
    {
        return view('departamento.dashboard');
    }
    
    
    }
