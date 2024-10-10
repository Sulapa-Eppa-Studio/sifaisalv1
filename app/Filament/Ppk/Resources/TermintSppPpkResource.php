<?php

namespace App\Filament\Ppk\Resources;

use App\Filament\Ppk\Resources\TermintSppPpkResource\Pages;
use App\Filament\Ppk\Resources\TermintSppPpkResource\RelationManagers;
use App\Models\TermintSppPpk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use App\Enums\FileType;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ButtonAction;
use Illuminate\Database\Eloquent\Model;

class TermintSppPpkResource extends Resource
{
    protected static ?string $model = TermintSppPpk::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationLabel = 'SPP';

    protected static ?string $label = 'Surat Permohonan Pembayaran (SPP)';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Menu Utama';

    public static function canEdit(Model $record): bool
    {
        return $record->ppspm_verification_status == 'rejected';
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('contract_id')
                    ->relationship('contract', 'contract_number')
                    ->required()
                    ->searchable()
                    ->label('Nomor Kontrak')
                    ->options(get_my_contracts_for_options_by_ppk()),
                Forms\Components\TextInput::make('no_termint')
                    ->label('Nomor SPP')
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->label('Uraian Pembayaran SPP-PPK')
                    ->required(),
                Forms\Components\TextInput::make('payment_value')
                    ->label('Nilai Permintaan Pembayaran')
                    ->required()
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                    ->prefix('Rp'),
                Forms\Components\Toggle::make('has_advance_payment')
                    ->label('Uang Muka')
                    ->disabledOn('edit')
                    ->reactive(),
                Forms\Components\Fieldset::make('Pilih Dokumen Yang Akan Diunggah')
                    ->schema(function (Forms\Components\Component $component) {
                        return self::getDocumentFields($component->getState()['has_advance_payment'] ?? false);
                    })
                    ->columns(2),
            ]);
    }

    protected static function getDocumentFields(bool $hasAdvancePayment): array
    {
        $pdfValidation = [
            'acceptedFileTypes' => ['application/pdf'],
            'maxSize' => 2048, // 2MB
        ];

        if ($hasAdvancePayment) {
            return [
                Forms\Components\FileUpload::make('files.' . FileType::SURAT_PERMOHONAN_PEMBAYARAN_UANG_MUKA->value)
                    ->label('Surat Permohonan Pembayaran Uang Muka')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::RINCIAN_PENGGUNAAN_UANG_MUKA->value)
                    ->label('Rincian Penggunaan Uang Muka')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::SPTJB->value)
                    ->label('SPTJB')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::BA_SERAH_TERIMA_SAKTI->value)
                    ->label('BA. Serah Terima dari Aplikasi SAKTI')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::KUITANSI->value)
                    ->label('Kuitansi')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::BERITA_ACARA_PEMBAYARAN->value)
                    ->label('Berita Acara Pembayaran')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::BUKTI_PAJAK->value)
                    ->label('Bukti Pajak')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::JAMINAN_UANG_MUKA->value)
                    ->label('Jaminan Uang Muka')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::KARWAS_SAKTI->value)
                    ->label('Karwas dari Aplikasi SAKTI')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::SPP->value)
                    ->label('SPP')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::RINGKASAN_KONTRAK->value)
                    ->label('Ringkasan Kontrak')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
            ];
        } else {
            return [
                Forms\Components\FileUpload::make('files.' . FileType::KARWAS->value)
                    ->label('Karwas')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::KUITANSI->value)
                    ->label('Kuitansi')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::BAPP_BAST->value)
                    ->label('BAPP / BAST')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::SPP->value)
                    ->label('SPP')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::BA_SERAH_TERIMA->value)
                    ->label('BA Serah Terima')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::SURAT_PERMOHONAN_PENYEDIA_JASA->value)
                    ->label('Surat Permohonan dari Penyedia Jasa')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::BAP->value)
                    ->label('BAP')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::RINGKASAN_KONTRAK->value)
                    ->label('Ringkasan Kontrak')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::BUKTI_PEMBAYARAN->value)
                    ->label('Surat Setoran Pajak (SSP)')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::SPTJB->value)
                    ->label('SPTJB')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
            ];
        }
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract.contract_number')
                    ->label('Kontrak'),

                Tables\Columns\TextColumn::make('no_termint')
                    ->label('Nomor SPP'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->toggleable(isToggledHiddenByDefault: true)->wrap(),

                Tables\Columns\TextColumn::make('ppspm_verification_status')
                    ->label('Status Verifikasi')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('payment_value')
                    ->label('Nilai Pembayaran')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->money('IDR', true)
                    ->sortable(),

                Tables\Columns\BooleanColumn::make('has_advance_payment')
                    ->label('Uang Muka'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('viewFiles')
                    ->label('Lihat File')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn(TermintSppPpk $record): string => "Daftar File untuk {$record->no_termint}")
                    ->modalWidth('6xl')
                    ->modalContent(function (TermintSppPpk $record) {
                        return view('livewire.view-files-modal', ['record' => $record]);
                    })
                    ->modalActions([
                        ButtonAction::make('close')
                            ->label('Tutup')
                            ->close(),
                    ]),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Definisikan relasi jika diperlukan
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTermintSppPpks::route('/'),
            'create' => Pages\CreateTermintSppPpk::route('/create'),
            'edit' => Pages\EditTermintSppPpk::route('/{record}/edit'),
        ];
    }
}
