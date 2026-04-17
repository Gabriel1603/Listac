<?php

use Illuminate\Support\Facades\Route;


function criarRota($classe)
{
    $controller = "App\\Http\\Controllers\\" . $classe . 'Controller';
    
    $rota = $classe . 's';


    Route::get("/$rota", ['uses' => "$controller@index"])->name("$rota.index");
}

criarRota("Listac");