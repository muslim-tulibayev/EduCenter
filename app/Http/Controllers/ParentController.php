<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class ParentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:parent');
        
        parent::__construct();
    }
}
