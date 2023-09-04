<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:teacher');

        parent::__construct(); 
    }
}
