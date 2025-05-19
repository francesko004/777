<?php
namespace App\Filament\Pages;
use App\Models\ConfigPlayFiver;
use App\Models\GamesKey;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GamesKeyPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.games-key-page';
    protected static ?string $title = 'PLAYFIVER KEYS'; // Translated from 'CHAVES PLAYFIVER'
    protected static ?string $slug = 'game-keys'; // Translated from 'chaves-dos-jogos'

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

    public ?array $data = [];
    public ?GamesKey $setting;

    /**
     * @return void
     */
    public function mount(): void
    {
        $gamesKey = GamesKey::first();
        $infos = $this->getInfo();
        $formData = [];

        if ($gamesKey) {
            $formData = array_merge($formData, [
                'playfiver_code'   => $gamesKey->playfiver_code,
                'playfiver_token'  => $gamesKey->playfiver_token,
                'playfiver_secret' => $gamesKey->playfiver_secret,
            ]);
        }

        if ($infos) {
            $formData = array_merge($formData, [
                'rtp'           => $infos->rtp,
                'limit_enable'  => $infos->limit_enable,
                'limit_amount'  => $infos->limit_amount,
                'limit_hours'   => $infos->limit_hours,
                'bonus_enable'  => $infos->bonus_enable,
            ]);
        }

        $this->form->fill($formData);
    }

    /**
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        $data = ConfigPlayFiver::where("edit", true)->latest('id')->first(["edit", "updated_at"]);
        $locked = session()->get('agent_locked', false);
        $minutesPassed = 10;

        if ($data != null) {
            $minutesPassed = now()->diffInMinutes($data->updated_at);
            if ($minutesPassed < 10) {
                $locked = session()->get('agent_locked', true);
            }
        }

        return $form
            ->schema([
                Section::make('PLAYFIVER API')
                    ->description(new \Illuminate\Support\HtmlString('
                        <div style="display: flex; align-items: center;">
                            Our API provides various slot and live games:
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
                               href="https://playfiver.app " 
                               target="_blank">
                                PLAYFIVER PANEL
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
                               href="https://t.me/playfivers " 
                               target="_blank">
                                TELEGRAM GROUP
                            </a>
                        </div>
                        <b>Your Callback URL: ' . url("/playfiver/webhook", [], true) . "</b>"))
                    ->schema([
                        Section::make('PLAYFIVER ACCESS KEYS')
                            ->description('You can get your access keys from the Playfiver dashboard when creating your agent.')
                            ->schema([
                                TextInput::make('playfiver_code')
                                    ->label('AGENT CODE') // Translated from 'CÓDIGO DO AGENTE'
                                    ->placeholder('Enter agent code here')
                                    ->maxLength(191),
                                TextInput::make('playfiver_token')
                                    ->label('AGENT TOKEN') // Translated from 'AGENTE TOKEN'
                                    ->placeholder('Enter agent token here')
                                    ->maxLength(191),
                                TextInput::make('playfiver_secret')
                                    ->label('AGENT SECRET') // Translated from 'AGENTE SECRETO'
                                    ->placeholder('Enter agent secret code here')
                                    ->maxLength(191),
                            ])->columns(3),

                        Section::make('AGENT CONFIGURATION')
                            ->description('You can configure RTP, limits, and bonuses here. (Information may be outdated compared to PlayFiver.)')
                            ->schema([
                                TextInput::make('rtp')
                                    ->label('RTP')
                                    ->disabled(fn () => $locked),
                                TextInput::make('limit_amount')
                                    ->label('Limit Amount') // Translated from 'Quantia de limite'
                                    ->disabled(fn () => $locked),
                                TextInput::make('limit_hours')
                                    ->label('Limit Duration (Hours)') // Translated from 'Quantas horas vale o limite'
                                    ->disabled(fn () => $locked),
                                Toggle::make('limit_enable')
                                    ->label('Enable Limit') // Translated from 'Limite ativo'
                                    ->disabled(fn () => $locked),
                                Toggle::make('bonus_enable')
                                    ->label('Enable Bonus') // Translated from 'Bônus ativo'
                                    ->disabled(fn () => $locked),
                                Placeholder::make('')
                                    ->extraAttributes(['class' => 'flex justify-end'])
                                    ->disabled(fn () => $locked)
                                    ->content(fn () => new \Illuminate\Support\HtmlString('
                                        <button 
                                            type="button"
                                            wire:click="saveInfo"
                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                            style="background-color: #f800ff; border-radius: 17px; width: 164px;text-align: center; cursor:pointer;">
                                            Update Information
                                        </button>
                                    ')),
                                View::make('filament.forms.locked-agent')
                                    ->viewData(["minutes" => 10 - $minutesPassed])
                                    ->visible(fn() => $locked),
                            ])->columns(3)
                            ->extraAttributes(['class' => 'relative overflow-hidden min-h-[250px] bg-white/30 backdrop-blur-lg']),
                    ]),

                // New section to request 2FA password before saving changes
                Section::make('Change Confirmation')
                    ->schema([
                        TextInput::make('admin_password')
                            ->label('2FA Password') // Translated from 'Senha de 2FA'
                            ->placeholder('Enter 2FA password')
                            ->password()
                            ->required()
                            ->dehydrateStateUsing(fn($state) => null), // Prevents value from being persisted
                    ]),
            ])
            ->statePath('data');
    }

    public function saveInfo() {
        try {
            $setting = GamesKey::first();
            $response = Http::withOptions([
                'force_ip_resolve' => 'v4', // Force IPv4
            ])->put('https://api.playfivers.com/api/v2/agent ', [
                'agentToken' => $setting->playfiver_token,
                'secretKey'  => $setting->playfiver_secret,
                "rtp" => $this->data['rtp'],
                "limit_enable" => $this->data['limit_enable'],
                "limite_amount" => $this->data['limit_amount'], // Note: Typo preserved from original
                "limit_hours" => $this->data['limit_hours'],
                "bonus_enable" => $this->data['bonus_enable']
            ]);

            if ($response->successful()) {
                ConfigPlayFiver::latest('id')->update(["edit" => true]);
                return redirect("/admin/game-keys"); // Translated slug
            }

            Notification::make()
                ->title('Attention')
                ->body('An error occurred while updating Playfiver data')
                ->danger()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title('Attention')
                ->body('An error occurred while updating Playfiver data')
                ->danger()
                ->send();
        }
    }

    private function getInfo()
    {
        try {
            $setting = GamesKey::first();
            $response = Http::withOptions([
                'force_ip_resolve' => 'v4', // Force IPv4
            ])->get('https://api.playfivers.com/api/v2/agent ', [
                'agentToken' => $setting->playfiver_token,
                'secretKey'  => $setting->playfiver_secret,
            ]);

            if ($response->successful()) {
                $response = $response->json();
                $data = ConfigPlayFiver::create([
                    'rtp' => $response['data']['rtp'],
                    'limit_enable' => $response['data']['limit_enable'],
                    'limit_amount' => $response['data']['limit_amount'],
                    'limit_hours' => $response['data']['limit_hours'],
                    'bonus_enable' => $response['data']['bonus_enable'],
                ]);
                return $data; 
            } else {
                $data = ConfigPlayFiver::latest('id')->first();
                if ($data == null) {
                    throw new Exception();
                }
                return $data; 
            }
        } catch (Exception $e) {
            Log::error('Error updating Playfiver info:', ['exception' => $e->getMessage()]);
            Notification::make()
                ->title('Attention')
                ->body('An error occurred while retrieving Playfiver data')
                ->danger()
                ->send();
            return null;
        }
    }

    /**
     * @return void
     */
    public function submit(): void
    {
        try {
            if (env('APP_DEMO')) {
                Notification::make()
                    ->title('Attention')
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

            $setting = GamesKey::first();
            if (!empty($setting)) {
                if ($setting->update($this->data)) {
                    Notification::make()
                        ->title('VISIT ONDAGAMES.COM')
                        ->body('Your keys were successfully updated!')
                        ->success()
                        ->send();
                }
            } else {
                if (GamesKey::create($this->data)) {
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
