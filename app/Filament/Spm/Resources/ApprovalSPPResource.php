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
use App\Models\Contract;
use App\Models\Document;
use App\Models\TermintSppPpkFile;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as ActionFile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Support\RawJs;
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

                self::getDataContract(),

                self::getFiles(),

                Fieldset::make('payment_request')
                    ->relationship('payment_request')
                    ->label('Dokumen Penyedia Jasa')
                    ->columns(2)
                    ->schema(self::getPenyediaFields()),


            ]);
    }

    protected static function getDocumentFields(bool $hasAdvancePayment)
    {
        return self::getFiles();
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

                TextColumn::make('contract.work_package')
                    ->label('Paket Pekerjaan')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),

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
                    ->formatStateUsing(function ($record) {
                        $contract = $record->contract;
                        return 'Rp. ' . number_format($contract->payment_value - $contract->paid_value, 0, ',', '.');
                    })
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([

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

                        $payment_request = $record->payment_request;

                        $payment_request->update([
                            'ppspm_verification_status'     =>  'approved',
                            'ppspm_id'                      =>  get_auth_user()->spm->id,
                            'verification_progress'         =>  'treasurer',
                            'treasurer_verification_status' =>  'in_progress',
                        ]);

                        Notification::make()
                            ->title('Permohonan Pembayaran Disetujui')
                            ->body('Pengajuan Pembayaran #' . $payment_request->contract_number . ' telah disetujui.')
                            ->sendToDatabase($payment_request->service_provider->user);

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

                        $payment_request = $record->payment_request;

                        $payment_request->update([
                            'kpa_verification_status'       =>  'rejected',
                            'treasurer_verification_status' =>  'rejected',
                            'ppspm_verification_status'     =>  'rejected',
                            'ppspm_rejection_reason'        =>  $data['reject_reason'],
                            'ppspm_id'                      =>  get_auth_user()->spm->id,
                            'request_reject'                =>  false,
                        ]);

                        Notification::make()
                            ->title('Permohonan Pembayaran Ditolak')
                            ->body('Permohonan Anda ditolak oleh PP-SPM')
                            ->sendToDatabase($payment_request->service_provider->user);

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
            'view' => Pages\ViewApprovalSPP::route('/{record}/view'),
        ];
    }

    public static function getPenyediaFields(): array
    {
        return [

            TextInput::make('contract_number')->label('Nomor Kontrak')
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

            self::getPDFs(),

        ];
    }


    public static function getPDFs()
    {
        return Repeater::make('documents')
            ->relationship()
            ->label('Daftar Dokumen Penyedia Jasa')
            ->columnSpanFull()
            ->grid(2)
            ->schema([

                TextInput::make('name')
                    ->formatStateUsing(function ($state) {
                        return str_replace('_', ' ', strtoupper($state));
                    })
                    ->label(''),

                Actions::make([

                    ActionFile::make('View')
                        ->icon('heroicon-o-eye')
                        ->label('Tampilkan')
                        ->url(function (Document $record) {

                            return asset('/storage/' . $record->path);
                        }, true),

                ])->inlineLabel(),

            ]);
    }


    public static function getFiles()
    {
        return Repeater::make('files')
            ->relationship()
            ->label('Daftar Dokumen PPK')
            ->columnSpanFull()
            ->grid(2)
            ->schema([

                TextInput::make('file_type')
                    ->formatStateUsing(function ($state) {
                        return str_replace('_', ' ', strtoupper($state));
                    })
                    ->label(''),

                Actions::make([

                    ActionFile::make('View')
                        ->icon('heroicon-o-eye')
                        ->label('Tampilkan')
                        ->url(function (TermintSppPpkFile $record) {

                            return asset('/storage/' . $record->file_path);
                        }, true),

                ])->inlineLabel(),

            ]);
    }

    public static function getDataContract()
    {
        return  Fieldset::make('Data Kontrak')
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

            ]);
    }
}
