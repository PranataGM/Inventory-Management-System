<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Sistem';
    protected static ?string $navigationLabel = 'Audit Trail';
    protected static ?string $pluralModelLabel = 'Log Aktivitas';

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }
    public static function canViewAny(): bool { return auth()->user()->hasRole('Admin'); }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('action')
                    ->label('Aksi')
                    ->colors([
                        'primary' => 'login',
                        'danger' => 'logout',
                        'warning' => 'failed_login',
                        'success' => 'create',
                        'info' => 'update',
                        'danger' => 'delete',
                    ]),
                Tables\Columns\TextColumn::make('model_type')
                    ->label('Target')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'failed_login' => 'Failed Login',
                        'create' => 'Create Data',
                        'update' => 'Update Data',
                        'delete' => 'Delete Data',
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user.name')->label('Pengguna'),
                Forms\Components\TextInput::make('action')->label('Aksi'),
                Forms\Components\TextInput::make('model_type')->label('Model'),
                Forms\Components\TextInput::make('model_id')->label('ID Model'),
                Forms\Components\TextInput::make('ip_address')->label('IP Address'),
                Forms\Components\Textarea::make('user_agent')->label('User Agent'),
                Forms\Components\KeyValue::make('details')->label('Detail Perubahan (JSON)'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAuditLogs::route('/'),
        ];
    }
}
