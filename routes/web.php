<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // redirect to the admin panel

    if (Auth::guest()) {
        return redirect('/admin/login');
    }

    switch (Auth::user()->role) {
        case 'admin':
            return redirect('/admin');
            break;
        case 'penyedia_jasa':
            return redirect('/penyediaJasa');
            break;
        case 'ppk':
            return redirect('/ppk');
            break;

        case 'kpa':
            return redirect('/kpa');
            break;
        case 'spm':
            return redirect('/spm');
            break;
        case 'service_provider':
            return redirect('/serviceProvider');
            break;
        case 'treasurer':
            return redirect('/treasurer');
            break;
        default:
            return redirect('/login');
            break;
    }
});
