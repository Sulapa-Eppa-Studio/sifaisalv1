<?php

namespace App\Filament\Ppk\Resources;

use App\Filament\Ppk\Resources\TermintSppPpkResource\Pages;
use App\Models\TermintSppPpk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use App\Enums\FileType;
use App\Models\Contract;
use App\Models\PaymentRequest;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;
use Filament\Forms\Set;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;

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
        // if ($record instanceof TermintSppPpk) {
        //     if ($record->payment_request?->kpa_verification_status == 'rejected') return false;
        // }

        return $record->ppspm_verification_status == 'rejected';
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('contract_id')
                    ->required()
                    ->searchable()
                    ->label('Nomor Kontrak')
                    ->required()
                    ->searchable()
                    ->live()
                    ->label('Nomor Kontrak')
                    // ->afterStateHydrated(function (Set $set, $state) {
                    //     // Jalankan fungsi untuk mengisi payment request secara otomatis saat edit
                    //     self::loadPaymentRequestData($set, $state);
                    // })
                    // ->afterStateUpdated(function (Set $set, $state) {
                    //     // Juga tetap jalankan ketika pengguna memilih ulang
                    //     self::loadPaymentRequestData($set, $state);
                    // })
                    ->options(get_my_contracts_for_options_by_ppk()),

                Forms\Components\Select::make('payment_request_id')
                    ->label('Nomor Pengajuan')
                    ->required()
                    ->reactive()
                    ->options(function (Get $get) {
                        $contract = Contract::find($get('contract_id'));

                        $record = PaymentRequest::where('contract_number', $contract?->contract_number)
                            ->where('ppk_verification_status', 'approved')
                            ->get();

                        return $record->pluck('request_number', 'id');
                    }),

                // Forms\Components\Hidden::make('payment_request_id'),

                Forms\Components\TextInput::make('no_termint')
                    ->label('Nomor SPP')
                    ->required(),

                DatePicker::make('spp_date')->required()
                    ->label('Tanggal SPP')->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Uraian Pembayaran SPP-PPK')->columnSpanFull()
                    ->required(),

                Forms\Components\TextInput::make('payment_value')
                    ->label('Nilai Permintaan Pembayaran')
                    ->required()
                    ->columnSpanFull()
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                    ->prefix('Rp'),


                Forms\Components\Toggle::make('has_advance_payment')
                    ->label('Uang Muka')
                    ->disabledOn('edit')
                    ->reactive(),

                Forms\Components\Fieldset::make('Pilih Dokumen Yang Akan Diunggah')
                    ->visibleOn(['create', 'edit'])
                    ->schema(function (Forms\Components\Component $component) {
                        return self::getDocumentFields($component->getState()['has_advance_payment'] ?? false);
                    })
                    ->columns(2)
                    ->hidden(fn(array $state): bool => empty($state['payment_request_id'])),

                Forms\Components\Fieldset::make('Progres Verifikasi Petugas PPSPM')
                    ->visibleOn(['view', 'edit'])
                    ->hidden(function ($record) {
                        return $record?->ppspm_verification_status === 'not_available';
                    })
                    ->schema([

                        Forms\Components\TextInput::make('spm.full_name')
                            ->label('Petugas PPSPM')
                            ->formatStateUsing(function ($record) {
                                return $record?->spm?->full_name ?? 'Belum Tersedia';
                            })
                            ->disabled(),

                        Forms\Components\TextInput::make('ppspm_verification_status')
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

                        Forms\Components\RichEditor::make('ppspm_rejection_reason')
                            ->label('Alasan Penolakan PPSPM')
                            ->columnSpanFull()
                            ->hidden(function ($state) {
                                return !$state ? true : false;
                            })
                            ->disabled(),
                    ]),
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

                Forms\Components\FileUpload::make('files.' . FileType::LAINNYA->value)
                    ->label('LAINNYA')
                    ->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])
                    ->directory('termint_files'),
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
                    ->label('BAPP / BAST (dari Aplikasi SAKTI)')
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
                Forms\Components\FileUpload::make('files.' . FileType::BUKTI_PEMBAYARAN_PAJAK->value)
                    ->label('Surat Setoran Pajak (SSP)')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),
                Forms\Components\FileUpload::make('files.' . FileType::SPTJB->value)
                    ->label('SPTJB')
                    ->required()->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])->directory('termint_files'),

                Forms\Components\FileUpload::make('files.' . FileType::LAINNYA->value)
                    ->label('LAINNYA')
                    ->acceptedFileTypes($pdfValidation['acceptedFileTypes'])
                    ->maxSize($pdfValidation['maxSize'])
                    ->directory('termint_files'),

            ];
        }
    }

    public static function getEloquentQuery(): Builder
    {
        $user   =   get_auth_user();

        $query = static::getModel()::query()->where('user_id', $user->id)->orderBy('created_at', 'DESC');

        if (
            static::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            static::scopeEloquentQueryToTenant($query, $tenant);
        }

        return $query;
    }
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract.contract_number')
                    ->label('No. Kontrak'),

                Tables\Columns\TextColumn::make('payment_request.request_number')
                    ->label('No. Pengajuan')
                    ->prefix('#'),

                Tables\Columns\TextColumn::make('contract.work_package')
                    ->label('Paket Pekerjaan')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),

                Tables\Columns\TextColumn::make('no_termint')
                    ->label('No. SPP'),

                Tables\Columns\TextColumn::make('spp_date')->label('Tanggal SPP')
                    ->date(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                // Menggunakan TextColumn dengan badge untuk 'ppspm_verification_status'
                Tables\Columns\TextColumn::make('ppspm_verification_status')
                    ->label('Status Verifikasi')
                    ->badge()
                    ->colors([
                        'warning' => 'not_available',
                        'primary' => 'in_progress',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'not_available' => 'Belum Diproses',
                            'in_progress' => 'Sedang Diproses',
                            'approved'    => 'Disetujui',
                            'rejected'    => 'Ditolak',
                        ];
                        return $labels[$state] ?? ucfirst($state);
                    }),

                Tables\Columns\TextColumn::make('payment_value')
                    ->label('Nilai Pembayaran')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(function ($state) {
                        return format_number_new($state);
                    })
                    ->prefix('Rp. ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('id')
                    ->label('Sisa Kontrak')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(function (TermintSppPpk $record) {
                        $contract = $record->contract;
                        return format_number_new($contract->payment_value - $record->paid_value);
                    })
                    ->prefix('Rp. ')
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
                Tables\Actions\ViewAction::make()->label('Detail'),

                Action::make('viewFiles')
                    ->label('Lihat File')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn(TermintSppPpk $record): string => "Daftar File untuk {$record->no_termint}")
                    ->modalWidth('6xl')
                    ->modalContent(function (TermintSppPpk $record) {
                        return view('livewire.view-files-modal', ['record' => $record]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

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

    /**
     * Fungsi untuk memuat data PaymentRequest berdasarkan contract_id yang diberikan
     */
    protected static function loadPaymentRequestData(Set $set, $contractId)
    {
        if ($contractId) {
            $contract = Contract::find($contractId);
            $record = PaymentRequest::where('contract_number', $contract?->contract_number)
                ->where('ppk_verification_status', 'approved')
                ->where('verification_progress', 'ppk')
                ->first();

            $set('payment_request_id', $record?->id);
            $set('payment_request_name', $record?->request_number);
        }
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
