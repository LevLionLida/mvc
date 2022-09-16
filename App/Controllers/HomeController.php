<?php

namespace App\Controllers;

use Core\Controller;
use Core\Router;

class HomeController extends Controller
{
    public function index(int $id)
    {
        d($id);
    }


}