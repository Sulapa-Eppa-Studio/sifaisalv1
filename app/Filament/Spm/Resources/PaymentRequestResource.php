<?php

namespace App\Filament\Spm\Resources;

use App\Filament\Ppk\Resources\PaymentRequestResource as ResourcesPaymentRequestResource;
use App\Filament\Spm\Resources\PaymentRequestResource\Pages;
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

    protected static ?string $label = 'Verifikasi Dokumen Pendukung';

    protected static ?string $navigationLabel = 'Verifikasi Dok. Pendukung';




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

    public static function getEloquentQuery(): Builder
    {
        $user   =   get_auth_user();

        $ppspm  =   $user->spm;

        return parent::getModel()::query()->where('verification_progress', 'ppspm')->orWhere('ppspm_id', $ppspm->id);
    }

    public static function form(Form $form): Form
    {
        return $form
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

                            ->maxSize(1024 * 25),

                        FileUpload::make('Rekening Koran')
                            ->directory('documents')
                            ->uploadingMessage('Upload dokumen...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->maxSize(1024 * 25),

                        FileUpload::make('npwp')
                            ->label('NPWP')
                            ->directory('documents')
                            ->uploadingMessage('Upload dokumen...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->maxSize(1024 * 25),

                        FileUpload::make('E-Faktur')
                            ->directory('documents')
                            ->uploadingMessage('Upload dokumen...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->maxSize(1024 * 25),

                        FileUpload::make('Surat Keabsahan Dan Kebenaran Jaminan Uang Muka')
                            ->directory('documents')
                            ->uploadingMessage('Upload dokumen...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->maxSize(1024 * 25),
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
                            ->maxSize(1024 * 25), // ukuran file maksimum dalam kilobytes (12 MB)

                        FileUpload::make('Rekening Koran')
                            ->label('Rekening Koran')
                            ->directory('documents')
                            ->uploadingMessage('Upload Rekening Koran...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()

                            ->maxSize(1024 * 25),

                        FileUpload::make('NPWP')
                            ->label('NPWP')
                            ->directory('documents')
                            ->uploadingMessage('Upload NPWP...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()

                            ->maxSize(1024 * 25),

                        FileUpload::make('E-Faktur')
                            ->label('E-Faktur')
                            ->directory('documents')
                            ->uploadingMessage('Upload E-Faktur...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()

                            ->maxSize(1024 * 25),

                        FileUpload::make('Jaminan Pemeliharaan (Jika Termijn 100%)')
                            ->label('Jaminan Pemeliharaan (Jika Termijn 100%)')
                            ->directory('documents')
                            ->uploadingMessage('Upload Jaminan Pemeliharaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->nullable() // opsional, karena hanya diperlukan jika term sudah mencapai 100%

                            ->maxSize(1024 * 25),

                        FileUpload::make('Surat Permohonan Penerimaan Hasil Pekerjaan')
                            ->label('Surat Permohonan Penerimaan Hasil Pekerjaan')
                            ->directory('documents')
                            ->uploadingMessage('Upload Surat Permohonan Penerimaan Hasil Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()

                            ->maxSize(1024 * 25),

                        FileUpload::make('Surat Perintah Pemeriksaan Hasil Pekerjaan oleh PPK')
                            ->label('Surat Perintah Pemeriksaan Hasil Pekerjaan oleh PPK')
                            ->directory('documents')
                            ->uploadingMessage('Upload Surat Perintah Pemeriksaan Hasil Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()

                            ->maxSize(1024 * 25),

                        FileUpload::make('Berita Acara Pemeriksaan Pekerjaan')
                            ->label('Berita Acara Pemeriksaan Pekerjaan')
                            ->directory('documents')
                            ->uploadingMessage('Upload Berita Acara Pemeriksaan Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()

                            ->maxSize(1024 * 25),

                        FileUpload::make('Berita Acara Prestasi Pekerjaan')
                            ->label('Berita Acara Prestasi Pekerjaan')
                            ->directory('documents')
                            ->uploadingMessage('Upload Berita Acara Prestasi Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()

                            ->maxSize(1024 * 25),
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
                            ->maxLength(199)
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

                ResourcesPaymentRequestResource::getPDFs(),

            ]);
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

                TextColumn::make('id')
                    ->label('Sisa Kontrak')
                    ->formatStateUsing(function ($record) {
                        $contract = $record->contract;
                        return 'Rp. ' . number_format($contract->payment_value - $contract->paid_value, 0, ',', '.');
                    })
                    ->sortable(),

                TextColumn::make('payment_description')
                    ->label('Deskripsi')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                // Menggunakan badge pada 'ppspm_verification_status'
                TextColumn::make('ppspm_verification_status')
                    ->label('Status Verifikasi SPM')
                    ->badge()
                    ->colors([
                        'primary' => 'in_progress',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'in_progress' => 'Sedang Diproses',
                            'approved'    => 'Disetujui',
                            'rejected'    => 'Ditolak',
                        ];
                        return $labels[$state] ?? ucfirst($state);
                    })
                    ->sortable(),

                TextColumn::make('ppspm_rejection_reason')
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
                    ->color('success')
                    ->disabled(function (PaymentRequest $record) {
                        return $record->ppspm_verification_status !== 'in_progress';
                    })
                    ->action(function (PaymentRequest $record, array $data) {

                        $record->update([
                            'ppspm_verification_status'     =>  'approved',
                            'ppspm_id'                      =>  get_auth_user()->spm->id,
                            'verification_progress'         =>  'treasurer',
                            'treasurer_verification_status' =>  'in_progress',
                        ]);

                        Notification::make()
                            ->title('Permohonan Pembayaran Disetujui')
                            ->body('Pengajuan Pembayaran #' . $record->contract_number . ' telah disetujui.')
                            ->send();

                        Notification::make()
                            ->title('Permohonan Pembayaran Disetujui')
                            ->body('Pengajuan Pembayaran #' . $record->contract_number . ' telah disetujui.')
                            ->sendToDatabase($record->service_provider->user);
                    })
                    ->icon('heroicon-o-check-circle'),

                Tables\Actions\Action::make('reject_btn')
                    ->label('Tolak')
                    ->requiresConfirmation()
                    ->disabled(function (PaymentRequest $record) {
                        return $record->ppspm_verification_status !== 'in_progress';
                    })
                    ->form([
                        TextInput::make('reject_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Mengapa Anda menolaknya?')
                            ->minLength(3)
                            ->maxLength(199),
                    ])
                    ->action(function (PaymentRequest $record, array $data) {

                        $record->update([
                            'ppspm_verification_status'   =>  'rejected',
                            'verification_progress'       =>  'rejected',
                            'ppspm_rejection_reason'      =>  $data['reject_reason'],
                            'ppspm_id'                    =>  get_auth_user()->spm->id,
                        ]);

                        Notification::make()
                            ->title('Permohonan Pembayaran Ditolak')
                            ->body('Anda telah menolak permohonan dengan alasan: ' . $record->ppspm_rejection_reason)
                            ->send();

                        Notification::make()
                            ->title('Permohonan Pembayaran Ditolak')
                            ->body('Permohonan Anda ditolak oleh PP-SPM dengan alasan: ' . $record->ppspm_rejection_reason)
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
            'view'  =>  Pages\ViewPaymentRequest::route('/{record}/view'),
            'index' => Pages\ListPaymentRequests::route('/'),
            'create' => Pages\CreatePaymentRequest::route('/create'),
            'edit' => Pages\EditPaymentRequest::route('/{record}/edit'),
        ];
    }
}
