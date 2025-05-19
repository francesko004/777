<?php

namespace App\Filament\Pages;

use App\Helpers\Core;
use App\Models\CustomLayout;
use Creagia\FilamentCodeField\CodeField;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class LayoutCssCustom extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.layout-css-custom';
    protected static ?string $navigationLabel = 'Layout Customization';
    protected static ?string $modelLabel = 'Layout Customization';
    protected static ?string $title = 'Layout Customization';
    protected static ?string $slug = 'custom-layout';

    public ?array $data = [];
    public CustomLayout $custom;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin'); // Controls full page access
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin'); // Controls visibility of specific elements
    }

    public function mount(): void
    {
        $this->custom = CustomLayout::first();
        $data = $this->custom->toArray();
        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getClearCacheSection(),
                $this->getSecTokenJivochat(),
                $this->css_do_bonus_diario(),
                $this->css_do_termos_sport(),
                $this->central_suporte(),
                $this->css_do_geral(),
                $this->css_do_menu_cell(),
                $this->css_do_missoes(),
                $this->css_do_vips(),
                $this->css_do_promocoes(),
                $this->css_do_BetHistory(),
                $this->css_do_WalletWithdrawal(),
                $this->css_do_PixWallet(),
                $this->css_do_WalletDeposit(),
                $this->css_do_WalletBalance(),
                $this->css_do_WalletDashboard(),
                $this->css_do_affiliates(),
                $this->css_do_login_registro_esquci(),
                $this->css_do_listgames(),
                $this->css_do_homepage(),
                $this->css_do_navbar(),
                $this->css_do_footer(),
                $this->css_do_sidebar(),
                $this->css_do_popup_cookies(),
                $this->css_do_myconta(),
                $this->getSectionPlatformTexts(),
                $this->getSectiimagensmanegem(),
                $this->getSectilinkmagem(),
                $this->getSectionCustomCode(),
            ])
            ->statePath('data');
    }

    protected function getClearCacheSection(): Section
    {
        return Section::make('Optimization')
            ->description('Click the button below to clear the entire system cache')
            ->schema([
                \Filament\Forms\Components\Placeholder::make('clear_cache')
                    ->label('')
                    ->content(new \Illuminate\Support\HtmlString(
                        '<div style="font-weight: 600; display: flex; align-items: center;">
                            <!-- Clear Cache Button -->
                            <a href="' . route('clear.cache') . '" class="dark:text-white" 
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
                               onclick="return confirm(\'Are you sure you want to clear the cache?\');">
                                CLEAR CACHE
                            </a>
                            <!-- Update Colors Button -->
                            <a href="' . route('update.colors') . '" class="dark:text-white" 
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
                               onclick="return confirm(\'Do you want to update colors?\');">
                                UPDATE COLORS
                            </a>
                            <!-- Clear Memory Button -->
                            <a href="' . route('clear.memory') . '" class="dark:text-white" 
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
                               onclick="return confirm(\'Are you sure you want to clear memory?\');">
                                CLEAR MEMORY
                            </a>
                        </div>'
                    )),
            ]);
    }

    protected function getSecTokenJivochat(): Section
    {
        return Section::make('JIVOCHAT TOKEN')
            ->label('Change the Jivochat token')
            ->schema([
                \Filament\Forms\Components\Placeholder::make('clear_cache')
                    ->label('')
                    ->content(new \Illuminate\Support\HtmlString(
                        '<div style="font-weight: 600; display: flex; align-items: center;">
                            <!-- Chat Site Button -->
                            <a href="https://www.jivochat.com.br/?partner_id=47634 " class="dark:text-white" 
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
                               target="_blank">
                                CHAT SITE
                            </a>
                        </div>'
                    )),
                TextInput::make("token_jivochat")
                    ->label("Jivochat Token Ex: //code.jivosite.com/widget/lmxxxxxxxx")
                    ->placeholder("Enter Jivochat token here Ex: //code.jivosite.com/widget/lmxxxxxxxx"),
            ])->columns(1);
    }

    protected function getSectilinkmagem(): Section
    {
        return Section::make('ADDITIONAL LINKS')
            ->label('Edit additional links')
            ->schema([
                TextInput::make("link_suporte")->label("Support Link"),
                TextInput::make("link_lincenca")->label("License Link"),
                TextInput::make("link_app")->label("App Link"),
                TextInput::make("link_telegram")->label("Telegram Link"),
                TextInput::make("link_facebook")->label("Facebook Link"),
                TextInput::make("link_whatsapp")->label("WhatsApp Link"),
                TextInput::make("link_instagram")->label("Instagram Link"),
            ])->columns(3);
    }

    protected function getSectiimagensmanegem(): Section
    {
        return Section::make('Banners & Images')
            ->label('Images and Banners')
            ->schema([
                FileUpload::make('image_hot4')->label("License Banner Image")->placeholder('Upload an image')->image(),
                FileUpload::make('banner_deposito1')->label("Deposit Banner")->placeholder('Upload an image')->image(),
                FileUpload::make('banner_deposito2')->label("QR Code Banner")->placeholder('Upload an image')->image(),
                FileUpload::make('banner_registro')->label("Register Banner")->placeholder('Upload an image')->image(),
                FileUpload::make('banner_login')->label("Login Banner")->placeholder('Upload an image')->image(),
                FileUpload::make('menucell_inicio')->label("Home Menu Cell Image")->placeholder('Upload an image')->image(),
                FileUpload::make('menucell_suporte')->label("Support Menu Cell Image")->placeholder('Upload an image')->image(),
                FileUpload::make('menucell_carteira')->label("Wallet Menu Cell Image")->placeholder('Upload an image')->image(),
                FileUpload::make('menucell_afiliado')->label("Affiliate Menu Cell Image")->placeholder('Upload an image')->image(),
                FileUpload::make('menucell_saque')->label("Withdrawal Menu Cell Image")->placeholder('Upload an image')->image(),
                FileUpload::make('menucell_sair')->label("Logout Menu Cell Image")->placeholder('Upload an image')->image(),
                FileUpload::make('footer_imagen1')->label("Footer Image 1")->placeholder('Upload an image')->image(),
                FileUpload::make('footer_imagen2')->label("Footer Image 2")->placeholder('Upload an image')->image(),
                FileUpload::make('footer_imagen3')->label("Footer Image 3")->placeholder('Upload an image')->image(),
                FileUpload::make('footer_telegram')->label("Footer Telegram Image")->placeholder('Upload an image')->image(),
                FileUpload::make('footer_facebook')->label("Footer Facebook Image")->placeholder('Upload an image')->image(),
                FileUpload::make('footer_whatsapp')->label("Footer WhatsApp Image")->placeholder('Upload an image')->image(),
                FileUpload::make('footer_instagram')->label("Footer Instagram Image")->placeholder('Upload an image')->image(),
                FileUpload::make('footer_mais18')->label("Footer +18 Image")->placeholder('Upload an image')->image(),
            ])->columns(4);
    }

    protected function getSectionCustomCode(): Section
    {
        return Section::make()
            ->schema([
                TextInput::make('idPixelFC')->label("Facebook Pixel ID"),
                TextInput::make('idPixelGoogle')->label("Google Pixel ID"),
                CodeField::make('custom_css')->setLanguage(CodeField::CSS)->withLineNumbers()->minHeight(100),
                CodeField::make('custom_js')->setLanguage(CodeField::JS)->withLineNumbers()->minHeight(100),
            ]);
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

    /////////////////////////////////////////////////////////////////////
    ///////////////////////// DESIGN CENTER ///////////////////////////////
    /////////////////////////////////////////////////////////////////////
    // NAVBAR PAGE   | TOP OF THE SITE
    protected function css_do_navbar(): Section
    {
        return Section::make("Navbar Page")
            ->description('You can change Navbar colors')
            ->label('Navbar')
            ->schema([
                ColorPicker::make('navbar_background')->label('Navbar Background Color')->required(),
                ColorPicker::make('navbar_text')->label('Navbar Text Color')->required(),
                ColorPicker::make('navbar_icon_menu')->label('Menu Icon Color')->required(),
                ColorPicker::make('navbar_icon_promocoes')->label('Promotions Icon Color')->required(),
                ColorPicker::make('navbar_icon_promocoes_segunda_cor')->label('Secondary Promotions Icon Color')->required(),
                ColorPicker::make('navbar_icon_casino')->label('Casino Icon Color')->required(),
                ColorPicker::make('navbar_icon_sport')->label('Sports Icon Color')->required(),
                ColorPicker::make('navbar_button_text_login')->label('Login Button Text Color')->required(),
                ColorPicker::make('navbar_button_text_registro')->label('Register Button Text Color')->required(),
                ColorPicker::make('navbar_button_background_login')->label('Login Button Background')->required(),
                ColorPicker::make('navbar_button_background_registro')->label('Register Button Background')->required(),
                ColorPicker::make('navbar_button_border_color')->label('Button Border Color')->required(),
                ColorPicker::make('navbar_button_text_superior')->label('Top Button Text Color')->required(),
                ColorPicker::make('navbar_button_background_superior')->label('Top Button Background')->required(),
                ColorPicker::make('navbar_text_superior')->label('Top Text Color')->required(),
                ColorPicker::make('navbar_button_deposito_background')->label('Deposit Button Background')->required(),
                ColorPicker::make('navbar_button_deposito_text_color')->label('Deposit Button Text Color')->required(),
                ColorPicker::make('navbar_button_deposito_border_color')->label('Deposit Button Border Color')->required(),
                ColorPicker::make('navbar_button_deposito_píx_color_text')->label('PIX Deposit Button Text Color')->required(),
                ColorPicker::make('navbar_button_deposito_píx_background')->label('PIX Deposit Button Background')->required(),
                ColorPicker::make('navbar_button_deposito_píx_icon')->label('PIX Deposit Button Icon Color')->required(),
                ColorPicker::make('navbar_button_carteira_background')->label('Wallet Button Background')->required(),
                ColorPicker::make('navbar_button_carteira_text_color')->label('Wallet Button Text Color')->required(),
                ColorPicker::make('navbar_button_carteira_border_color')->label('Wallet Button Border Color')->required(),
                ColorPicker::make('navbar_perfil_text_color')->label('Profile Text Color')->required(),
                ColorPicker::make('navbar_perfil_background')->label('Profile Background Color')->required(),
                ColorPicker::make('navbar_perfil_icon_color')->label('Profile Icon Color')->required(),
                ColorPicker::make('navbar_perfil_icon_color_border')->label('Profile Icon Border Color')->required(),
                ColorPicker::make('navbar_perfil_modal_icon_color')->label('Profile Modal Icon Color')->required(),
                ColorPicker::make('navbar_perfil_modal_text_modal')->label('Profile Modal Text Color')->required(),
                ColorPicker::make('navbar_perfil_modal_background_modal')->label('Profile Modal Background Color')->required(),
                ColorPicker::make('navbar_perfil_modal_hover_modal')->label('Profile Modal Hover Color')->required(),
            ])->columns(4);
    }

    // SIDEBAR PAGE | SIDEBAR OF THE SITE
    protected function css_do_sidebar(): Section
    {
        return Section::make("Sidebar Page")
            ->description('You can change Sidebar colors')
            ->label('Sidebar')
            ->schema([
                ColorPicker::make('sidebar_background')->label('Sidebar Background Color')->required(),
                ColorPicker::make('sidebar_button_missoes_background')->label('Missions Button Background')->required(),
                ColorPicker::make('sidebar_button_vip_background')->label('VIP Button Background')->required(),
                ColorPicker::make('sidebar_button_ganhe_background')->label('Promotions Button Background')->required(),
                ColorPicker::make('sidebar_button_bonus_background')->label('Bonus Button Background')->required(),
                ColorPicker::make('sidebar_button_missoes_text')->label('Missions Button Text')->required(),
                ColorPicker::make('sidebar_button_ganhe_text')->label('Promotions Button Text')->required(),
                ColorPicker::make('sidebar_button_vip_text')->label('VIP Button Text')->required(),
                ColorPicker::make('sidebar_button_hover')->label('Button Hover Color')->required(),
                ColorPicker::make('sidebar_text_hover')->label('Text Hover Color')->required(),
                ColorPicker::make('sidebar_text_color')->label('Sidebar Text Color')->required(),
                ColorPicker::make('sidebar_border')->label('Sidebar Border Color')->required(),
                ColorPicker::make('sidebar_icons')->label('Sidebar Icon Color')->required(),
                ColorPicker::make('sidebar_icons_background')->label('Sidebar Icon Background')->required(),
            ])->columns(4);
    }

    // HOMEPAGE | SITE HOME PAGE
    protected function css_do_homepage(): Section
    {
        return Section::make("Homepage")
            ->description('You can change Homepage colors')
            ->label('Homepage')
            ->schema([
                ColorPicker::make('home_text_color')->label('Homepage Text Color')->required(),
                ColorPicker::make('home_background')->label('Homepage Background Color')->required(),
                ColorPicker::make('home_background_button_banner')->label('Banner Button Background')->required(),
                ColorPicker::make('home_icon_color_button_banner')->label('Banner Button Icon Color')->required(),
                ColorPicker::make('home_background_input_pesquisa')->label('Search Input Background')->required(),
                ColorPicker::make('home_icon_color_input_pesquisa')->label('Search Input Icon Color')->required(),
                ColorPicker::make('home_border_color_input_pesquisa')->label('Search Input Border Color')->required(),
                ColorPicker::make('topo_icon_color')->label('Back-to-top Icon Color')->required(),
                ColorPicker::make('topo_button_text_color')->label('Back-to-top Button Text')->required(),
                ColorPicker::make('topo_button_background')->label('Back-to-top Button Background')->required(),
                ColorPicker::make('topo_button_border_color')->label('Back-to-top Button Border')->required(),
                ColorPicker::make('home_background_categorias')->label('Categories Background')->required(),
                ColorPicker::make('home_text_color_categorias')->label('Categories Text Color')->required(),
                ColorPicker::make('home_background_pesquisa')->label('Search Background')->required(),
                ColorPicker::make('home_text_color_pesquisa')->label('Search Text Color')->required(),
                ColorPicker::make('home_background_pesquisa_aviso')->label('Search Warning Background')->required(),
                ColorPicker::make('home_text_color_pesquisa_aviso')->label('Search Warning Text Color')->required(),
                ColorPicker::make('home_background_button_pesquisa')->label('Search Clear Button Background')->required(),
                ColorPicker::make('home_icon_color_button_pesquisa')->label('Search Clear Button Icon')->required(),
                ColorPicker::make('home_background_button_vertodos')->label('View All Button Background')->required(),
                ColorPicker::make('home_text_color_button_vertodos')->label('View All Button Text Color')->required(),
                ColorPicker::make('home_background_button_jogar')->label('Play Button Background')->required(),
                ColorPicker::make('home_text_color_button_jogar')->label('Play Button Text Color')->required(),
                ColorPicker::make('home_icon_color_button_jogar')->label('Play Button Icon Color')->required(),
                ColorPicker::make('home_hover_jogar')->label('Play Button Hover Color')->required(),
            ])->columns(4);
    }

    // FOOTER PAGE | BOTTOM OF THE SITE
    protected function css_do_footer(): Section
    {
        return Section::make("Footer Page")
            ->description('You can change Footer colors')
            ->label('Footer')
            ->schema([
                ColorPicker::make('footer_background')->label('Footer Background Color')->required(),
                ColorPicker::make('footer_text_color')->label('Footer Text Color')->required(),
                ColorPicker::make('footer_links')->label('Footer Links Color')->required(),
                ColorPicker::make('footer_button_background')->label('Footer Button Background')->required(),
                ColorPicker::make('footer_button_text')->label('Footer Button Text Color')->required(),
                ColorPicker::make('footer_button_border')->label('Footer Button Border Color')->required(),
                ColorPicker::make('footer_icons')->label('Footer Icon Color')->required(),
                ColorPicker::make('footer_titulos')->label('Footer Titles Color')->required(),
                ColorPicker::make('footer_button_hover_language')->label('Footer Language Button Hover')->required(),
                ColorPicker::make('footer_button_color_language')->label('Footer Language Button Text')->required(),
                ColorPicker::make('footer_button_background_language')->label('Footer Language Button Background')->required(),
                ColorPicker::make('footer_button_border_language')->label('Footer Language Button Border')->required(),
                ColorPicker::make('footer_background_language')->label('Footer Language Background')->required(),
            ])->columns(4);
    }

    // TERMS AND SPORT PAGE
    protected function css_do_termos_sport(): Section
    {
        return Section::make("Terms and Sport Page")
            ->description('You can change Terms and Sport page colors')
            ->label('Terms and Sport')
            ->schema([
                ColorPicker::make('aviso_sport_background')->label('Sport Warning Background')->required(),
                ColorPicker::make('aviso_sport_text_color')->label('Sport Warning Text Color')->required(),
                ColorPicker::make('titulo_principal_termos')->label('Main Terms Title Color')->required(),
                ColorPicker::make('titulo_segundario_termos')->label('Secondary Terms Title Color')->required(),
                ColorPicker::make('descriçao_segundario_termos')->label('Secondary Terms Description Color')->required(),
            ])->columns(2);
    }

    // MY ACCOUNT MODAL | MY ACCOUNT PAGE
    protected function css_do_myconta(): Section
    {
        return Section::make("My Account Modal")
            ->description('You can change My Account page colors')
            ->label('My Account')
            ->schema([
                ColorPicker::make('myconta_background')->label('My Account Background')->required(),
                ColorPicker::make('myconta_sub_background')->label('Secondary My Account Background')->required(),
                ColorPicker::make('myconta_text_color')->label('My Account Text Color')->required(),
                ColorPicker::make('myconta_button_background')->label('My Account Button Background')->required(),
                ColorPicker::make('myconta_button_icon_color')->label('My Account Button Icon Color')->required(),
            ])->columns(2);
    }

    // SUPPORT CENTER | SUPPORT PAGE
    protected function central_suporte(): Section
    {
        return Section::make('Support Center')
            ->description('You can change Support Center colors')
            ->schema([
                ColorPicker::make('central_suporte_background')->label('Support Background Color')->required(),
                ColorPicker::make('central_suporte_sub_background')->label('Secondary Support Background')->required(),
                ColorPicker::make('central_suporte_button_background_ao_vivo')->label('Live Chat Button Background')->required(),
                ColorPicker::make('central_suporte_button_texto_ao_vivo')->label('Live Chat Button Text Color')->required(),
                ColorPicker::make('central_suporte_button_icon_ao_vivo')->label('Live Chat Button Icon Color')->required(),
                ColorPicker::make('central_suporte_button_background_denuncia')->label('Report Button Background')->required(),
                ColorPicker::make('central_suporte_button_texto_denuncia')->label('Report Button Text Color')->required(),
                ColorPicker::make('central_suporte_button_icon_denuncia')->label('Report Button Icon Color')->required(),
                ColorPicker::make('central_suporte_title_text_color')->label('Title Text Color')->required(),
                ColorPicker::make('central_suporte_icon_title_text_color')->label('Title Icon Color')->required(),
                ColorPicker::make('central_suporte_info_text_color')->label('Info Text Color')->required(),
                ColorPicker::make('central_suporte_info_icon_color')->label('Info Icon Color')->required(),
                ColorPicker::make('central_suporte_aviso_title_color')->label('Warning Title Color')->required(),
                ColorPicker::make('central_suporte_aviso_text_color')->label('Warning Text Color')->required(),
                ColorPicker::make('central_suporte_aviso_text_negrito_color')->label('Warning Bold Text Color')->required(),
                ColorPicker::make('central_suporte_aviso_icon_color')->label('Warning Icon Color')->required(),
            ])->columns(2);
    }

    // LOGIN, REGISTER, AND FORGOT PASSWORD PAGE
    protected function css_do_login_registro_esquci(): Section
    {
        return Section::make("Login, Register, and Forgot Password Page")
            ->description('You can change Login/Register/Forgot Password page colors')
            ->label('Login, Register, and Forgot Password')
            ->schema([
                ColorPicker::make('register_background')->label('Register Background Color')->required(),
                ColorPicker::make('register_text_color')->label('Register Text Color')->required(),
                ColorPicker::make('register_links_color')->label('Register Link Color')->required(),
                ColorPicker::make('register_input_background')->label('Register Input Background')->required(),
                ColorPicker::make('register_input_text_color')->label('Register Input Text Color')->required(),
                ColorPicker::make('register_input_border_color')->label('Register Input Border Color')->required(),
                ColorPicker::make('register_button_text_color')->label('Register Button Text Color')->required(),
                ColorPicker::make('register_button_background')->label('Register Button Background')->required(),
                ColorPicker::make('register_button_border_color')->label('Register Button Border Color')->required(),
                ColorPicker::make('register_security_color')->label('Register Security Text Color')->required(),
                ColorPicker::make('register_security_background')->label('Register Security Background')->required(),
                ColorPicker::make('register_security_border_color')->label('Register Security Border Color')->required(),
                ColorPicker::make('geral_icons_color')->label('General Icon Color')->required(),
                ColorPicker::make('login_background')->label('Login Background Color')->required(),
                ColorPicker::make('login_text_color')->label('Login Text Color')->required(),
                ColorPicker::make('login_links_color')->label('Login Link Color')->required(),
                ColorPicker::make('login_input_background')->label('Login Input Background')->required(),
                ColorPicker::make('login_input_text_color')->label('Login Input Text Color')->required(),
                ColorPicker::make('login_input_border_color')->label('Login Input Border Color')->required(),
                ColorPicker::make('login_button_text_color')->label('Login Button Text Color')->required(),
                ColorPicker::make('login_button_background')->label('Login Button Background')->required(),
                ColorPicker::make('login_button_border_color')->label('Login Button Border Color')->required(),
                ColorPicker::make('esqueci_background')->label('Forgot Password Background')->required(),
                ColorPicker::make('esqueci_text_color')->label('Forgot Password Text Color')->required(),
                ColorPicker::make('esqueci_input_background')->label('Forgot Password Input Background')->required(),
                ColorPicker::make('esqueci_input_text_color')->label('Forgot Password Input Text Color')->required(),
                ColorPicker::make('esqueci_input_border_color')->label('Forgot Password Input Border Color')->required(),
                ColorPicker::make('esqueci_button_text_color')->label('Forgot Password Button Text Color')->required(),
                ColorPicker::make('esqueci_button_background')->label('Forgot Password Button Background')->required(),
                ColorPicker::make('esqueci_button_border_color')->label('Forgot Password Button Border Color')->required(),
            ])->columns(4);
    }

    // GAME LIST PAGE
    protected function css_do_listgames(): Section
    {
        return Section::make("Game List Page")
            ->description('You can change Game List colors')
            ->label('Game List')
            ->schema([
                ColorPicker::make('gamelist_background')->label('Game List Background')->required(),
                ColorPicker::make('gamelist_input_background')->label('Game List Input Background')->required(),
                ColorPicker::make('gamelist_input_text_color')->label('Game List Input Text Color')->required(),
                ColorPicker::make('gamelist_input_border_color')->label('Game List Input Border Color')->required(),
                ColorPicker::make('gamelist_text_color')->label('Game List Text Color')->required(),
                ColorPicker::make('gamelist_button_background')->label('Game List Button Background')->required(),
                ColorPicker::make('gamelist_button_text_color')->label('Game List Button Text Color')->required(),
                ColorPicker::make('gamelist_button_border_color')->label('Game List Button Border Color')->required(),
            ])->columns(4);
    }

    // DAILY BONUS PAGE
    protected function css_do_bonus_diario(): Section
    {
        return Section::make("Daily Bonus Page")
            ->description('You can change Daily Bonus page colors')
            ->label('Daily Bonus')
            ->schema([
                ColorPicker::make('bonus_diario_background')->label('Daily Bonus Background')->required(),
                ColorPicker::make('bonus_diario_sub_background')->label('Daily Bonus Secondary Background')->required(),
                ColorPicker::make('bonus_diario_texto')->label('Daily Bonus Text Color')->required(),
                ColorPicker::make('bonus_diario_texto_icon')->label('Daily Bonus Icon Text Color')->required(),
                ColorPicker::make('bonus_diario_texto_valor_bonus')->label('Bonus Value Text Color')->required(),
                ColorPicker::make('bonus_diario_aviso')->label('Daily Bonus Warning Color')->required(),
                ColorPicker::make('bonus_diario_botao_backgroud')->label('Daily Bonus Button Background')->required(),
                ColorPicker::make('bonus_diario_botao_texto_color')->label('Daily Bonus Button Text Color')->required(),
                ColorPicker::make('bonus_diario_regras_title')->label('Bonus Rules Title Color')->required(),
                ColorPicker::make('bonus_diario_regras_titulo')->label('Bonus Rules Subtitle Color')->required(),
                ColorPicker::make('bonus_diario_bola_interna')->label('Internal Bonus Ball Color')->required(),
                ColorPicker::make('bonus_diario_bola_fora_')->label('External Bonus Ball Color')->required(),
                ColorPicker::make('bonus_diario_bola_carregamento')->label('Loading Bonus Ball Color')->required(),
                ColorPicker::make('bonus_diario_texto_bola')->label('Bonus Ball Text Color')->required(),
            ])->columns(4);
    }

    /////////////////////////////////////////////////////////////////////
    ////////////////////// DESIGN CENTER PT2 ////////////////////////////
    /////////////////////////////////////////////////////////////////////
    // WALLET DASHBOARD
    protected function css_do_WalletDashboard(): Section
    {
        return Section::make('Wallet Dashboard')
            ->description('You can change Wallet Dashboard colors')
            ->label('Wallet Dashboard')
            ->schema([
                ColorPicker::make('carteira_background')->label('Wallet Background Color')->required(),
                ColorPicker::make('carteira_button_saldo_background')->label('Wallet Balance Background')->required(),
                ColorPicker::make('carteira_button_saldo_text_color')->label('Wallet Balance Text Color')->required(),
                ColorPicker::make('carteira_button_saldo_border_color')->label('Wallet Balance Border Color')->required(),
                ColorPicker::make('carteira_button_saque_background')->label('Wallet Withdrawal Button Background')->required(),
                ColorPicker::make('carteira_button_saque_text_color')->label('Wallet Withdrawal Button Text Color')->required(),
                ColorPicker::make('carteira_button_saque_border_color')->label('Wallet Withdrawal Button Border Color')->required(),
                ColorPicker::make('carteira_button_deposito_background')->label('Wallet Deposit Button Background')->required(),
                ColorPicker::make('carteira_button_deposito_text_color')->label('Wallet Deposit Button Text Color')->required(),
                ColorPicker::make('carteira_button_deposito_border_color')->label('Wallet Deposit Button Border Color')->required(),
                ColorPicker::make('carteira_history_barra_background')->label('Wallet History Bar Background')->required(),
                ColorPicker::make('carteira_history_barra_text_color')->label('Wallet History Bar Text Color')->required(),
                ColorPicker::make('carteira_history_icon_color')->label('Wallet History Icon Color')->required(),
                ColorPicker::make('carteira_saldo_pix_button_text_color')->label('Wallet PIX Button Text Color')->required(),
                ColorPicker::make('carteira_saldo_pix_input_background')->label('Wallet PIX Input Background')->required(),
                ColorPicker::make('carteira_saldo_pix_input_text_color')->label('Wallet PIX Input Text Color')->required(),
                ColorPicker::make('carteira_saldo_pix_border_color')->label('Wallet PIX Border Color')->required(),
                ColorPicker::make('carteira_saldo_pix_icon_color')->label('Wallet PIX Icon Color')->required(),
                ColorPicker::make('carteira_saldo_button_saque_text_color')->label('Wallet Withdrawal Button Text Color')->required(),
                ColorPicker::make('carteira_saldo_button_saque_background')->label('Wallet Withdrawal Button Background')->required(),
                ColorPicker::make('carteira_saldo_button_saque_border_color')->label('Wallet Withdrawal Button Border Color')->required(),
                ColorPicker::make('carteira_saque_number_color')->label('Wallet Withdrawal Number Color')->required(),
                ColorPicker::make('carteira_saque_input_background')->label('Wallet Withdrawal Input Background')->required(),
                ColorPicker::make('carteira_saque_input_text_color')->label('Wallet Withdrawal Input Text Color')->required(),
                ColorPicker::make('carteira_saque_input_border_color')->label('Wallet Withdrawal Input Border Color')->required(),
                ColorPicker::make('carteira_saque_button_text_color')->label('Wallet Withdrawal Button Text Color')->required(),
                ColorPicker::make('carteira_saque_button_background')->label('Wallet Withdrawal Button Background')->required(),
                ColorPicker::make('carteira_saque_button_border_color')->label('Wallet Withdrawal Button Border Color')->required(),
                ColorPicker::make('carteira_saldo_button_deposito_text_color')->label('Wallet Deposit Button Text Color')->required(),
                ColorPicker::make('carteira_saldo_button_deposito_background')->label('Wallet Deposit Button Background')->required(),
                ColorPicker::make('carteira_saldo_button_deposito_border_color')->label('Wallet Deposit Button Border Color')->required(),
                ColorPicker::make('carteira_saldo_button_deposito_píx_color_text')->label('Wallet Deposit PIX Button Text Color')->required(),
                ColorPicker::make('carteira_saldo_button_deposito_píx_background')->label('Wallet Deposit PIX Button Background')->required(),
                ColorPicker::make('carteira_saldo_button_deposito_píx_icon')->label('Wallet Deposit PIX Button Icon Color')->required(),
            ])->columns(4);
    }

    // BET HISTORY PAGE
    protected function css_do_BetHistory(): Section
    {
        return Section::make("Bet History Page")
            ->description('You can change Bet History page colors')
            ->label('Bet History')
            ->schema([
                ColorPicker::make('bet_history_background')->label('Bet History Background')->required(),
                ColorPicker::make('bet_history_text_color')->label('Bet History Text Color')->required(),
                ColorPicker::make('bet_history_button_background')->label('Bet History Button Background')->required(),
                ColorPicker::make('bet_history_button_text_color')->label('Bet History Button Text Color')->required(),
                ColorPicker::make('bet_history_button_border_color')->label('Bet History Button Border Color')->required(),
                ColorPicker::make('bet_history_barra_background')->label('Bet History Bar Background')->required(),
                ColorPicker::make('bet_history_barra_text_color')->label('Bet History Bar Text Color')->required(),
                ColorPicker::make('bet_history_barra_icon_color')->label('Bet History Bar Icon Color')->required(),
            ])->columns(4);
    }

    // WALLET WITHDRAWAL PAGE
    protected function css_do_WalletWithdrawal(): Section
    {
        return Section::make('Wallet Withdrawal')
            ->description('You can change Wallet Withdrawal colors')
            ->label('Wallet Withdrawal')
            ->schema([
                ColorPicker::make('carteira_withdrawal_background')->label('Wallet Withdrawal Background')->required(),
                ColorPicker::make('carteira_withdrawal_text_color')->label('Wallet Withdrawal Text Color')->required(),
                ColorPicker::make('carteira_withdrawal_input_background')->label('Wallet Withdrawal Input Background')->required(),
                ColorPicker::make('carteira_withdrawal_input_text_color')->label('Wallet Withdrawal Input Text Color')->required(),
                ColorPicker::make('carteira_withdrawal_input_border_color')->label('Wallet Withdrawal Input Border Color')->required(),
                ColorPicker::make('carteira_withdrawal_button_background')->label('Wallet Withdrawal Button Background')->required(),
                ColorPicker::make('carteira_withdrawal_button_text_color')->label('Wallet Withdrawal Button Text Color')->required(),
                ColorPicker::make('carteira_withdrawal_button_border_color')->label('Wallet Withdrawal Button Border Color')->required(),
                ColorPicker::make('carteira_withdrawal_icon_color')->label('Wallet Withdrawal Icon Color')->required(),
                ColorPicker::make('carteira_withdrawal_icon_background')->label('Wallet Withdrawal Icon Background')->required(),
                ColorPicker::make('carteira_withdrawal_icon_border_color')->label('Wallet Withdrawal Icon Border Color')->required(),
                ColorPicker::make('carteira_withdrawal_aviso_background')->label('Wallet Withdrawal Warning Background')->required(),
                ColorPicker::make('carteira_withdrawal_aviso_text_color')->label('Wallet Withdrawal Warning Text Color')->required(),
                ColorPicker::make('carteira_withdrawal_aviso_icon_color')->label('Wallet Withdrawal Warning Icon Color')->required(),
                ColorPicker::make('carteira_withdrawal_aviso_border_color')->label('Wallet Withdrawal Warning Border Color')->required(),
                ColorPicker::make('carteira_withdrawal_aviso_text_negrito_color')->label('Wallet Withdrawal Warning Bold Text Color')->required(),
            ])->columns(4);
    }

    // WALLET PIX PAGE
    protected function css_do_PixWallet(): Section
    {
        return Section::make('Wallet PIX')
            ->description('You can change Wallet PIX colors')
            ->label('Wallet PIX')
            ->schema([
                ColorPicker::make('carteira_pix_background')->label('Wallet PIX Background')->required(),
                ColorPicker::make('carteira_pix_text_color')->label('Wallet PIX Text Color')->required(),
                ColorPicker::make('carteira_pix_input_background')->label('Wallet PIX Input Background')->required(),
                ColorPicker::make('carteira_pix_input_text_color')->label('Wallet PIX Input Text Color')->required(),
                ColorPicker::make('carteira_pix_input_border_color')->label('Wallet PIX Input Border Color')->required(),
                ColorPicker::make('carteira_pix_button_background')->label('Wallet PIX Button Background')->required(),
                ColorPicker::make('carteira_pix_button_text_color')->label('Wallet PIX Button Text Color')->required(),
                ColorPicker::make('carteira_pix_button_border_color')->label('Wallet PIX Button Border Color')->required(),
                ColorPicker::make('carteira_pix_qr_background')->label('Wallet PIX QR Background')->required(),
                ColorPicker::make('carteira_pix_qr_text_color')->label('Wallet PIX QR Text Color')->required(),
                ColorPicker::make('carteira_pix_qr_icon_color')->label('Wallet PIX QR Icon Color')->required(),
                ColorPicker::make('carteira_pix_qr_border_color')->label('Wallet PIX QR Border Color')->required(),
                ColorPicker::make('carteira_pix_qr_text_negrito_color')->label('Wallet PIX QR Bold Text Color')->required(),
            ])->columns(4);
    }

    // WALLET DEPOSIT PAGE
    protected function css_do_WalletDeposit(): Section
    {
        return Section::make('Wallet Deposit')
            ->description('You can change Wallet Deposit colors')
            ->label('Wallet Deposit')
            ->schema([
                ColorPicker::make('carteira_deposito_background')->label('Wallet Deposit Background')->required(),
                ColorPicker::make('carteira_deposito_contagem_background')->label('Wallet Deposit Counter Background')->required(),
                ColorPicker::make('carteira_deposito_contagem_text_color')->label('Wallet Deposit Counter Text Color')->required(),
                ColorPicker::make('carteira_deposito_contagem_icon_color')->label('Wallet Deposit Counter Icon Color')->required(),
                ColorPicker::make('carteira_deposito_contagem_border_color')->label('Wallet Deposit Counter Border Color')->required(),
                ColorPicker::make('carteira_deposito_button_background')->label('Wallet Deposit Button Background')->required(),
                ColorPicker::make('carteira_deposito_button_text_color')->label('Wallet Deposit Button Text Color')->required(),
                ColorPicker::make('carteira_deposito_button_border_color')->label('Wallet Deposit Button Border Color')->required(),
                ColorPicker::make('carteira_deposito_qr_background')->label('Wallet Deposit QR Background')->required(),
                ColorPicker::make('carteira_deposito_qr_text_color')->label('Wallet Deposit QR Text Color')->required(),
                ColorPicker::make('carteira_deposito_qr_icon_color')->label('Wallet Deposit QR Icon Color')->required(),
                ColorPicker::make('carteira_deposito_qr_border_color')->label('Wallet Deposit QR Border Color')->required(),
                ColorPicker::make('carteira_deposito_qr_text_negrito_color')->label('Wallet Deposit QR Bold Text Color')->required(),
            ])->columns(4);
    }

    // WALLET BALANCE PAGE
    protected function css_do_WalletBalance(): Section
    {
        return Section::make('Wallet Balance')
            ->description('You can change Wallet Balance colors')
            ->label('Wallet Balance')
            ->schema([
                ColorPicker::make('carteira_balance_background')->label('Wallet Balance Background')->required(),
                ColorPicker::make('carteira_balance_text_color')->label('Wallet Balance Text Color')->required(),
                ColorPicker::make('carteira_balance_button_background')->label('Wallet Balance Button Background')->required(),
                ColorPicker::make('carteira_balance_button_text_color')->label('Wallet Balance Button Text Color')->required(),
                ColorPicker::make('carteira_balance_button_border_color')->label('Wallet Balance Button Border Color')->required(),
                ColorPicker::make('carteira_balance_icon_color')->label('Wallet Balance Icon Color')->required(),
                ColorPicker::make('carteira_balance_icon_background')->label('Wallet Balance Icon Background')->required(),
                ColorPicker::make('carteira_balance_icon_border_color')->label('Wallet Balance Icon Border Color')->required(),
                ColorPicker::make('carteira_balance_aviso_background')->label('Wallet Balance Warning Background')->required(),
                ColorPicker::make('carteira_balance_aviso_text_color')->label('Wallet Balance Warning Text Color')->required(),
                ColorPicker::make('carteira_balance_aviso_icon_color')->label('Wallet Balance Warning Icon Color')->required(),
                ColorPicker::make('carteira_balance_aviso_border_color')->label('Wallet Balance Warning Border Color')->required(),
                ColorPicker::make('carteira_balance_aviso_text_negrito_color')->label('Wallet Balance Warning Bold Text Color')->required(),
            ])->columns(4);
    }

    // AFFILIATES PAGE
    protected function css_do_affiliates(): Section
    {
        return Section::make('Affiliates Page')
            ->description('You can change Affiliates page colors')
            ->label('Affiliates')
            ->schema([
                ColorPicker::make('afiliados_background')->label('Affiliates Background')->required(),
                ColorPicker::make('afiliados_text_color')->label('Affiliates Text Color')->required(),
                ColorPicker::make('afiliados_button_background')->label('Affiliates Button Background')->required(),
                ColorPicker::make('afiliados_button_text_color')->label('Affiliates Button Text Color')->required(),
                ColorPicker::make('afiliados_button_border_color')->label('Affiliates Button Border Color')->required(),
                ColorPicker::make('afiliados_icon_color')->label('Affiliates Icon Color')->required(),
                ColorPicker::make('afiliados_icon_background')->label('Affiliates Icon Background')->required(),
                ColorPicker::make('afiliados_icon_border_color')->label('Affiliates Icon Border Color')->required(),
                ColorPicker::make('afiliados_icon_text_color')->label('Affiliates Icon Text Color')->required(),
                ColorPicker::make('afiliados_icon_text_negrito_color')->label('Affiliates Icon Bold Text Color')->required(),
                ColorPicker::make('afiliados_icon_sub_text_color')->label('Affiliates Icon Subtext Color')->required(),
                ColorPicker::make('afiliados_icon_sub_background')->label('Affiliates Icon Subtext Background')->required(),
                ColorPicker::make('afiliados_icon_sub_border_color')->label('Affiliates Icon Subtext Border Color')->required(),
                ColorPicker::make('afiliados_icon_sub_text_negrito_color')->label('Affiliates Icon Subtext Bold Text Color')->required(),
                ColorPicker::make('afiliados_icon_mover_color')->label('Affiliates Icon Move Color')->required(),
                ColorPicker::make('afiliados_icon_mover_background')->label('Affiliates Icon Move Background')->required(),
                ColorPicker::make('afiliates_icon_mover_border_color')->label('Affiliates Icon Move Border Color')->required(),
                ColorPicker::make('afiliados_icon_mover_text_color')->label('Affiliates Icon Move Text Color')->required(),
                ColorPicker::make('afiliados_icon_mover_text_negrito_color')->label('Affiliates Icon Move Bold Text Color')->required(),
            ])->columns(4);
    }

    // GENERAL STYLES
    protected function css_do_geral(): Section
    {
        return Section::make('General Styles')
            ->description('You can change general styles')
            ->label('General')
            ->schema([
                ColorPicker::make('background_geral')->label('General Background Color')->required(),
                ColorPicker::make('background_geral_text_color')->label('General Text Color')->required(),
            ])->columns(2);
    }

    // MENU CELL STYLES
    protected function css_do_menu_cell(): Section
    {
        return Section::make('Menu Cell Styles')
            ->description('You can change menu cell styles')
            ->label('Menu Cell')
            ->schema([
                ColorPicker::make('menu_cell_background')->label('Menu Cell Background Color')->required(),
                ColorPicker::make('menu_cell_text_color')->label('Menu Cell Text Color')->required(),
            ])->columns(2);
    }

    // MISSIONS STYLES
    protected function css_do_missoes(): Section
    {
        return Section::make('Missions Styles')
            ->description('You can change missions styles')
            ->label('Missions')
            ->schema([
                ColorPicker::make('missoes_background')->label('Missions Background Color')->required(),
                ColorPicker::make('missoes_sub_background')->label('Missions Secondary Background')->required(),
                ColorPicker::make('missoes_text_color')->label('Missions Text Color')->required(),
                ColorPicker::make('missoes_title_color')->label('Missions Title Color')->required(),
                ColorPicker::make('missoes_bonus_background')->label('Missions Bonus Background')->required(),
                ColorPicker::make('missoes_bonus_text_color')->label('Missions Bonus Text Color')->required(),
            ])->columns(2);
    }

    // VIP STYLES
    protected function css_do_vips(): Section
    {
        return Section::make('VIP Styles')
            ->description('You can change VIP styles')
            ->label('VIP')
            ->schema([
                ColorPicker::make('vips_background')->label('VIP Background Color')->required(),
                ColorPicker::make('vips_text_color')->label('VIP Text Color')->required(),
                ColorPicker::make('vips_button_background')->label('VIP Button Background')->required(),
                ColorPicker::make('vips_button_text_color')->label('VIP Button Text Color')->required(),
                ColorPicker::make('vips_button_border_color')->label('VIP Button Border Color')->required(),
                ColorPicker::make('vips_icons_text_color')->label('VIP Icons Text Color')->required(),
                ColorPicker::make('vips_icons_background')->label('VIP Icons Background')->required(),
                ColorPicker::make('vips_icons_sub_text_color')->label('VIP Icons Subtext Color')->required(),
                ColorPicker::make('vips_background_profile_vip')->label('VIP Profile Background')->required(),
                ColorPicker::make('vips_icon_mover_color')->label('VIP Icon Move Color')->required(),
                ColorPicker::make('vip_atual_background')->label('Current VIP Background')->required(),
            ])->columns(4);
    }

    // PROMOTIONS STYLES
    protected function css_do_promocoes(): Section
    {
        return Section::make('Promotions Styles')
            ->description('You can change promotions styles')
            ->label('Promotions')
            ->schema([
                ColorPicker::make('promocoes_background')->label('Promotions Background Color')->required(),
                ColorPicker::make('promocoes_text_color')->label('Promotions Text Color')->required(),
                ColorPicker::make('promocoes_button_background')->label('Promotions Button Background')->required(),
                ColorPicker::make('promocoes_button_text_color')->label('Promotions Button Text Color')->required(),
                ColorPicker::make('promocoes_button_border_color')->label('Promotions Button Border Color')->required(),
                ColorPicker::make('promocoes_icon_color')->label('Promotions Icon Color')->required(),
                ColorPicker::make('promocoes_icon_background')->label('Promotions Icon Background')->required(),
                ColorPicker::make('promocoes_icon_border_color')->label('Promotions Icon Border Color')->required(),
                ColorPicker::make('promocoes_icon_text_color')->label('Promotions Icon Text Color')->required(),
                ColorPicker::make('promocoes_icon_text_negrito_color')->label('Promotions Icon Bold Text Color')->required(),
                ColorPicker::make('promocoes_icon_sub_text_color')->label('Promotions Icon Subtext Color')->required(),
                ColorPicker::make('promocoes_icon_sub_background')->label('Promotions Icon Subtext Background')->required(),
                ColorPicker::make('promocoes_icon_sub_border_color')->label('Promotions Icon Subtext Border Color')->required(),
                ColorPicker::make('promocoes_icon_sub_text_negrito_color')->label('Promotions Icon Subtext Bold Text Color')->required(),
                ColorPicker::make('promocoes_icon_mover_color')->label('Promotions Icon Move Color')->required(),
                ColorPicker::make('promocoes_icon_mover_background')->label('Promotions Icon Move Background')->required(),
                ColorPicker::make('promocoes_icon_mover_border_color')->label('Promotions Icon Move Border Color')->required(),
                ColorPicker::make('promocoes_icon_mover_text_color')->label('Promotions Icon Move Text Color')->required(),
                ColorPicker::make('promocoes_icon_mover_text_negrito_color')->label('Promotions Icon Move Bold Text Color')->required(),
            ])->columns(4);
    }

    // PLATFORM TEXTS
    protected function getSectionPlatformTexts(): Section
    {
        return Section::make('Platform Texts')
            ->description('You can change platform texts')
            ->schema([
                TextInput::make('platform_name')->label('Platform Name'),
                TextInput::make('platform_short_name')->label('Platform Short Name'),
                TextInput::make('platform_description')->label('Platform Description'),
                TextInput::make('platform_copyright')->label('Platform Copyright'),
                TextInput::make('platform_author')->label('Platform Author'),
            ])->columns(3);
    }

    // COOKIE POPUP
    protected function css_do_popup_cookies(): Section
    {
        return Section::make('Cookie Popup')
            ->description('You can change cookie popup colors')
            ->schema([
                ColorPicker::make('popup_cookies_background')->label('Cookie Popup Background')->required(),
                ColorPicker::make('popup_cookies_text_color')->label('Cookie Popup Text Color')->required(),
                ColorPicker::make('popup_cookies_button_background')->label('Cookie Popup Button Background')->required(),
                ColorPicker::make('popup_cookies_button_text_color')->label('Cookie Popup Button Text Color')->required(),
                ColorPicker::make('popup_cookies_button_border_color')->label('Cookie Popup Button Border Color')->required(),
                ColorPicker::make('popup_cookies_link_color')->label('Cookie Popup Link Color')->required(),
            ])->columns(3);
    }
}
