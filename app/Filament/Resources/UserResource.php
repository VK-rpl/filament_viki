<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                
                TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->hiddenOn('edit'),

                FileUpload::make('profile_photo')
                    ->label('Foto Profil')
                    ->image()
                    ->directory('profile_photos')
                    ->disk('public')
                    ->visibility('public')
                    ->nullable(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->label('Foto Profil')
                    ->url(fn ($record) => $record->getFilamentAvatarUrl()) // Menggunakan accessor
                    ->disk('public')
                    ->circular(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('resetPassword')
                    ->label('Reset Password')
                    ->requiresConfirmation(
                        'Konfirmasi Reset Password',
                        'Anda yakin ingin mereset password?',
                        'heroicon-s-exclamation',
                        'warning'
                    )
                    ->action(fn (User $user) => self::resetPassword($user))
                    ->color('warning'),
            ]);
    }

    public static function afterSave($record): void
    {
        if (request()->hasFile('profile_photo')) {
            // Hapus foto profil yang lama jika ada
            if ($record->getOriginal('profile_photo')) {
                Storage::disk('public')->delete($record->getOriginal('profile_photo'));
            }

            // Simpan foto profil yang baru
            $path = request()->file('profile_photo')->store('profile_photos', 'public');
            $record->profile_photo = $path;
            $record->saveQuietly();

            // Log untuk debugging
            \Log::info('Profile photo updated successfully for user ID: ' . $record->id);
        }
    }

    public static function resetPassword(User $user): void
    {
        // Reset password ke "12345678"
        $user->password = bcrypt('12345678');
        $user->save();

        Notification::make()
            ->title('Sukses')
            ->body('Password telah direset.')
            ->success()
            ->send();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}