<?php

namespace App\Filament\Pages;

use App\Models\SettingMail;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Filament\Forms\Components\Select;

class SettingMailPage extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.setting-mail-page';
    public ?array $data = [];
    public SettingMail $setting;

    /**
     * @dev  
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin'); // Controls full page access
    }
    
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin'); // Controls visibility of specific elements
    }

    /**
     * @return void
     */
    public function mount(): void
    {
        $smtp = SettingMail::first();
        if (!empty($smtp)) {
            $this->setting = $smtp;
            $this->form->fill($this->setting->toArray());
        } else {
            $this->form->fill();
        }
    }

    /**
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('ONDA GAMES CREATED THIS PLATFORM FOR YOU')
                    ->description(new HtmlString('
                        <div style="font-weight: 600; display: flex; align-items: center;">
                            LEARN MORE ABOUT US. JOIN OUR IGAMING COMMUNITY. ACCESS NOW! 
                            <a class="dark:text-white" 
                               style="
                                    font-size: 14px;
                                    font-weight: 600;
                                    width: 127px;
                                    display: flex;
                                    background-color: #f800ff;
                                    padding: 10px;
                                    border-radius: 11px;
                                    justify-content: center;
                                    margin-left: 10px;
                               " 
                               href="https://ondagames.com " 
                               target="_blank">
                                OFFICIAL SITE
                            </a>
                            <a class="dark:text-white" 
                               style="
                                    font-size: 14px;
                                    font-weight: 600;
                                    width: 127px;
                                    display: flex;
                                    background-color: #f800ff;
                                    padding: 10px;
                                    border-radius: 11px;
                                    justify-content: center;
                                    margin-left: 10px;
                               " 
                               href="https://t.me/ondagames_oficial " 
                               target="_blank">
                                TELEGRAM GROUP
                            </a>
                        </div>
                    ')),
                Section::make('EMAIL SERVER CREDENTIALS')
                    ->description('Enter your credentials for sending notification emails')
                    ->schema([
                        Select::make('software_smtp_type')
                            ->label('PROTOCOL')
                            ->placeholder('Select the mailer')
                            ->options([
                                'imap' => 'IMAP',
                                'smtp' => 'SMTP',
                                'pop' => 'POP',
                            ])
                            ->required(),
                        TextInput::make('software_smtp_mail_host')
                            ->label('SERVER ADDRESS')
                            ->placeholder('Enter your mail host')
                            ->maxLength(191),
                        TextInput::make('software_smtp_mail_port')
                            ->label('PORT')
                            ->placeholder('SERVER PORT')
                            ->maxLength(191),
                        TextInput::make('software_smtp_mail_username')
                            ->label('USERNAME')
                            ->placeholder('USER NAME')
                            ->maxLength(191),
                        TextInput::make('software_smtp_mail_password')
                            ->label('PASSWORD')
                            ->placeholder('USER PASSWORD')
                            ->maxLength(191),
                        Select::make('software_smtp_mail_encryption')
                            ->label('ENCRYPTION')
                            ->placeholder('Select encryption')
                            ->options([
                                'ssl' => 'SSL',
                                'tls' => 'TLS',
                            ])
                            ->required(),
                        TextInput::make('software_smtp_mail_from_address')
                            ->label('EMAIL HEADER')
                            ->placeholder('Enter Header Email Address')
                            ->maxLength(191),
                        TextInput::make('software_smtp_mail_from_name')
                            ->label('HEADER NAME')
                            ->placeholder('Enter Header Name')
                            ->maxLength(191),
                    ])->columns(4),
            ])
            ->statePath('data');
    }

    /**
     * @return void
     */
    public function submit(): void
    {
        try {
            if (env('APP_DEMO')) {
                Notification::make()
                    ->title('Warning')
                    ->body('You cannot make changes in demo mode')
                    ->danger()
                    ->send();
                return;
            }

            $setting = SettingMail::first();
            if (!empty($setting)) {
                if (!empty($this->data['software_smtp_type'])) {
                    $envs = DotenvEditor::load(base_path('.env'));
                    $envs->setKeys([
                        'MAIL_MAILER' => $this->data['software_smtp_type'],
                        'MAIL_HOST' => $this->data['software_smtp_mail_host'],
                        'MAIL_PORT' => $this->data['software_smtp_mail_port'],
                        'MAIL_USERNAME' => $this->data['software_smtp_mail_username'],
                        'MAIL_PASSWORD' => $this->data['software_smtp_mail_password'],
                        'MAIL_ENCRYPTION' => $this->data['software_smtp_mail_encryption'],
                        'MAIL_FROM_ADDRESS' => $this->data['software_smtp_mail_from_address'],
                        'MAIL_FROM_NAME' => $this->data['software_smtp_mail_from_name'],
                    ]);
                    $envs->save();
                }

                if ($setting->update($this->data)) {
                    Notification::make()
                        ->title('VISIT ONDAGAMES.COM')
                        ->body('Your keys were successfully updated!')
                        ->success()
                        ->send();
                }
            } else {
                if (SettingMail::create($this->data)) {
                    Notification::make()
                        ->title('VISIT ONDAGAMES.COM')
                        ->body('Your keys were successfully created!')
                        ->success()
                        ->send();
                }
            }
        } catch (Halt $exception) {
            Notification::make()
                ->title('Error updating data!')
                ->body('Error updating data!')
                ->danger()
                ->send();
        }
    }
}
