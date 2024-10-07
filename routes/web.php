<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // redirect to the admin panel

    if (Auth::guest()) {
        return view('welcome');
    }

    // if (request()->getHost() === '127.0.0.1:8000') {
    //     return redirect('/');
    // }

    // return redirect('/');

    switch (Auth::user()->role) {
        case 'admin':
            return redirect('/admin');
            break;
        case 'penyedia_jasa':
            return redirect('/penyedia-jasa');
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
            return redirect('/penyedia-jasa');
            break;
        case 'treasurer':
            return redirect('/treasurer');
            break;
        default:
            return view('welcome');

            break;
    }
});

//  Export PDF
Route::get('report/{report_model}/export/pdf', [PdfController::class, 'export'])->name('ds.report.export.pdf');
