<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkPackageResource\Pages;
use App\Filament\Resources\WorkPackageResource\RelationManagers;
use App\Models\WorkPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkPackageResource extends Resource
{
    protected static ?string $model = WorkPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $label =   'Tabel Paket Pekerjaan';

    protected static ?string $navigationLabel = 'Paket Pekerjaan';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Menu Utama';

    public static function canAccess(): bool
    {
        return auth()->user()->role == 'admin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('created_at', 'DESC');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Paket Pekerjaan')->columnSpanFull()->placeholder('Masukkan nama paket pekerjaan'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWorkPackages::route('/'),
        ];
    }
}
