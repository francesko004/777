<?php

namespace App\Filament\Pages;

use App\Livewire\LatestPixPayments;
use App\Models\SuitPayPayment;
use App\Models\User;
use App\Traits\Gateways\SuitpayTrait;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class SuitPayPaymentPage extends Page
{
    use SuitpayTrait;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.pages.suit-pay-payment-page';

    protected static ?string $navigationLabel = 'SuitPay Payments';
    protected static ?string $modelLabel = 'SuitPay Payments';
    protected static ?string $title = 'SuitPay Payments';
    protected static ?string $slug = 'suitpay-payments';

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
    public SuitPayPayment $suitPayPayment;

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Payment Details')
                    ->schema([
                        Select::make('user_id')
                            ->label('Users')
                            ->placeholder('Select a user')
                            ->relationship(name: 'user', titleAttribute: 'name')
                            ->options(fn($get) => User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        TextInput::make('pix_key')
                            ->label('Pix Key')
                            ->placeholder('Enter the Pix key')
                            ->required(),
                        Select::make('pix_type')
                            ->label('Key Type')
                            ->placeholder('Select key type')
                            ->options([
                                'document' => 'Document',
                                'phoneNumber' => 'Phone Number',
                                'randomKey' => 'Random Key',
                                'paymentCode' => 'Payment Code',
                            ]),
                        TextInput::make('amount')
                            ->label('Amount')
                            ->placeholder('Enter an amount')
                            ->required()
                            ->numeric(),
                        Textarea::make('observation')
                            ->label('Observation')
                            ->placeholder('Leave an observation if needed')
                            ->rows(5)
                            ->cols(10)
                            ->columnSpanFull()
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    /**
     * @return void
     */
    public function submit(): void
    {
        if(env('APP_DEMO')) {
            Notification::make()
                ->title('Warning')
                ->body('You cannot make changes in demo mode')
                ->danger()
                ->send();
            return;
        }

        $suitpayment = SuitPayPayment::create([
            'user_id'       => $this->data['user_id'],
            'pix_key'       => $this->data['pix_key'],
            'pix_type'      => $this->data['pix_type'],
            'amount'        => $this->data['amount'],
            'observation'   => $this->data['observation'],
        ]);

        if($suitpayment) {
            $resp = self::pixCashOut([
                'pix_key' => $this->data['pix_key'],
                'pix_type' => $this->data['pix_type'],
                'amount' => $this->data['amount'],
                'suitpayment_id' => $suitpayment->id
            ]);

            if($resp) {
                Notification::make()
                    ->title('Withdrawal Requested')
                    ->body('Withdrawal requested successfully')
                    ->success()
                    ->send();
            }else{
                Notification::make()
                    ->title('Withdrawal Error')
                    ->body('Error requesting withdrawal')
                    ->danger()
                    ->send();
            }
        }else{
            Notification::make()
                ->title('Save Error')
                ->body('Error saving withdrawal request')
                ->danger()
                ->send();
        }
    }

    /**
     * @return array|\Filament\Widgets\WidgetConfiguration[]|string[]
     */
    public function getFooterWidgets(): array
    {
        return [
            LatestPixPayments::class
        ];
    }
}
