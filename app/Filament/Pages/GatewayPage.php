<?php

namespace App\Filament\Pages;

use App\Models\Gateway;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class GatewayPage extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.gateway-page';
    public ?array $data = [];
    public Gateway $setting;

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
        $gateway = Gateway::first();
        if (!empty($gateway)) {
            $this->setting = $gateway;
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

                Section::make('REGISTER YOUR GATEWAY API KEYS')
                    ->description('Configure API keys for payment gateways')
                    ->schema([
                        Section::make('DIGITO PAY')
                            ->description(new HtmlString('
                                <div style="display: flex; align-items: center;">
                                    Need an account with Digito Pay? Fill out the contact form and request your account:
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
                                       href="https://app.digitopayoficial.com.br/auth/login " 
                                       target="_blank">
                                        DASHBOARD
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
                                       href="https://wa.me/554888142566 " 
                                       target="_blank">
                                        MANAGER
                                    </a>
                                </div>
                            '))
                            ->schema([
                                TextInput::make('digito_uri')
                                    ->label('CLIENT URL')
                                    ->placeholder('Enter API URL')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                                TextInput::make('digito_client')
                                    ->label('CLIENT ID')
                                    ->placeholder('Enter client ID')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                                TextInput::make('digito_secret')
                                    ->label('CLIENT SECRET')
                                    ->placeholder('Enter client secret')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('BSPAY E PIXUP')
                            ->description(new HtmlString('
                                <div style="display: flex; align-items: center;">
                                    Need an account with Digito Pay? Fill out the contact form and request your account:
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
                                       href="https://dashboard.pixupbr.com/ " 
                                       target="_blank">
                                        DASHBOARD
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
                                       href="https://wa.me/557189320292 " 
                                       target="_blank">
                                        MANAGER
                                    </a>
                                </div>
                                <b>Your Webhook: ' . url("/bspay/callback", [], true) . "</b>"))
                            ->schema([
                                TextInput::make('bspay_uri')
                                    ->label('CLIENT URL')
                                    ->placeholder('Enter API URL')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                                TextInput::make('bspay_cliente_id')
                                    ->label('CLIENT ID')
                                    ->placeholder('Enter client ID')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                                TextInput::make('bspay_cliente_secret')
                                    ->label('CLIENT SECRET')
                                    ->placeholder('Enter client secret')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('EZZEPAY')
                            ->description(new HtmlString('
                                <div style="display: flex; align-items: center;">
                                    Need an account with Digito Pay? Fill out the contact form and request your account:
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
                                       href="https://app.ezzebank.com/login " 
                                       target="_blank">
                                        DASHBOARD
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
                                       href="https://wa.me/556192262660 " 
                                       target="_blank">
                                        MANAGER
                                    </a>
                                </div>
                                <b>Your Webhook: ' . url("/ezzepay/webhook", [], true) . "</b>"))
                            ->schema([
                                TextInput::make('ezze_uri')
                                    ->label('CLIENT URL')
                                    ->placeholder('Enter API URL')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                                TextInput::make('ezze_client')
                                    ->label('CLIENT ID')
                                    ->placeholder('Enter client ID')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                                TextInput::make('ezze_secret')
                                    ->label('CLIENT SECRET')
                                    ->placeholder('Enter client secret')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                                TextInput::make('ezze_user')
                                    ->label('WEBHOOK USER')
                                    ->placeholder('Enter webhook authentication username')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                                TextInput::make('ezze_senha')
                                    ->label('WEBHOOK PASSWORD')
                                    ->placeholder('Enter webhook authentication password')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('SUITEPAY')
                            ->description(new HtmlString('
                                <div style="display: flex; align-items: center;">
                                    Need an account with Digito Pay? Fill out the contact form and request your account:
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
                                       href="https://www.suitpay.app/ " 
                                       target="_blank">
                                        DASHBOARD
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
                                       href="https://wa.me/556299055428 " 
                                       target="_blank">
                                        MANAGER
                                    </a>
                                </div>
                                <b>To enable withdrawals, release the IP</b>'))
                            ->schema([
                                TextInput::make('suitpay_uri')
                                    ->label('CLIENT URL')
                                    ->placeholder('Enter API URL')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                                TextInput::make('suitpay_cliente_id')
                                    ->label('CLIENT ID')
                                    ->placeholder('Enter client ID')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                                TextInput::make('suitpay_cliente_secret')
                                    ->label('CLIENT SECRET')
                                    ->placeholder('Enter client secret')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Change Confirmation')
                            ->schema([
                                TextInput::make('admin_password')
                                    ->label('2FA Password')
                                    ->placeholder('Enter 2FA password')
                                    ->password()
                                    ->required()
                                    ->dehydrateStateUsing(fn($state) => null), // Prevents value from being persisted
                            ]),
                    ]),
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

            if (
                !isset($this->data['admin_password']) ||
                $this->data['admin_password'] !== env('TOKEN_DE_2FA')
            ) {
                Notification::make()
                    ->title('Access Denied')
                    ->body('2FA password is incorrect. You cannot update the data.')
                    ->danger()
                    ->send();
                return;
            }

            $setting = Gateway::first();
            if (!empty($setting)) {
                if ($setting->update($this->data)) {
                    if (!empty($this->data['stripe_public_key'])) {
                        $envs = DotenvEditor::load(base_path('.env'));
                        $envs->setKeys([
                            'STRIPE_KEY' => $this->data['stripe_public_key'],
                            'STRIPE_SECRET' => $this->data['stripe_secret_key'],
                            'STRIPE_WEBHOOK_SECRET' => $this->data['stripe_webhook_key'],
                        ]);
                        $envs->save();
                    }
                    Notification::make()
                        ->title('VISIT ONDAGAMES.COM')
                        ->body('Your keys were successfully updated!')
                        ->success()
                        ->send();
                }
            } else {
                if (Gateway::create($this->data)) {
                    Notification::make()
                        ->title('VISIT ONDAGAMES.COM')
                        ->body('Your keys were successfully created!')
                        ->success()
                        ->send();
                }
            }
        } catch (\Filament\Support\Exceptions\Halt $exception) {
            Notification::make()
                ->title('Error updating data!')
                ->body('Error updating data!')
                ->danger()
                ->send();
        }
    }
}
