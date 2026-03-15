<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function features()
    {
        return view('public.features');
    }

    public function solutions()
    {
        return view('public.solutions');
    }

    public function pricing()
    {
        return view('public.pricing');
    }

    public function docs()
    {
        return view('public.docs');
    }
}
