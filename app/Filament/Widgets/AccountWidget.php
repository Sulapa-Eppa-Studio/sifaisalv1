<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AccountWidget extends Widget
{
    protected static string $view = 'filament.widgets.account-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getHeading(): string
    {
        return 'Selamat Datang, ' . Auth::user()->name . '!';
    }



    protected function getSubheading(): string
    {
        return 'SiFaisal ( Sistem Informasi Verifikasi Pertanggungjawaban Anggaran Kegiatan Yang Dilaksanakan Secara Kontraktual )';
    }

    public $notifications;

    public function mount()
    {
        // Contoh: Mengambil data notifikasi pengguna
        $this->notifications = Auth::user()->unreadNotifications;
    }
}
