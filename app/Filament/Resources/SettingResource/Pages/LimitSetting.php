<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Support\HtmlString;
use App\Models\User;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LimitSetting extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    protected static string $resource = SettingResource::class;

    protected static string $view = 'filament.resources.setting-resource.pages.limit-setting';

    /**
     * @dev  
     * @param Model $record
     * @return bool
     */
    public static function canView(Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * @return string|Htmlable
     */
    public function getTitle(): string | Htmlable
    {
        return __('WITHDRAWAL LIMIT PER PERIOD');
    }

    public Setting $record;
    public ?array $data = [];

    /**
     * @dev  
     * @return void
     */
    public function mount(): void
    {
        $setting = Setting::first();
        $this->record = $setting;
        $this->form->fill($setting->toArray());
    }

    /**
     * @dev  
     * @return void
     */
    public function save()
    {
        try {
            if (env('APP_DEMO')) {
                Notification::make()
                    ->title('Warning')
                    ->body('You cannot perform this action in the demo version')
                    ->danger()
                    ->send();
                return;
            }

            $setting = Setting::find($this->record->id);

            if ($setting->update($this->data)) {
                Cache::put('setting', $setting);

                Notification::make()
                    ->title('Data updated')
                    ->body('Data successfully updated!')
                    ->success()
                    ->send();

                redirect(route('filament.admin.resources.settings.limit', ['record' => $this->record->id]));
            }
        } catch (Halt $exception) {
            return;
        }
    }

    /**
     * @dev  
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
                           href="https://ondagames.com"
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
                           href="https://t.me/ondagames_oficial"
                           target="_blank">
                            TELEGRAM GROUP
                        </a>
                    </div>
                ')),
                Section::make('ADJUST THE LIMIT PER PERIOD')
                    ->description('You can set how much a user can withdraw per period.')
                    ->schema([
                        TextInput::make('withdrawal_limit')
                            ->label('HOW MUCH CAN A USER WITHDRAW?')
                            ->numeric(),
                        Select::make('withdrawal_period')
                            ->label('WHAT IS THE WITHDRAWAL PERIOD?')
                            ->options([
                                'daily' => 'DAY',
                                'weekly' => 'WEEK',
                                'monthly' => 'MONTH',
                                'yearly' => 'YEAR',
                            ]),
                    ])->columns(2)
            ])
            ->statePath('data');
    }
}
