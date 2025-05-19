<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;

class ChangePasswordUser extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    public User $record;
    public ?array $data = [];

    protected static string $resource = UserResource::class;
    protected static string $view = 'filament.resources.user-resource.pages.change-password-user';
    protected static ?string $title = 'Change Password';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function save()
    {
        // 1. Check if the extra password (admin_password) matches the one defined in .env
        if (!isset($this->data['admin_password']) || $this->data['admin_password'] !== env('TOKEN_DE_2FA')) {
            Notification::make()
                ->title('Incorrect Password')
                ->body('The confirmation password does not match.')
                ->danger()
                ->send();
            // Stop execution without saving
            return;
        }

        try {
            $user = User::find($this->record->id);
            // Update only if information has been validated (assuming 'confirm_password' is validated in the form)
            $user->update(['password' => $this->data['password']]);

            Notification::make()
                ->title('Password Changed')
                ->body('The password was successfully changed! The user will need to relogin.')
                ->success()
                ->send();
        } catch (Halt $exception) {
            return;
        }
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('CHANGE THE USER PASSWORD')
                ->description('After changing the password, the user will be logged out and will need to log in again.')
                ->schema([
                    TextInput::make('password')
                        ->label('USER PASSWORD')
                        ->placeholder('Enter the new password')
                        ->password()
                        ->required()
                        ->maxLength(191),
                    TextInput::make('confirm_password')
                        ->label('REPEAT PASSWORD')
                        ->placeholder('Confirm the new password')
                        ->password()
                        ->required()
                        ->confirmed() // Ensures the value matches the "password" field
                        ->maxLength(191),
                    // New field for the admin extra password
                    TextInput::make('admin_password')
                        ->label('2FA TOKEN')
                        ->placeholder('Enter the 2fa confirmation password')
                        ->password()
                        ->required()
                        ->maxLength(191),
                ])
                ->columns(2)
                ->statePath('data'),
        ];
    }
}
