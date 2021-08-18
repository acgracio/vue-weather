<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/weather', function () {
    $apiKey = config('services.openweathermap.key');
    $lat = request('lat');
    $long = request('long');

    //$response = Http::get("http://api.openweathermap.org/data/2.5/forecast?id=$apiKey");
    $response = Http::get("http://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$long&id=$apiKey&units=metric");
    
    //$response = Http::get("http://api.openweathermap.org/data/2.5/forecast?id=$apiKey");


    return $response->json();
});

Route::get('/forecast', function () {
    $apiKey = config('services.openweathermap.key');
    $lat = request('lat');
    $long = request('long');

    $response = Http::get("https://api.openweathermap.org/data/2.5/onecall?lat=$lat&lon=$long&exclude=minutely,hourly&id=$apiKey&units=metric");
    
    return $response->json();
});
