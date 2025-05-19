<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use App\Models\User;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\HtmlString;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Contracts\Support\Htmlable;

class DefaultSetting extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    protected static string $resource = SettingResource::class;

    protected static string $view = 'filament.resources.setting-resource.pages.default-setting';

    public static function canView(Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function getTitle(): string | Htmlable
    {
        return __('ADJUST THE PLATFORM DATA'); 
    }

    public Setting $record;
    public ?array $data = [];

    public function mount(): void
    {   
        $envs = DotenvEditor::load(base_path('.env'));
        $setting = Setting::first();
        $this->record = $setting;
        $this->record->url_env = $envs->getValue("FILAMENT_BASE_URL");
        $this->form->fill($setting->toArray());
    }

    public function save()
    {
        try {
            if(env('APP_DEMO')) {
                Notification::make()
                    ->title('Attention')
                    ->body('You cannot make this change in the demo version')
                    ->danger()
                    ->send();
                return;
            }

            $setting = Setting::find($this->record->id);

            $favicon   = $this->data['software_favicon'];
            $logoWhite = $this->data['software_logo_white'];
            $logoBlack = $this->data['software_logo_black'];
            $softwareBackground = $this->data['software_background'];

            if (is_array($softwareBackground) || is_object($softwareBackground)) {
                if(!empty($softwareBackground)) {
                    $this->data['software_background'] = $this->uploadFile($softwareBackground);

                    if(is_array($this->data['software_background'])) {
                        unset($this->data['software_background']);
                    }
                }
            }

            if (is_array($favicon) || is_object($favicon)) {
                if(!empty($favicon)) {
                    $this->data['software_favicon'] = $this->uploadFile($favicon);

                    if(is_array($this->data['software_favicon'])) {
                        unset($this->data['software_favicon']);
                    }
                }
            }

            if (is_array($logoWhite) || is_object($logoWhite)) {
                if(!empty($logoWhite)) {
                    $this->data['software_logo_white'] = $this->uploadFile($logoWhite);

                    if(is_array($this->data['software_logo_white'])) {
                        unset($this->data['software_logo_white']);
                    }
                }
            }

            if (is_array($logoBlack) || is_object($logoBlack)) {
                if(!empty($logoBlack)) {
                    $this->data['software_logo_black'] = $this->uploadFile($logoBlack);

                    if(is_array($this->data['software_logo_black'])) {
                        unset($this->data['software_logo_black']);
                    }
                }
            }

            $envs = DotenvEditor::load(base_path('.env'));

            $envs->setKeys([
                'APP_NAME' => $this->data['software_name'],
                'FILAMENT_BASE_URL' => $this->data['url_env']
            ]);

            $envs->save();

            if($setting->update($this->data)) {
                Cache::put('setting', $setting);

                Notification::make()
                    ->title('Data updated')
                    ->body('Data successfully updated!')
                    ->success()
                    ->send();

                redirect(route('filament.admin.resources.settings.index'));
            }
        } catch (Halt $exception) {
            return;
        }
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
                Section::make('EDIT LOGO AND DATA')
                    ->description('Edit the platform logo and data')
                    ->schema([
                        Group::make()->schema([
                            TextInput::make('software_name')
                                ->label('PLATFORM NAME')
                                ->placeholder('Enter the platform name')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('software_description')
                                ->placeholder('Enter the platform description')
                                ->label('PLATFORM DESCRIPTION')
                                ->maxLength(191),
                            TextInput::make('url_env')
                                ->label('ADMIN PANEL URL')
                                ->placeholder('Enter the admin panel URL')
                                ->required()
                                ->maxLength(191),
                        ])->columns(2),
                        Group::make()->schema([
                            FileUpload::make('software_favicon')
                                ->label('FAVICON | --> [ 52x52 ]')
                                ->placeholder('Upload a favicon')
                                ->image(),
                            Group::make()->schema([
                                FileUpload::make('software_logo_white')
                                    ->label('LOGO 1  | --> [ 1228 x 274 ] DISPLAYED ON HOMEPAGE')
                                    ->placeholder('Upload a white logo')
                                    ->image()
                                    ->columnSpanFull(),
                                FileUpload::make('software_logo_black')
                                    ->label('LOGO 2  | --> [ 400x100 ] DISPLAYED ON LOADING')
                                    ->placeholder('Upload an image')
                                    ->image()
                                    ->columnSpanFull()
                            ])
                        ])->columns(2),
                    ])
            ])
            ->statePath('data') ;
    }

    private function uploadFile($array)
    {
        if (!is_array($array) && !is_object($array)) {
            return $array;
        }
    
        if(!empty($array)) {
            foreach ($array as $k => $temporaryFile) {
                if ($temporaryFile instanceof TemporaryUploadedFile) {
                    $path = \Helper::upload($temporaryFile);
                    if($path) {
                        return $path['path'];
                    }
                } else {
                    return $temporaryFile;
                }
            }
        }
    }
}
