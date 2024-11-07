<?php

namespace App\Filament\Spm\Resources;

use App\Filament\Spm\Resources\ApprovalSPPResource\Pages;
use App\Filament\Spm\Resources\ApprovalSPPResource\RelationManagers;
use App\Models\ApprovalSPP;
use App\Models\TermintSppPpk;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ButtonAction;
use App\Enums\FileType;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;

class ApprovalSPPResource extends Resource
{
    protected static ?string $model = TermintSppPpk::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $label = 'Verifikasi SPP';

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
            ->schema([
                Forms\Components\Select::make('contract_id')
                    ->label('No. Kontrak')
                    ->relationship('contract', 'contract_number')
                    ->required(),
                Forms\Components\TextInput::make('no_termint')
                    ->label('No. SPP')
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->label('Uraian Pembayaran SPP-PPK')
                    ->required(),
                Forms\Components\TextInput::make('payment_value')
                    ->label('Nilai Permintaan Pembayaran')
                    ->required()->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)->prefix('Rp'),
                Forms\Components\Toggle::make('has_advance_payment')
                    ->label('Uang Muka')
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
                Forms\Components\FileUpload::make('files.' . FileType::BUKTI_PEMBAYARAN_PAJAK->value)
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contract.contract_number')
                    ->label('Kontrak')
                    ->searchable(),

                TextColumn::make('no_termint')
                    ->label('No. Pengajuan SPP')
                    ->searchable(),

                TextColumn::make('contract.contract_number')
                    ->label('No. Kontrak'),

                TextColumn::make('payment_request.request_number')
                    ->label('No. Permintaan')
                    ->prefix('#'),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                TextColumn::make('payment_value')
                    ->label('Nilai Pembayaran')
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

                // Menggunakan badge pada 'ppspm_verification_status'
                TextColumn::make('ppspm_verification_status')
                    ->label('Status Verifikasi SPM')
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
                    })
                    ->sortable(),

                TextColumn::make('ppspm_rejection_reason')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Alasan Penolakan')
                    ->limit(50)
                    ->searchable(),

                BooleanColumn::make('has_advance_payment')
                    ->label('Uang Muka'),

                TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat Pada')
                    ->dateTime(),

                TextColumn::make('updated_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Diperbarui Pada')
                    ->dateTime(),
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
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),


                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('approve_btn')
                    ->label('Setujui')
                    ->requiresConfirmation()
                    ->color('success')
                    ->disabled(function (TermintSppPpk $record) {
                        return $record->ppspm_verification_status !== 'in_progress';
                    })
                    ->action(function (TermintSppPpk $record, array $data) {
                        $record->update([
                            'ppspm_verification_status' => 'approved',
                            'ppspm_id'                  => get_auth_user()->spm->id,
                        ]);

                        Notification::make()
                            ->title('Permohonan Pembayaran Disetujui')
                            ->body('Pengajuan Pembayaran #' . $record->no_termint . ' telah disetujui.')
                            ->success()
                            ->send();
                    })
                    ->icon('heroicon-o-check-circle'),

                Tables\Actions\Action::make('reject_btn')
                    ->label('Tolak')
                    ->requiresConfirmation()
                    ->disabled(function (TermintSppPpk $record) {
                        if ($record->request_reject === 1) {
                            return false;
                        } else {
                            return $record->ppspm_verification_status !== 'in_progress';
                        }
                    })
                    ->modalWidth('xl')
                    ->form([
                        \Filament\Forms\Components\RichEditor::make('reject_reason')
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->label('Alasan Penolakan')
                            ->placeholder('Kenapa Anda menolaknya?')
                            ->required(),
                    ])
                    ->action(function (TermintSppPpk $record, array $data) {
                        $record->update([
                            'ppspm_verification_status' => 'rejected',
                            'ppspm_rejection_reason'    => $data['reject_reason'],
                            'ppspm_id'                  => get_auth_user()->spm->id,
                            'request_reject'            => false,
                        ]);



                        Notification::make()
                            ->title('Permohonan Pembayaran Ditolak')
                            ->body('Anda telah menolak permohonan dengan alasan: ' . $record->ppspm_rejection_reason)
                            ->danger()
                            ->send();
                    })
                    ->color('danger')
                    ->icon('heroicon-o-x-mark'),

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
            'index' => Pages\ListApprovalSPPS::route('/'),
            'create' => Pages\CreateApprovalSPP::route('/create'),
            'edit' => Pages\EditApprovalSPP::route('/{record}/edit'),
        ];
    }
}
