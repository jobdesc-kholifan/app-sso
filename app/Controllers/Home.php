<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data['title'] = "Landing Page";
        $data['pageTitle'] = "Landing Page";

        return view('landing', $data);
    }
}
