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
                    throw new \Exception('Terdapat pengajuan pembayaran yang belum selesai!');
                }
            }

            if ($contract->payment_stages <= get_payment_stage($contract)) {
                throw new \Exception('Tahap pembayaran melebihi kontrak ' . $contract->payment_stages . " Tahap Pembayaran : " . get_payment_stage($contract));
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
                    'path'  =>  $data['jaminan_uang_muka'],
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

                Document::create([
                    'name'  =>  'Surat Permohonan Pembayaran Tahap',
                    'path'  =>  $data['Surat Permohonan Pembayaran Tahap'],  // Ambil dari $data
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
                    'name'  =>  'Jaminan Pemeliharaan (Jika Termijn 100%)',
                    'path'  =>  $data['Jaminan Pemeliharaan (Jika Termijn 100%)'],  // Ambil dari $data
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
                    'name'  =>  'Surat Perintah Pemeriksaan Hasil Pekerjaan oleh PPK',
                    'path'  =>  $data['Surat Perintah Pemeriksaan Hasil Pekerjaan oleh PPK'],  // Ambil dari $data
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);

                Document::create([
                    'name'  =>  'Berita Acara Pemeriksaan Pekerjaan',
                    'path'  =>  $data['Berita Acara Pemeriksaan Pekerjaan'],  // Ambil dari $data
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);

                Document::create([
                    'name'  =>  'Berita Acara Prestasi Pekerjaan',
                    'path'  =>  $data['Berita Acara Prestasi Pekerjaan'],  // Ambil dari $data
                    'type'  =>  'document_by_penyedia_jasa',
                    'payment_request_id'    =>  $record->id,
                ]);
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
