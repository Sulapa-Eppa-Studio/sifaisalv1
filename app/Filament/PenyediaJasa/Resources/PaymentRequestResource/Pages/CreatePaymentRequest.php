<?php

namespace App\Filament\PenyediaJasa\Resources\PaymentRequestResource\Pages;

use App\Filament\PenyediaJasa\Resources\PaymentRequestResource;
use App\Models\Contract;
use App\Models\Document;
use App\Models\PaymentRequest;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreatePaymentRequest extends CreateRecord
{
    protected static string $resource = PaymentRequestResource::class;

    protected function afterFill(): void
    {
        // Runs after the form fields are populated with their default values.
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        try {

            DB::beginTransaction();

            $sp_id      =   get_auth_user()->services_provider->id;

            $contract   =   Contract::where('contract_number', $data['contract_number'])->firstOrFail();

            $payment_lists  =   PaymentRequest::where('contract_number', $contract->contract_number)->get();

            foreach ($payment_lists as $value) {
                if ($value->verification_progress != 'done') {
                    throw new \Exception( ucwords(strtolower('TERDAPAT PENGAJUAN PEMBAYARAN TAHAP SEBELUMNYA YANG BELUM TERPROSES!!')));
                }
            }

            if ($contract->payment_stages < get_payment_stage($contract)) {
                throw new \Exception('Tahap pembayaran melebihi kontrak ' . $contract->payment_stages . " Tahap Pembayaran : " . get_payment_stage($contract));
            }

            if($contract->payment_value - $contract->paid_value < $data['payment_value']) {
                throw new \Exception('Nilai pembayaran melebihi sisa kontrak ' . 'Rp. ' . number_format($contract->payment_value - $contract->paid_value, 0, ',', '.'));
            }

            $data['payment_stage']          =   get_payment_stage($contract);
            $data['contract_number']        =   $contract->contract_number;
            $data['service_provider_id']    =   $sp_id;
            $data['ppk_id']                 =   $contract->ppk_id;

            $record = new ($this->getModel())($data);

            $record->save();

            if (cek_pembayaran_pertama($contract)) {

                // insert documents
                Document::create([
                    'name'  =>  'Surat Permohonan Pembayaran Uang Muka',
                    'path'  =>  $data['Surat Permohonan Pembayaran Uang Muka'],
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);

                Document::create([
                    'name'  =>  'Rincian Penggunaan Uang Muka',
                    'path'  =>  $data['Rincian Penggunaan Uang Muka'],
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);

                Document::create([
                    'name'  =>  'Rekening Koran',
                    'path'  =>  $data['Rekening Koran'],
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);

                Document::create([
                    'name'  =>  'npwp',
                    'path'  =>  $data['npwp'],
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);


                Document::create([
                    'name'  =>  'E-Faktur',
                    'path'  =>  $data['E-Faktur'],
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);

                Document::create([
                    'name'  =>  'Jaminan Uang Muka',
                    'path'  =>  $data['Jaminan Uang Muka'],
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);

                Document::create([
                    'name'  =>  'Surat Keabsahan Dan Kebenaran Jaminan Uang Muka',
                    'path'  =>  $data['Surat Keabsahan Dan Kebenaran Jaminan Uang Muka'],
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);
            } else {

                // Required documents (langsung dibuat tanpa isset)
                Document::create([
                    'name'  =>  'Surat Permohonan Pembayaran',
                    'path'  =>  $data['Surat Permohonan Pembayaran'],  // Ambil dari $data
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);

                Document::create([
                    'name'  =>  'Rekening Koran',
                    'path'  =>  $data['Rekening Koran'],  // Ambil dari $data
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);

                Document::create([
                    'name'  =>  'NPWP',
                    'path'  =>  $data['NPWP'],  // Ambil dari $data
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);

                Document::create([
                    'name'  =>  'E-Faktur',
                    'path'  =>  $data['E-Faktur'],  // Ambil dari $data
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);

                Document::create([
                    'name'  =>  'Surat Permohonan Penerimaan Hasil Pekerjaan',
                    'path'  =>  $data['Surat Permohonan Penerimaan Hasil Pekerjaan'],  // Ambil dari $data
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);

                Document::create([
                    'name'  =>  'Surat Perintah Pemeriksaan Hasil Pekerjaan',
                    'path'  =>  $data['Surat Perintah Pemeriksaan Hasil Pekerjaan'],  // Ambil dari $data
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);

                Document::create([
                    'name'  =>  'Berita Acara Pemeriksaan Pekerjaan',
                    'path'  =>  $data['Berita Acara Pemeriksaan Pekerjaan'],  // Ambil dari $data
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);
                if (isset($data['Berita Acara Prestasi Pekerjaan'])) {
                    Document::create([
                        'name'  =>  'Berita Acara Prestasi Pekerjaan',
                        'path'  =>  $data['Berita Acara Prestasi Pekerjaan'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Gambar Kerja'])) {
                    Document::create([
                        'name'  =>  'Gambar Kerja',
                        'path'  =>  $data['Gambar Kerja'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Spesifikasi Teknis'])) {
                    Document::create([
                        'name'  =>  'Spesifikasi Teknis',
                        'path'  =>  $data['Spesifikasi Teknis'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Backup Perhitungan Kuantitas'])) {
                    Document::create([
                        'name'  =>  'Backup Perhitungan Kuantitas',
                        'path'  =>  $data['Backup Perhitungan Kuantitas'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Form Penerimaan Hasil Pekerjaan'])) {
                    Document::create([
                        'name'  =>  'Form Penerimaan Hasil Pekerjaan',
                        'path'  =>  $data['Form Penerimaan Hasil Pekerjaan'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Peralatan Pemeriksaan'])) {
                    Document::create([
                        'name'  =>  'Peralatan Pemeriksaan',
                        'path'  =>  $data['Peralatan Pemeriksaan'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Daftar Hadir'])) {
                    Document::create([
                        'name'  =>  'Daftar Hadir',
                        'path'  =>  $data['Daftar Hadir'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Laporan Hasil Pengujian Kualitas'])) {
                    Document::create([
                        'name'  =>  'Laporan Hasil Pengujian Kualitas',
                        'path'  =>  $data['Laporan Hasil Pengujian Kualitas'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Laporan Kemajuan Pekerjaan'])) {
                    Document::create([
                        'name'  =>  'Laporan Kemajuan Pekerjaan',
                        'path'  =>  $data['Laporan Kemajuan Pekerjaan'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Sertifikat Bulanan'])) {
                    Document::create([
                        'name'  =>  'Sertifikat Bulanan',
                        'path'  =>  $data['Sertifikat Bulanan'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Laporan Bulanan'])) {
                    Document::create([
                        'name'  =>  'Laporan Bulanan',
                        'path'  =>  $data['Laporan Bulanan'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                // Nullable documents (hanya dibuat jika ada)
                if (isset($data['Jaminan Pemeliharaan (Jika Termijn 100%)'])) {
                    Document::create([
                        'name'  =>  'Jaminan Pemeliharaan (Jika Termijn 100%)',
                        'path'  =>  $data['Jaminan Pemeliharaan (Jika Termijn 100%)'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Laporan Bulanan ( Jika Konsultan )'])) {
                    Document::create([
                        'name'  =>  'Laporan Bulanan ( Jika Konsultan )',
                        'path'  =>  $data['Laporan Bulanan ( Jika Konsultan )'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Laporan Antara'])) {
                    Document::create([
                        'name'  =>  'Laporan Antara (Jika Konsultan)',
                        'path'  =>  $data['Laporan Antara'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Laporan Akhir'])) {
                    Document::create([
                        'name'  =>  'Laporan Akhir (Jika Konsultan)',
                        'path'  =>  $data['Laporan Akhir'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Backup Invoice'])) {
                    Document::create([
                        'name'  =>  'Backup Invoice (Jika Konsultan)',
                        'path'  =>  $data['Backup Invoice'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }

                if (isset($data['Dokumen Lainnya'])) {
                    Document::create([
                        'name'  =>  'Dokumen Lainnya yang dipersyaratkan dalam kontrak',
                        'path'  =>  $data['Dokumen Lainnya'],  // Ambil dari $data
                        'type'  =>  'document_by_penyedia_jasa',
                        'payment_request_id'    =>  $record->id,
                    ]);
                }
            }


            DB::commit();

            return $record;
        } catch (\Throwable $th) {

            DB::rollBack();

            Notification::make()
                ->title('Terjadi Keslaahan')
                ->body($th->getMessage())
                ->send();

            $this->halt();
        }
    }


    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        return $resource::getUrl('index');
    }
}
