<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\PaymentRequest;
use App\Models\PPK;
use App\Models\ServiceProvider;
use App\Models\TermintSppPpk;
use App\Models\User;
use App\Models\WorkPackage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{

    public function export(Request $request)
    {
        try {
            $report_model = $request->report_model;

            return match ($report_model) {
                "user_report" => $this->handle_user_report($request),
                "contract_report" => $this->handle_contract_report($request),
                "payment_request_report" => $this->handle_payment_request_report($request),
                "spp_report" => $this->handle_termint_spp_ppks_report($request),
                "workpackage_report" => $this->handle_work_package_report($request),
            };
        } catch (\Throwable $th) {

            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function handle_user_report(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date'   => 'nullable|date|date_format:Y-m-d|after:start_date',
        ]);

        $start_date = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $end_date = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');

        $period_dates = [$start_date, $end_date];

        // Menghitung jumlah pengguna berdasarkan role
        $admin_count = User::where('role', 'admin')->count();
        $kpa_count = User::where('role', 'kpa')->count();
        $ppk_count = User::where('role', 'ppk')->count();
        $spm_count = User::where('role', 'spm')->count();
        $penyedia_jasa_count = User::where('role', 'penyedia_jasa')->count();
        $bendahara_count = User::where('role', 'bendahara')->count();

        // Data pengguna berdasarkan role
        $users_by_role = [
            'Admin' => User::where('role', 'admin')->select('id', 'name', 'email')->get()->toArray(),
            'KPA' => User::where('role', 'kpa')->select('id', 'name', 'email')->get()->toArray(),
            'PPK' => User::where('role', 'ppk')->select('id', 'name', 'email')->get()->toArray(),
            'SPM' => User::where('role', 'spm')->select('id', 'name', 'email')->get()->toArray(),
            'Penyedia Jasa' => User::where('role', 'penyedia_jasa')->select('id', 'name', 'email')->get()->toArray(),
            'Bendahara' => User::where('role', 'bendahara')->select('id', 'name', 'email')->get()->toArray(),
        ];

        // Data pengguna secara total
        $data_user = [
            'Total Admin'       => $admin_count,
            'Total KPA'         => $kpa_count,
            'Total PPK'         => $ppk_count,
            'Total SPM'         => $spm_count,
            'Total Penyedia Jasa' => $penyedia_jasa_count,
            'Total Bendahara'   => $bendahara_count,
        ];

        // Tabel jumlah pengguna per role
        $table_data = [
            ['role' => 'Admin', 'jumlah' => $admin_count],
            ['role' => 'KPA', 'jumlah' => $kpa_count],
            ['role' => 'PPK', 'jumlah' => $ppk_count],
            ['role' => 'SPM', 'jumlah' => $spm_count],
            ['role' => 'Penyedia Jasa', 'jumlah' => $penyedia_jasa_count],
            ['role' => 'Bendahara', 'jumlah' => $bendahara_count],
        ];

        // Menyusun data tabel dinamis berdasarkan role
        $tables = [
            'Ringkasan Jumlah Pengguna' => [
                "kolom" => ['Role', 'Jumlah'],
                "data"  => array_map(function ($item) {
                    return [$item['role'], $item['jumlah']];
                }, $table_data),
            ]
        ];

        // Menambahkan tabel untuk setiap role
        foreach ($users_by_role as $role => $users) {
            $tables["Daftar Pengguna - $role"] = [
                "kolom" => ['ID', 'Nama', 'Email'],
                "data"  => array_map(function ($user) {
                    return [$user['id'], $user['name'], $user['email']];
                }, $users),
            ];
        }

        // Data laporan yang akan di-passing ke view PDF
        $data = [
            'title'     =>  'Laporan Pengguna Periode ' . Carbon::parse($start_date)->format('d M Y') . ' - ' . Carbon::parse($end_date)->format('d M Y'),
            'content'   =>  [
                'Jumlah Pengguna Berdasarkan Role' =>  $data_user,
            ],
            'tables'    =>  $tables
        ];

        $pdf = Pdf::loadView("components.reports.report-layout", $data);



        return $pdf->download("user_report.pdf");
    }

    public function handle_contract_report(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date'   => 'nullable|date|date_format:Y-m-d|after:start_date',
        ]);

        $start_date = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $end_date = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');

        $period_dates = [$start_date, $end_date];

        // Mendapatkan data kontrak dan relasi
        $contracts = Contract::with('service_provider', 'admin', 'ppk')->get();

        // Data tabel kontrak beserta relasinya
        $contract_table_data = $contracts->map(function ($contract) {
            return [
                'ID' => $contract->id,
                'No. Kontrak' => $contract->contract_number,
                'Paket Pekerjaan' => $contract->work_package,
                'Nilai Kontrak' => number_format($contract->payment_value, 0, ',', '.'),
                'Penyedia Jasa' => $contract->service_provider->full_name ?? 'Tidak Ada',
                'Admin' => $contract->admin->name ?? 'Tidak Ada',
                'PPK' => $contract->ppk->full_name ?? 'Tidak Ada',
                'Tgl Kontrak' => Carbon::parse($contract->contract_date)->format('d M Y'),
            ];
        })->toArray();



        // Menyusun data tabel dinamis berdasarkan role
        $tables = [
            'Ringkasan Kontrak' => [
                "kolom" => ['ID', 'No. Kontrak', 'Paket Pekerjaan', 'Nilai Kontrak', 'Penyedia Jasa', 'Admin', 'PPK', 'Tgl Kontrak'],
                "data"  => $contract_table_data,
            ],
        ];

        // Data laporan yang akan di-passing ke view PDF
        $data = [
            'title'     =>  'Laporan Kontrak dan Penyedia Jasa Periode ' . Carbon::parse($start_date)->format('d M Y') . ' - ' . Carbon::parse($end_date)->format('d M Y'),
            'content'   =>  [],
            'tables'    =>  $tables
        ];

        $pdf = Pdf::loadView("components.reports.report-layout", $data);

        return $pdf->download("contract_report.pdf");
    }

    public function handle_payment_request_report(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date'   => 'nullable|date|date_format:Y-m-d|after:start_date',
        ]);

        $start_date = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $end_date = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');

        $period_dates = [$start_date, $end_date];

        // Mengambil data permintaan pembayaran berdasarkan periode

        $user = Auth::user();
        if ($user->role === 'admin') {

            $payment_requests = PaymentRequest::with(['service_provider', 'ppk', 'spm', 'treasurer', 'kpa', 'contract'])->get();
        } else {
            $providerId = ServiceProvider::where('user_id', $user->id)->first()->id;

            $payment_requests = PaymentRequest::with(['service_provider', 'ppk', 'spm', 'treasurer', 'kpa', 'contract'])->where('service_provider_id', $providerId)->get();
        }

        // Menyusun data tabel untuk laporan PDF
        $payment_request_table_data = $payment_requests->map(function ($payment_request) {
            return [
                'No. Kontrak' => $payment_request->contract_number,
                'No. Permintaan' => $payment_request->request_number,
                'Tahap Pembayaran' => $payment_request->payment_stage,
                'Nilai Pembayaran' => number_format($payment_request->payment_value, 0, ',', '.'),
                'Penyedia Jasa' => $payment_request->service_provider->full_name ?? 'Tidak Ada',
                'Status Verifikasi PPK' => ucfirst($payment_request->ppk_verification_status),
                'Status Verifikasi KPA' => ucfirst($payment_request->kpa_verification_status),
                'Tanggal Dibuat' => Carbon::parse($payment_request->created_at)->format('d M Y'),
            ];
        })->toArray();

        // Data tabel untuk laporan PDF
        $tables = [
            'Laporan Permintaan Pembayaran (Ringkasan)' => [
                "kolom" => [
                    'No. Kontrak',
                    'No. Permintaan',
                    'Tahap Pembayaran',
                    'Nilai Pembayaran',
                    'Penyedia Jasa',
                    'Status Verifikasi PPK',
                    'Status Verifikasi KPA',
                    'Tanggal Dibuat'
                ],
                "data"  => $payment_request_table_data,
            ],
        ];

        // Menyusun data laporan yang akan dipassing ke view PDF
        $data = [
            'title'     => 'Laporan Permintaan Pembayaran Periode ' . Carbon::parse($start_date)->format('d M Y') . ' - ' . Carbon::parse($end_date)->format('d M Y'),
            'content'   => [],
            'tables'    => $tables
        ];

        $pdf = Pdf::loadView("components.reports.report-layout", $data);

        return $pdf->download("payment_request_report.pdf");
    }


    public function handle_termint_spp_ppks_report(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date'   => 'nullable|date|date_format:Y-m-d|after:start_date',
        ]);

        $start_date = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $end_date = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');

        $period_dates = [$start_date, $end_date];

        // Mengambil data dari termint_spp_ppks berdasarkan periode
        $termint_spp_ppks = TermintSppPpk::with(['contract', 'spm', 'user'])->get();

        // Menyusun data tabel dengan informasi penting saja
        $termint_table_data = $termint_spp_ppks->map(function ($termint) {
            return [
                'No. Termint' => $termint->no_termint,
                'Deskripsi' => $termint->description,
                'Nilai Pembayaran' => number_format($termint->payment_value, 0, ',', '.'),
                'Pembayaran Uang Muka' => $termint->has_advance_payment ? 'Ya' : 'Tidak',
                'No. Kontrak' => $termint->contract->contract_number ?? 'Tidak Ada',
                'Pengguna' => $termint->user->name ?? 'Tidak Ada',
                'Status Verifikasi PPSPM' => ucfirst($termint->ppspm_verification_status),
                'Tanggal Dibuat' => Carbon::parse($termint->created_at)->format('d M Y'),
            ];
        })->toArray();

        // Data tabel untuk laporan PDF
        $tables = [
            'Laporan Surat Permohonan Pembayaran (Ringkasan)' => [
                "kolom" => [
                    'No. Termint',
                    'Deskripsi',
                    'Nilai Pembayaran',
                    'Pembayaran Uang Muka',
                    'No. Kontrak',
                    'Pengguna',
                    'Status Verifikasi PPSPM',
                    'Tanggal Dibuat'
                ],
                "data"  => $termint_table_data,
            ],
        ];

        // Menyusun data laporan yang akan dipassing ke view PDF
        $data = [
            'title'     => 'Laporan Termint SPP PPK Periode ' . Carbon::parse($start_date)->format('d M Y') . ' - ' . Carbon::parse($end_date)->format('d M Y'),
            'content'   => [],
            'tables'    => $tables
        ];

        $pdf = Pdf::loadView("components.reports.report-layout", $data);

        return $pdf->download("termint_spp_ppk_report.pdf");
    }

    public function handle_work_package_report(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date'   => 'nullable|date|date_format:Y-m-d|after:start_date',
        ]);

        $start_date = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $end_date = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');

        $period_dates = [$start_date, $end_date];

        // Mengambil data paket pekerjaan (work packages)
        $work_packages = WorkPackage::all();

        // Menyusun data tabel dengan informasi penting saja
        $work_package_table_data = $work_packages->map(function ($work_package) {
            return [
                'ID' => $work_package->id,
                'Nama Paket' => $work_package->name,
                'Tanggal Dibuat' => Carbon::parse($work_package->created_at)->format('d M Y'),
            ];
        })->toArray();

        // Data tabel untuk laporan PDF
        $tables = [
            'Laporan Paket Pekerjaan (Ringkasan)' => [
                "kolom" => [
                    'ID',
                    'Nama Paket',
                    'Tanggal Dibuat'
                ],
                "data"  => $work_package_table_data,
            ],
        ];

        // Menyusun data laporan yang akan dipassing ke view PDF
        $data = [
            'title'     => 'Laporan Paket Pekerjaan Periode ' . Carbon::parse($start_date)->format('d M Y') . ' - ' . Carbon::parse($end_date)->format('d M Y'),
            'content'   => [],
            'tables'    => $tables
        ];

        $pdf = Pdf::loadView("components.reports.report-layout", $data);

        return $pdf->download("work_package_report.pdf");
    }
}
