<?php

namespace App\Filament\Ppk\Resources;

use App\Filament\Ppk\Resources\PaymentRequestResource\Pages;
use App\Filament\Ppk\Resources\PaymentRequestResource\RelationManagers;
use App\Models\Contract;
use App\Models\Document;
use App\Models\PaymentRequest;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Joaopaulolndev\FilamentPdfViewer\Forms\Components\PdfViewerField;
use Symfony\Component\Yaml\Inline;

class PaymentRequestResource extends Resource
{
    protected static ?string $model = PaymentRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $label = 'Verifikasi Pengajuan';

    protected static ?string $navigationGroup = 'Menu Utama';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([

                TextInput::make('contract.contract_number')
                    ->required(),

                TextInput::make('request_number')
                    ->required()
                    ->label('Nomor Permintaan')
                    ->maxLength(255),

                TextInput::make('payment_value')
                    ->string()
                    ->required()
                    ->prefix('Rp. ')
                    ->stripCharacters(',')
                    ->columnSpanFull()
                    ->label('Nilai Pembayaran')
                    ->mask(RawJs::make('$money($input)')),

                Textarea::make('payment_description')
                    ->required()
                    ->columnSpanFull()
                    ->label('Deskripsi Pembayaran'),

                Fieldset::make('Dokumen Pendukung Untuk Pembayaran Uang Muka')
                    ->label('Dokumen')
                    ->hidden(function (Get $get) {

                        $number = $get('contract_number');

                        $ctx = Contract::find($number);

                        return $ctx?->advance_payment == true ? false : true;
                    })
                    ->visibleOn(['create', 'edit'])
                    ->columns(3)
                    ->schema([

                        FileUpload::make('Surat Permohonan Pembayaran Uang Muka')
                            ->directory('documents')
                            ->uploadingMessage('Upload dokumen...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->minSize(12)
                            ->maxSize(1024 * 12),

                        FileUpload::make('Rekening Koran')
                            ->directory('documents')
                            ->uploadingMessage('Upload dokumen...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->minSize(12)
                            ->required()
                            ->maxSize(1024 * 12),

                        FileUpload::make('npwp')
                            ->label('NPWP')
                            ->directory('documents')
                            ->uploadingMessage('Upload dokumen...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->minSize(12)
                            ->required()
                            ->maxSize(1024 * 12),

                        FileUpload::make('E-Faktur')
                            ->directory('documents')
                            ->uploadingMessage('Upload dokumen...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->minSize(12)
                            ->required()
                            ->maxSize(1024 * 12),

                        FileUpload::make('Surat Keabsahan Dan Kebenaran Jaminan Uang Muka')
                            ->directory('documents')
                            ->uploadingMessage('Upload dokumen...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->minSize(12)
                            ->required()
                            ->maxSize(1024 * 12),
                    ]),


                Fieldset::make('Dokumen Pendukung')
                    ->label('Dokumen')
                    ->columns(3)
                    ->hidden(function (Get $get) {
                        $number = $get('contract_number');

                        if (!$number) return true;

                        $ctx = Contract::find($number);

                        return $ctx?->advance_payment == true ? true : false;
                    })
                    ->visibleOn(['create', 'edit'])
                    ->schema([

                        FileUpload::make('Surat Permohonan Pembayaran Tahap')
                            ->label('Surat Permohonan Pembayaran Tahap')
                            ->directory('documents')
                            ->uploadingMessage('Upload dokumen pembayaran tahap...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->minSize(12) // ukuran file minimum dalam kilobytes
                            ->maxSize(1024 * 12), // ukuran file maksimum dalam kilobytes (12 MB)

                        FileUpload::make('Rekening Koran')
                            ->label('Rekening Koran')
                            ->directory('documents')
                            ->uploadingMessage('Upload Rekening Koran...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->minSize(12)
                            ->maxSize(1024 * 12),

                        FileUpload::make('NPWP')
                            ->label('NPWP')
                            ->directory('documents')
                            ->uploadingMessage('Upload NPWP...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->minSize(12)
                            ->maxSize(1024 * 12),

                        FileUpload::make('E-Faktur')
                            ->label('E-Faktur')
                            ->directory('documents')
                            ->uploadingMessage('Upload E-Faktur...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->minSize(12)
                            ->maxSize(1024 * 12),

                        FileUpload::make('Jaminan Pemeliharaan (Jika Termijn 100%)')
                            ->label('Jaminan Pemeliharaan (Jika Termijn 100%)')
                            ->directory('documents')
                            ->uploadingMessage('Upload Jaminan Pemeliharaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->nullable() // opsional, karena hanya diperlukan jika term sudah mencapai 100%
                            ->minSize(12)
                            ->maxSize(1024 * 12),

                        FileUpload::make('Surat Permohonan Penerimaan Hasil Pekerjaan')
                            ->label('Surat Permohonan Penerimaan Hasil Pekerjaan')
                            ->directory('documents')
                            ->uploadingMessage('Upload Surat Permohonan Penerimaan Hasil Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->minSize(12)
                            ->maxSize(1024 * 12),

                        FileUpload::make('Surat Perintah Pemeriksaan Hasil Pekerjaan oleh PPK')
                            ->label('Surat Perintah Pemeriksaan Hasil Pekerjaan oleh PPK')
                            ->directory('documents')
                            ->uploadingMessage('Upload Surat Perintah Pemeriksaan Hasil Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->minSize(12)
                            ->maxSize(1024 * 12),

                        FileUpload::make('Berita Acara Pemeriksaan Pekerjaan')
                            ->label('Berita Acara Pemeriksaan Pekerjaan')
                            ->directory('documents')
                            ->uploadingMessage('Upload Berita Acara Pemeriksaan Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->minSize(12)
                            ->maxSize(1024 * 12),

                        FileUpload::make('Berita Acara Prestasi Pekerjaan')
                            ->label('Berita Acara Prestasi Pekerjaan')
                            ->directory('documents')
                            ->uploadingMessage('Upload Berita Acara Prestasi Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->minSize(12)
                            ->maxSize(1024 * 12),
                    ]),



                Fieldset::make('Data Kontrak')
                    ->relationship('contract')
                    ->visibleOn('view')
                    ->schema([

                        TextInput::make('contract_number')
                            ->label('Nomor Kontrak')
                            ->required()
                            ->maxLength(255),

                        DatePicker::make('contract_date')
                            ->label('Tanggal Kontrak')
                            ->required(),

                        TextInput::make('can_number')
                            ->label('Nomor CAN')
                            ->nullable(),

                        TextInput::make('work_package')
                            ->label('Paket Pekerjaan')
                            ->required(),

                        TextInput::make('execution_time')
                            ->label('Masa Pelaksanaan (Hari Kalender)')
                            ->numeric()
                            ->suffix('Hari')
                            ->required(),

                        Select::make('advance_payment')
                            ->label('Pemberian Uang Muka')
                            ->options([
                                true => 'Ya',
                                false => 'Tidak',
                            ])
                            ->required(),

                        TextInput::make('payment_stages')
                            ->label('Jumlah Tahap Pembayaran')
                            ->numeric()
                            ->prefix('Tahap')
                            ->nullable(),

                        TextInput::make('npwp')
                            ->label('NPWP')
                            ->maxLength(20)
                            ->required(),

                        TextInput::make('bank_account_number')
                            ->label('Nomor Rekening')
                            ->maxLength(20)
                            ->required(),

                        TextInput::make('working_unit')
                            ->label('Satuan Kerja')
                            ->required(),

                        TextInput::make('payment_value')
                            ->label('Nilai Kontrak')
                            ->prefix('Rp. ')
                            ->columnSpanFull()
                            ->mask(RawJs::make('$money($input)'))
                            ->numeric()
                            ->required(),

                    ]),



                self::getPDFs(),


            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query()->where('verification_progress', 'ppk')->orderBy('created_at', 'DESC');

        if (
            static::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            static::scopeEloquentQueryToTenant($query, $tenant);
        }

        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contract_number')
                    ->label('Nomor Kontrak')
                    ->searchable(),

                TextColumn::make('request_number')
                    ->label('Nomor Permintaan')
                    ->prefix('#')
                    ->searchable(),

                TextColumn::make('payment_stage')
                    ->label('Tahap Pembayaran')
                    ->prefix('Tahap ')
                    ->sortable(),

                TextColumn::make('payment_value')
                    ->label('Nilai Pembayaran')
                    ->money('IDR', true)
                    ->sortable(),

                TextColumn::make('payment_description')
                    ->label('Deskripsi')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(50),

                TextColumn::make('ppk_verification_status')
                    ->label('Status Verifikasi PPK')
                    ->colors([
                        'primary'   => 'in_progress',
                        'success'   => 'approved',
                        'danger'    => 'rejected',
                    ])
                    ->sortable(),

                TextColumn::make('ppk_rejection_reason')
                    ->label('Alasan Penolakan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('approve_btn')
                    ->label('Setujui')
                    ->requiresConfirmation()
                    ->disabled(function (PaymentRequest $record) {

                        if ($record->ppk_verification_status == 'in_progress') {
                            return false;
                        }

                        return true;
                    })
                    ->action(function (PaymentRequest $record, array $data) {

                        $record->update([
                            'ppk_verification_status'   =>  'approved',
                            'ppk_id'                    =>  get_auth_user()->ppk->id,
                            'verification_progress'     =>  'ppspm',
                            'ppspm_verification_status' =>  'in_progress',
                        ]);

                        Notification::make('x_not')
                            ->title('Permohonan Pembayaran Diterima')
                            ->body('Pengajuan Pembayaran #' . $record->contract_number . ' Diterima')
                            ->send();

                        Notification::make('x_not_srv')
                            ->title('Permohonan Pembayaran Diterima')
                            ->body('Pengajuan Pembayaran #' . $record->contract_number . ' Diterima')
                            ->sendToDatabase($record->service_provider->user);
                    })
                    ->icon('heroicon-o-check-circle'),

                Tables\Actions\Action::make('reject_btn')
                    ->label('Tolak')
                    ->requiresConfirmation()
                    ->disabled(function (PaymentRequest $record) {

                        if ($record->ppk_verification_status == 'in_progress') {
                            return false;
                        }

                        return true;
                    })
                    ->form([
                        TextInput::make('reject_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Kenapa anda menolaknya?')
                            ->minLength(3)
                            ->maxLength(199)
                    ])
                    ->action(function (PaymentRequest $record, array $data) {

                        $record->update([
                            'ppk_verification_status'   => 'rejected',
                            'ppk_rejection_reason'      =>  $data['reject_reason'],
                            'ppk_id'                    =>  get_auth_user()->ppk->id,
                        ]);

                        Notification::make('x_not')
                            ->title('Permohonan Pembayaran ditolak')
                            ->body('Berhasil Menolak Permohonan Dengan alasan ' . "' $record->ppk_rejection_reason '")
                            ->send();

                        Notification::make('x_not_srv')
                            ->title('Permohonan Pembayaran ditolak')
                            ->body('Petugas PPK Menolak Permohonan Anda Dengan alasan ' . "' $record->ppk_rejection_reason '")
                            ->sendToDatabase($record->service_provider->user);
                    })
                    ->color('danger')
                    ->icon('heroicon-o-x-mark'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentRequests::route('/'),
            'create' => Pages\CreatePaymentRequest::route('/create'),
            'view' => Pages\PaymentRequestPayment::route('/{record}/view'),
            'edit' => Pages\EditPaymentRequest::route('/{record}/edit'),
        ];
    }

    public static function getPDFs()
    {
        return Repeater::make('documents')
            ->relationship()
            ->label('Daftar Dokumen Pendukung')
            ->columnSpanFull()
            ->grid(2)
            ->schema([

                TextInput::make('name')->label(''),

                Actions::make([

                    Action::make('View')
                        ->icon('heroicon-o-eye')
                        ->label('Tampilkan')
                        ->url(function (Document $record) {

                            return asset('/storage/' . $record->path);
                        }, true),

                ])->inlineLabel(),

            ]);
    }
}
