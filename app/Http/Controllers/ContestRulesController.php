<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContestRulesController extends Controller
{
    public function index()
    {
        return view('contest.rules');
    }
}
