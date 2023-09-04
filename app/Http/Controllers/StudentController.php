<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');

        parent::__construct();
    }
}
