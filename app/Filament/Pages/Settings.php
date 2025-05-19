<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Filament\Pages;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Components\Actions\Action;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.settings';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $modelLabel = 'Settings';
    protected static ?string $title = 'Settings';
    protected static ?string $slug = 'settings';

    public ?array $data = [];
    public Setting $setting;

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
    public Setting $setting;

    public function mount(): void
    {
        $this->setting = Setting::first();
        $data = $this->setting->toArray();

        $logoWhite = $data['software_logo_white'] ?? null;
        $logoBlack = $data['software_logo_black'] ?? null;

        if (is_array($logoWhite) || is_object($logoWhite)) {
            if (!empty($logoWhite)) {
                $this->data['software_logo_white'] = $this->uploadFile($logoWhite);
            }
        }

        if (is_array($logoBlack) || is_object($logoBlack)) {
            if (!empty($logoBlack)) {
                $this->data['software_logo_black'] = $this->uploadFile($logoBlack);
            }
        }

        $this->form->fill($data);
    }

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
                    '))
                    ->schema([
                        Section::make('GENERAL SETTINGS')
                            ->description('Configure general platform settings')
                            ->schema([
                                TextInput::make('software_name')
                                    ->label('Platform Name')
                                    ->placeholder('Enter platform name')
                                    ->maxLength(191),
                                FileUpload::make('software_logo_white')
                                    ->label('Light Logo')
                                    ->placeholder('Upload a light logo')
                                    ->image(),
                                FileUpload::make('software_logo_black')
                                    ->label('Dark Logo')
                                    ->placeholder('Upload a dark logo')
                                    ->image(),
                            ])->columns(3),

                        Section::make('DEPOSITS & WITHDRAWALS')
                            ->schema([
                                TextInput::make('min_deposit')
                                    ->label('Minimum Deposit')
                                    ->numeric()
                                    ->maxLength(191),
                                TextInput::make('max_deposit')
                                    ->label('Maximum Deposit')
                                    ->numeric()
                                    ->maxLength(191),
                                TextInput::make('min_withdrawal')
                                    ->label('Minimum Withdrawal')
                                    ->numeric()
                                    ->maxLength(191),
                                TextInput::make('max_withdrawal')
                                    ->label('Maximum Withdrawal')
                                    ->numeric()
                                    ->maxLength(191),
                                TextInput::make('rollover')
                                    ->label('Rollover')
                                    ->numeric()
                                    ->maxLength(191),
                            ])->columns(5),

                        Section::make('AFFILIATE SETTINGS')
                            ->schema([
                                TextInput::make('affiliate_commission')
                                    ->label('Affiliate Commission (%)')
                                    ->numeric()
                                    ->suffix('%')
                                    ->maxLength(191),
                                TextInput::make('affiliate_max_loss')
                                    ->label('Max Loss Limit')
                                    ->numeric()
                                    ->helperText('This option allows the affiliate to accumulate negative balances from their referred users\' losses.')
                                    ->maxLength(191),
                                TextInput::make('ngr_percent')
                                    ->label('NGR (%)')
                                    ->numeric()
                                    ->suffix('%')
                                    ->maxLength(191),
                            ])->columns(3),

                        Section::make('GENERAL DATA')
                            ->schema([
                                TextInput::make('initial_bonus')
                                    ->label('Initial Bonus (%)')
                                    ->numeric()
                                    ->suffix('%')
                                    ->maxLength(191),
                                TextInput::make('currency_code')
                                    ->label('Currency')
                                    ->placeholder('Currency code (e.g., BRL)')
                                    ->maxLength(191),
                                Select::make('decimal_format')
                                    ->label('Decimal Format')
                                    ->options(['dot' => 'Dot']),
                                Select::make('currency_position')
                                    ->label('Currency Position')
                                    ->options(['left' => 'Left', 'right' => 'Right']),
                            ])->columns(4),

                        Section::make('CHANGE CONFIRMATION')
                            ->schema([
                                TextInput::make('admin_password')
                                    ->label('2FA Password')
                                    ->placeholder('Enter 2FA password')
                                    ->password()
                                    ->required()
                                    ->dehydrateStateUsing(fn($state) => null),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    private function uploadFile($file)
    {
        if (is_string($file)) {
            return [$file];
        }

        if (!empty($file) && (is_array($file) || is_object($file))) {
            foreach ($file as $temporaryFile) {
                if ($temporaryFile instanceof TemporaryUploadedFile) {
                    $path = Core::upload($temporaryFile);
                    return [$path['path'] ?? $temporaryFile];
                }
                return [$temporaryFile];
            }
        }
        return null;
    }

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

            $setting = Setting::first();
            if (!empty($setting)) {
                $envs = DotenvEditor::load(base_path('.env'));
                $envs->setKeys([
                    'APP_NAME' => $this->data['software_name'],
                ]);
                $envs->save();

                if ($setting->update($this->data)) {
                    Cache::put('setting', $setting);
                    Notification::make()
                        ->title('VISIT ONDAGAMES.COM')
                        ->body('Data updated successfully!')
                        ->success()
                        ->send();
                    redirect(route('filament.admin.pages.dashboard-admin'));
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
