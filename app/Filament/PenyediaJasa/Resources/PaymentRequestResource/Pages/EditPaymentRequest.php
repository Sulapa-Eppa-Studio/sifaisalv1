<?php

namespace App\Filament\PenyediaJasa\Resources\PaymentRequestResource\Pages;

use App\Filament\PenyediaJasa\Resources\PaymentRequestResource;
use Filament\Actions;
use App\Models\Contract;
use App\Models\Document;
use App\Models\PaymentRequest;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditPaymentRequest extends EditRecord
{
    protected static string $resource = PaymentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['verification_progress']      = 'ppk';
        $data['ppk_verification_status']    = 'in_progress';
        $data['ppspm_verification_status']  = 'not_available';
        $data['treasurer_verification_status'] = 'not_available';
        $data['kpa_verification_status'] = 'not_available';


        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        $data['docs'] = array_merge($data, $record->documents()->get()->pluck('path', 'name')->toArray());


        return $data;
    }


    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {

            DB::beginTransaction();

            $sp_id      =   get_auth_user()->services_provider->id;

            $contract   =   Contract::where('contract_number', $data['contract_number'])->firstOrFail();

            if ($contract->payment_stages < get_payment_stage($contract)) {
                throw new \Exception('Tahap pembayaran melebihi kontrak ' . $contract->payment_stages . " Tahap Pembayaran : " . get_payment_stage($contract));
            }

            if ($contract->payment_value - $contract->paid_value < $data['payment_value']) {
                throw new \Exception('Nilai pembayaran melebihi sisa kontrak ' . 'Rp. ' . number_format($contract->payment_value - $contract->paid_value, 0, ',', '.'));
            }

            $data['payment_stage']          =   get_payment_stage($contract);
            $data['contract_number']        =   $contract->contract_number;
            $data['service_provider_id']    =   $sp_id;

            $data['ppk_verification_status']        =   'in_progress';
            $data['ppspm_verification_status']      =   'not_available';
            $data['treasurer_verification_status']  =   'not_available';
            $data['kpa_verification_status']        =   'not_available';

            $record->update($data);

            if ($record instanceof PaymentRequest) {
                $record->documents()->delete();
            }

            foreach ($data['docs'] as $key => $path) {

                if (!$path) continue;

                Document::create([
                    'name'  =>  $key,
                    'path'  =>  $path,
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
                ->color('#c44d47') //#369663 => green, #c44d47 => red
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
