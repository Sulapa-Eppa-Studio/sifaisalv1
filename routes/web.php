<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // redirect to the admin panel
    return redirect('/admin');
});
