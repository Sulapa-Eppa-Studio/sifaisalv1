<?php

namespace App\Filament\Kpa\Resources\SPMRequestAprovalResource\Pages;

use App\Filament\Kpa\Resources\SPMRequestAprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSPMRequest extends ViewRecord
{
    protected static string $resource = SPMRequestAprovalResource::class;


    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        $ppk_request        =   $record->ppk_request;
        $payment_request    =   $record->payment_request;

        $docs_ppk   =   $ppk_request->files;
        $docs_pymnt =   $payment_request->documents;

        $docs   =   [
            [
                'name'  =>  strtoupper('Surat Perintah Membayar'),
                'path'  =>  $record->spm_document,
            ]
        ];

        foreach ($docs_ppk as $value) {
            $docs[] = [
                'name'  =>  strtoupper(str_replace('_', ' ', $value->file_type)),
                'path'  =>  $value->file_path,
            ];
        }

        foreach ($docs_pymnt as $value) {
            $docs[] = [
                'name'  =>  strtoupper($value->name),
                'path'  =>  $value->path,
            ];
        }


        $data['docs']   =   $docs;

        return $data;
    }
}
