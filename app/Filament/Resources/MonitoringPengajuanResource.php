<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonitoringPengajuanResource\Pages;
use App\Models\Contract;
use App\Models\Document;
use App\Models\PaymentRequest;
use Filament\Facades\Filament;
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
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MonitoringPengajuanResource extends Resource
{
    protected static ?string $model = PaymentRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $label = 'Monitoring Pembayaran';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Menu Utama';


    // canCreate, canEdit, canDelete, canDeleteAny
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
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

                self::getPDFs(),
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

                TextColumn::make('payment_description')
                    ->label('Deskripsi')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                // Menggunakan badge dan menyesuaikan label serta warna
                TextColumn::make('verification_progress')
                    ->label('Progres Verifikasi')
                    ->badge()
                    ->colors([
                        'primary'   => 'ppk',
                        'success'   => 'done',
                        'danger'    => 'rejected',
                        'warning'   => 'ppspm',
                        'secondary' => 'treasurer',
                    ])
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'ppk'       => 'PPK',
                            'ppspm'     => 'PP-SPM',
                            'treasurer' => 'Bendahara',
                            'done'      => 'Selesai',
                            'rejected'  => 'Ditolak',
                        ];
                        return $labels[$state] ?? strtoupper($state);
                    })
                    ->sortable(),

                // Menyesuaikan kolom status dengan badge dan label bahasa Indonesia
                TextColumn::make('ppk_verification_status')
                    ->label('Status Verifikasi PPK')
                    ->badge()
                    ->colors([
                        'warning' => 'not_available',
                        'primary' => 'in_progress',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'not_available' => 'Belum Tersedia',
                            'in_progress'   => 'Sedang Diproses',
                            'approved'      => 'Disetujui',
                            'rejected'      => 'Ditolak',
                        ];
                        return $labels[$state] ?? $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('ppspm_verification_status')
                    ->label('Status Verifikasi PP-SPM')
                    ->badge()
                    ->colors([
                        'warning' => 'not_available',
                        'primary' => 'in_progress',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'not_available' => 'Belum Tersedia',
                            'in_progress'   => 'Sedang Diproses',
                            'approved'      => 'Disetujui',
                            'rejected'      => 'Ditolak',
                        ];
                        return $labels[$state] ?? $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('treasurer_verification_status')
                    ->label('Status Verifikasi Bendahara')
                    ->badge()
                    ->colors([
                        'warning' => 'not_available',
                        'primary' => 'in_progress',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'not_available' => 'Belum Tersedia',
                            'in_progress'   => 'Sedang Diproses',
                            'approved'      => 'Disetujui',
                            'rejected'      => 'Ditolak',
                        ];
                        return $labels[$state] ?? $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('kpa_verification_status')
                    ->label('Status Verifikasi KPA')
                    ->badge()
                    ->colors([
                        'warning' => 'not_available',
                        'primary' => 'in_progress',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'not_available' => 'Belum Tersedia',
                            'in_progress'   => 'Sedang Diproses',
                            'approved'      => 'Disetujui',
                            'rejected'      => 'Ditolak',
                        ];
                        return $labels[$state] ?? $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->dateTime(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime(),
            ])
            ->filters([
                Filter::make('verification_status')
                    ->query(fn(Builder $query): Builder => $query->where('verification_status', 'approved'))
                    ->label('Pembayaran Disetujui'),

                Filter::make('verification_status')
                    ->query(fn(Builder $query): Builder => $query->where('verification_status', 'in_progress'))
                    ->label('Pembayaran Sedang Diproses'),

                Filter::make('verification_status')
                    ->query(fn(Builder $query): Builder => $query->where('verification_status', 'rejected'))
                    ->label('Pembayaran Ditolak'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
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



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonitoringPengajuans::route('/'),
            'create' => Pages\CreateMonitoringPengajuan::route('/create'),
            'edit' => Pages\EditMonitoringPengajuan::route('/{record}/edit'),
            'view' => Pages\ViewMonitoringPengajuanResource::route('/{record}'),
        ];
    }
}
