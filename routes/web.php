<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $date = Carbon::createFromFormat('d/m/Y', '09/09/2024')->format('Y-m-d');
    dd($date);
    // return view('welcome');
});
