<?php

namespace App\Filament\PenyediaJasa\Resources;

use App\Filament\PenyediaJasa\Resources\PaymentRequestResource\Pages;
use App\Models\Contract;
use App\Models\PaymentRequest;
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

    public static function canEdit(Model $record): bool
    {
        return false;
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

                        // TextInput::make('contract.service_provider.full_name')
                        //     ->label('Penyedia Jasa'),

                        // Select::make('admin_id')
                        //     ->label('Admin')
                        //     ->relationship('admin', 'name')
                        //     ->required(),

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
