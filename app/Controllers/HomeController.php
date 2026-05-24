<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index(): string
    {
        $data['title'] = "Landing Page";
        $data['pageTitle'] = "Landing Page";

        return view('landing', $data);
    }
}
