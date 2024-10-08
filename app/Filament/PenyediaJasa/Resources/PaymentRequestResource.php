<?php

namespace App\Filament\PenyediaJasa\Resources;

use App\Filament\PenyediaJasa\Resources\PaymentRequestResource\Pages;
use App\Models\Contract;
use App\Models\PaymentRequest;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
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
use Illuminate\Database\Eloquent\Model;

class PaymentRequestResource extends Resource
{
    protected static ?string $model = PaymentRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $label = 'Permohonan Pembayaran';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Menu Utama';

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query()->where('service_provider_id', get_auth_user()->services_provider->id);

        if (
            static::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            static::scopeEloquentQueryToTenant($query, $tenant);
        }

        return $query;
    }

    public static function canEdit(Model $record): bool
    {
        return $record->verification_progress == 'rejected';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('contract_number')
                    ->required()
                    ->searchable()
                    ->label('Nomor Kontrak')
                    ->live()
                    ->options(get_my_contracts_for_options()),

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
                    ->reactive()
                    ->maxValue(function (Get $get) {
                        // dump($get('contract_number'));
                    })
                    ->mask(RawJs::make('$money($input)')),

                Textarea::make('payment_description')
                    ->columnSpanFull()
                    ->maxLength(199)
                    ->minLength(3)
                    ->label('Deskripsi Pembayaran'),


                Fieldset::make('Dokumen Pendukung Untuk Pembayaran Uang Muka')
                    ->label('Dokumen Pendukung Untuk Pembayaran Uang Muka')
                    ->hidden(function (Get $get) {

                        $number = $get('contract_number');

                        $ctx = Contract::where('contract_number', $number)->first();

                        if (!$ctx) return true;

                        return cek_pembayaran_pertama($ctx) ? false : true;
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

                        FileUpload::make('jaminan_uang_muka')
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
                    ->label('Dokumen Pendukung')
                    ->columns(3)
                    ->hidden(function (Get $get) {
                        $number = $get('contract_number');

                        if (!$number) return true;

                        $ctx = Contract::where('contract_number', $number)->first();

                        if (!$ctx) return true;

                        return cek_pembayaran_pertama($ctx) ? true : false;
                    })
                    ->visibleOn(['create', 'edit'])
                    ->schema([

                        FileUpload::make('Surat Permohonan Pembayaran')
                            ->label('Surat Permohonan Pembayaran')
                            ->directory('documents')
                            ->uploadingMessage('Upload Surat Permohonan Pembayaran...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required() // required
                            ->maxSize(1024 * 25),

                        FileUpload::make('Rekening Koran')
                            ->label('Rekening Koran')
                            ->directory('documents')
                            ->uploadingMessage('Upload Rekening Koran...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required() // required
                            ->maxSize(1024 * 25),

                        FileUpload::make('NPWP')
                            ->label('NPWP')
                            ->directory('documents')
                            ->uploadingMessage('Upload NPWP...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required() // required
                            ->maxSize(1024 * 25),

                        FileUpload::make('E-Faktur')
                            ->label('E-Faktur')
                            ->directory('documents')
                            ->uploadingMessage('Upload E-Faktur...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required() // required
                            ->maxSize(1024 * 25),

                        FileUpload::make('Jaminan Pemeliharaan')
                            ->label('Jaminan Pemeliharaan (Jika Termijn 100%)')
                            ->directory('documents')
                            ->uploadingMessage('Upload Jaminan Pemeliharaan...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Surat Permohonan Penerimaan Hasil Pekerjaan')
                            ->label('Surat Permohonan Penerimaan Hasil Pekerjaan yang telah disetujui direksi')
                            ->directory('documents')
                            ->uploadingMessage('Upload Surat Permohonan Penerimaan Hasil Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required() // required

                            ->maxSize(1024 * 25),

                        FileUpload::make('Surat Perintah Pemeriksaan Hasil Pekerjaan')
                            ->label('Surat Perintah Pemeriksaan Hasil Pekerjaan oleh PPK')
                            ->directory('documents')
                            ->uploadingMessage('Upload Surat Perintah Pemeriksaan Hasil Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required() // required

                            ->maxSize(1024 * 25),

                        FileUpload::make('Berita Acara Pemeriksaan Pekerjaan')
                            ->label('Berita Acara Pemeriksaan Pekerjaan')
                            ->directory('documents')
                            ->uploadingMessage('Upload Berita Acara Pemeriksaan Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required() // required

                            ->maxSize(1024 * 25),

                        FileUpload::make('Berita Acara Prestasi Pekerjaan')
                            ->label('Berita Acara Prestasi Pekerjaan')
                            ->directory('documents')
                            ->uploadingMessage('Upload Berita Acara Prestasi Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required() // required

                            ->maxSize(1024 * 25),

                        FileUpload::make('Gambar Kerja')
                            ->label('Gambar Kerja (Shop Drawing / Asbuilt Drawing)')
                            ->directory('documents')
                            ->uploadingMessage('Upload Gambar Kerja...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Spesifikasi Teknis')
                            ->label('Spesifikasi Teknis')
                            ->directory('documents')
                            ->uploadingMessage('Upload Spesifikasi Teknis...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Backup Perhitungan Kuantitas')
                            ->label('Backup Perhitungan Kuantitas')
                            ->directory('documents')
                            ->uploadingMessage('Upload Backup Perhitungan Kuantitas...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Form Penerimaan Hasil Pekerjaan')
                            ->label('Form Penerimaan Hasil Pekerjaan')
                            ->directory('documents')
                            ->uploadingMessage('Upload Form Penerimaan Hasil Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Peralatan Pemeriksaan')
                            ->label('Peralatan Pemeriksaan')
                            ->directory('documents')
                            ->uploadingMessage('Upload Peralatan Pemeriksaan...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Daftar Hadir')
                            ->label('Daftar Hadir')
                            ->directory('documents')
                            ->uploadingMessage('Upload Daftar Hadir...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Laporan Hasil Pengujian Kualitas')
                            ->label('Laporan Hasil Pengujian Kualitas')
                            ->directory('documents')
                            ->uploadingMessage('Upload Laporan Hasil Pengujian Kualitas...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Laporan Kemajuan Pekerjaan')
                            ->label('Laporan Kemajuan Pekerjaan')
                            ->directory('documents')
                            ->uploadingMessage('Upload Laporan Kemajuan Pekerjaan...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Sertifikat Bulanan')
                            ->label('Sertifikat Bulanan / Monthly Certificate')
                            ->directory('documents')
                            ->uploadingMessage('Upload Sertifikat Bulanan...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Laporan Bulanan')
                            ->label('Laporan Bulanan')
                            ->directory('documents')
                            ->uploadingMessage('Upload Laporan Bulanan...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                    ]),

                Fieldset::make('Dokumen Pendukung Jika Konsultan')
                    ->columns(3)
                    ->hidden(function (Get $get) {
                        $number = $get('contract_number');

                        if (!$number) return true;

                        $ctx = Contract::where('contract_number', $number)->first();

                        if (!$ctx) return true;

                        return cek_pembayaran_pertama($ctx) ? true : false;
                    })
                    ->visibleOn(['create', 'edit'])
                    ->schema([

                        FileUpload::make('Laporan Bulanan ( Jika Konsultan )')
                            ->label('Laporan Bulanan ( Jika Konsultan )')
                            ->directory('documents')
                            ->uploadingMessage('Upload Laporan Bulanan...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Laporan Antara')
                            ->label('Laporan Antara (Jika Konsultan)')
                            ->directory('documents')
                            ->uploadingMessage('Upload Laporan Antara...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Laporan Akhir')
                            ->label('Laporan Akhir (Jika Konsultan)')
                            ->directory('documents')
                            ->uploadingMessage('Upload Laporan Akhir...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Backup Invoice')
                            ->label('Backup Invoice (Jika Konsultan)')
                            ->directory('documents')
                            ->uploadingMessage('Upload Backup Invoice...')
                            ->acceptedFileTypes(['application/pdf'])

                            ->maxSize(1024 * 25),

                        FileUpload::make('Dokumen Lainnya')
                            ->label('Dokumen Lainnya yang dipersyaratkan dalam kontrak')
                            ->directory('documents')
                            ->uploadingMessage('Upload Dokumen Lainnya...')
                            ->acceptedFileTypes(['application/pdf'])

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

                        // TextInput::make('contract.service_provider.full_name')
                        //     ->label('Penyedia Jasa'),

                        // Select::make('admin_id')
                        //     ->label('Admin')
                        //     ->relationship('admin', 'name')
                        //     ->required(),

                    ]),

                Fieldset::make('Progres Verifikasi Petugas PPK')
                    ->visibleOn(['view', 'edit'])
                    ->schema([

                        TextInput::make('ppk.full_name')
                            ->label('Petugas PPK')
                            ->formatStateUsing(function ($record) {
                                return $record?->ppk?->full_name ?? 'Belum Tersedia';
                            })
                            ->disabled(),

                        TextInput::make('ppk_verification_status')
                            ->label('Status Verifikasi Petugas PPK')
                            ->formatStateUsing(function ($state) {
                                return match ($state) {
                                    'not_available' => 'Belum Tersedia',
                                    'in_progress' => 'Dalam Proses',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    default => $state,
                                };
                            })
                            ->disabled(),

                        Textarea::make('ppk_rejection_reason')
                            ->label('Alasan Penolkan PPK')
                            ->columnSpanFull()
                            ->hidden(function ($state) {
                                return !$state ? true : false;
                            })
                            ->disabled(),
                    ]),

                Fieldset::make('Progres Verifikasi Petugas PPSPM')
                    ->visibleOn(['view', 'edit'])
                    ->hidden(function ($record) {
                        return $record?->ppspm_verification_status === 'not_available';
                    })
                    ->schema([

                        TextInput::make('spm.full_name')
                            ->label('Petugas PPSPM')
                            ->formatStateUsing(function ($record) {
                                return $record?->spm?->full_name ?? 'Belum Tersedia';
                            })
                            ->disabled(),

                        TextInput::make('ppspm_verification_status')
                            ->label('Status Verifikasi Petugas PPSPM')
                            ->formatStateUsing(function ($state, $record) {
                                return match ($state) {
                                    'not_available' => 'Belum Tersedia',
                                    'in_progress' => 'Dalam Proses',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    default => $state,
                                };
                            })
                            ->disabled(),

                        Textarea::make('ppspm_rejection_reason')
                            ->label('Alasan Penolkan PPSPM')
                            ->columnSpanFull()
                            ->hidden(function ($state) {
                                return !$state ? true : false;
                            })
                            ->disabled(),
                    ]),

                Fieldset::make('Progres Verifikasi Petugas Bendahara')
                    ->visibleOn(['view', 'edit'])
                    ->hidden(function ($record) {
                        return $record?->treasurer_verification_status === 'not_available';
                    })
                    ->schema([

                        TextInput::make('treasurer.full_name')
                            ->label('Petugas Bendahara')
                            ->formatStateUsing(function ($record) {
                                return $record?->treasurer?->full_name ?? 'Belum Tersedia';
                            })
                            ->disabled(),

                        TextInput::make('treasurer_verification_status')
                            ->label('Status Verifikasi Petugas Bendahara')
                            ->formatStateUsing(function ($state) {
                                return match ($state) {
                                    'not_available' => 'Belum Tersedia',
                                    'in_progress' => 'Dalam Proses',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    default => $state,
                                };
                            })
                            ->disabled(),

                        Textarea::make('treasurer_rejection_reason')
                            ->label('Alasan Penolkan Bendahara')
                            ->columnSpanFull()
                            ->hidden(function ($state) {
                                return !$state ? true : false;
                            })
                            ->disabled(),
                    ]),

                Fieldset::make('Progres Verifikasi KPA')
                    ->visibleOn(['view', 'edit'])
                    ->hidden(function ($record) {
                        return $record?->kpa_verification_status === 'not_available';
                    })
                    ->schema([

                        TextInput::make('kpa_verification_status')
                            ->label('Status Verifikasi KPA')
                            ->formatStateUsing(function ($state) {
                                return match ($state) {
                                    'not_available' => 'Belum Tersedia',
                                    'in_progress' => 'Dalam Proses',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    default => $state,
                                };
                            })
                            ->disabled(),

                        Textarea::make('kpa_rejection_reason')
                            ->label('Alasan Penolkan KPA')
                            ->columnSpanFull()
                            ->hidden(function ($state) {
                                return !$state ? true : false;
                            })
                            ->disabled(),
                    ]),


                // Select::make('verification_status')
                //     ->columnSpanFull()
                //     ->options([
                //         'in_progress' => 'Dalam Proses',
                //         'approved' => 'Disetujui',
                //         'rejected' => 'Ditolak',
                //     ])
                //     ->default('in_progress')
                //     ->label('Status Verifikasi'),

                // Textarea::make('rejection_reason')
                //     ->label('Alasan Penolakan')
                //     ->columnSpanFull()
                //     ->nullable(),

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
                    ->money('IDR', 0)
                    ->sortable(),

                TextColumn::make('id')
                    ->label('Sisa Kontrak')
                    ->money('IDR', true)
                    ->formatStateUsing(function ($record) {
                        $contract = $record->contract;
                        return 'Rp. ' . number_format($contract->payment_value - $contract->paid_value, 0, ',', '.');
                    })
                    ->sortable(),

                TextColumn::make('payment_description')
                    ->label('Deskripsi')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(50),

                TextColumn::make('verification_progress')
                    ->label('Progres Verifikasi')
                    ->colors([
                        'primary'   => 'ppk',
                        'success'   => 'done',
                        'danger'    => 'rejected',
                        'warning'   => 'ppspm',
                        'secondary' => 'treasurer',
                    ])
                    ->formatStateUsing(function ($state) {
                        return strtoupper($state);
                    })
                    ->sortable(),

                TextColumn::make('ppk_verification_status')
                    ->label('Status Verifikasi PPK')
                    ->colors([
                        'warning'   => 'not_available',
                        'primary'   => 'in_progress',
                        'success'   => 'approved',
                        'danger'    => 'rejected',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('ppspm_verification_status')
                    ->label('Status Verifikasi PP-SPM')
                    ->colors([
                        'warning'   => 'not_available',
                        'primary'   => 'in_progress',
                        'success'   => 'approved',
                        'danger'    => 'rejected',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('treasurer_verification_status')
                    ->label('Status Verifikasi Bendahara')
                    ->colors([
                        'warning'   => 'not_available',
                        'primary'   => 'in_progress',
                        'success'   => 'approved',
                        'danger'    => 'rejected',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('kpa_verification_status')
                    ->label('Status Verifikasi KPA')
                    ->colors([
                        'warning'   => 'not_available',
                        'primary'   => 'in_progress',
                        'success'   => 'approved',
                        'danger'    => 'rejected',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

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
                Filter::make('verification_status')
                    ->query(fn(Builder $query): Builder => $query->where('verification_status', 'approved'))
                    ->label('Approved Payments'),

                Filter::make('verification_status')
                    ->query(fn(Builder $query): Builder => $query->where('verification_status', 'in_progress'))
                    ->label('In Progress Payments'),

                Filter::make('verification_status')
                    ->query(fn(Builder $query): Builder => $query->where('verification_status', 'rejected'))
                    ->label('Rejected Payments'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'edit' => Pages\EditPaymentRequest::route('/{record}/edit'),
            'view' => Pages\ViewPaymentRequest::route('/{record}'),
        ];
    }
}
