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
                    ->relationship('contract', 'contract_number')
                    ->required(),
                Forms\Components\TextInput::make('no_termint')
                    ->label('No Termint')
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->label('Uraian Pembayaran SPPK-PPK')
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
                Forms\Components\FileUpload::make('files.' . FileType::BUKTI_PEMBAYARAN->value)
                    ->label('Bukti Pembayaran')
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

                TextColumn::make('contract.contract_number')->label('Kontrak')->searchable(),

                TextColumn::make('no_termint')->label('No. SPP')->searchable(),

                TextColumn::make('description')->label('Deskripsi')->searchable(),

                TextColumn::make('payment_value')->label('Nilai Pembayaran')->currency('IDR'),

                BooleanColumn::make('has_advance_payment')->label('Uang Muka'),

                TextColumn::make('ppspm_verification_status')
                    ->label('Status Verifikasi SPM')
                    ->colors([
                        'primary'   => 'in_progress',
                        'success'   => 'approved',
                        'danger'    => 'rejected',
                    ])
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

                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('approve_btn')
                    ->label('Setujui')
                    ->requiresConfirmation()
                    ->color('success')
                    ->disabled(function (TermintSppPpk $record) {

                        if ($record->ppspm_verification_status == 'in_progress') {
                            return false;
                        }

                        return true;
                    })
                    ->action(function (TermintSppPpk $record, array $data) {

                        $record->update([
                            'ppspm_verification_status'     =>  'approved',
                            'ppspm_id'                      =>  get_auth_user()->spm->id,
                        ]);

                        Notification::make('x_not')
                            ->title('Permohonan Pembayaran Diterima')
                            ->body('Pengajuan Pembayaran #' . $record->contract_number . ' Diterima')
                            ->send();
                    })
                    ->icon('heroicon-o-check-circle'),

                Tables\Actions\Action::make('reject_btn')
                    ->label('Tolak')
                    ->requiresConfirmation()
                    ->disabled(function (TermintSppPpk $record) {

                        if ($record->ppspm_verification_status == 'in_progress') {
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
                    ->action(function (TermintSppPpk $record, array $data) {

                        $record->update([
                            'ppspm_verification_status'   => 'rejected',
                            'ppspm_rejection_reason'      =>  $data['reject_reason'],
                            'ppspm_id'                    =>  get_auth_user()->spm->id,
                        ]);

                        Notification::make('x_not')
                            ->title('Permohonan Pembayaran ditolak')
                            ->body('Berhasil Menolak Permohonan Dengan alasan ' . "' $record->ppspm_rejection_reason '")
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
