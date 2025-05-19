<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Illuminate\Support\HtmlString;
use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
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

class RolloverSetting extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    protected static string $resource = SettingResource::class;

    protected static string $view = 'filament.resources.setting-resource.pages.rollover-setting';

    /**
     * Determine if the user can view this page.
     * 
     * @param Model $record
     * @return bool
     */
    public static function canView(Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Get the page title.
     * 
     * @return string|Htmlable
     */
    public function getTitle(): string|Htmlable
    {
        return __('ROLLOVER SYSTEM');
    }

    public Setting $record;
    public ?array $data = [];

    /**
     * Mount the component and initialize form with settings data.
     * 
     * @return void
     */
    public function mount(): void
    {
        $setting = Setting::first();
        $this->record = $setting;
        $this->form->fill($setting->toArray());
    }

    /**
     * Save the updated settings.
     * 
     * @return void
     */
    public function save()
    {
        try {
            if (env('APP_DEMO')) {
                Notification::make()
                    ->title('Attention')
                    ->body('You cannot make changes in the demo version.')
                    ->danger()
                    ->send();
                return;
            }

            $setting = Setting::find($this->record->id);

            if ($setting->update($this->data)) {
                Cache::put('setting', $setting);

                Notification::make()
                    ->title('ACCESS ONDAGAMES.COM')
                    ->body('Changes saved successfully!')
                    ->success()
                    ->send();
            }
        } catch (Halt $exception) {
            return;
        }
    }

    /**
     * Build the form schema.
     * 
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
                Section::make('BONUS AND DEPOSIT PROTECTION SYSTEM')
                    ->description('Protection to prevent withdrawals without wagering and money laundering.')
                    ->schema([
                        TextInput::make('rollover_deposit')
                            ->label('DEPOSIT ROLLOVER')
                            ->numeric()
                            ->default(1)
                            ->suffix('x')
                            ->helperText('The multiplication factor for the deposit — recommended = 2')
                            ->maxLength(191),

                        Group::make()->schema([
                            TextInput::make('rollover')
                                ->label('BONUS ROLLOVER')
                                ->numeric()
                                ->default(1)
                                ->suffix('x')
                                ->helperText('The multiplication factor for the bonus — recommended = 5')
                                ->maxLength(191),
                            TextInput::make('rollover_protection')
                                ->label('Bonus Rollover Protection')
                                ->numeric()
                                ->default(1)
                                ->suffix('x')
                                ->helperText('Define the minimum number of transactions to clear the rollover')
                                ->maxLength(191),
                        ])->columns(2),

                        Toggle::make('disable_rollover')
                            ->label('Disable Rollover')
                            ->helperText('If unchecked, rollover for bonus and deposit is active')
                    ])->columns(2),
            ])
            ->statePath('data');
    }
}
