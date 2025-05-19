<?php

namespace App\Filament\Pages;

use App\Models\AffiliateHistory as ModelsAffiliateHistory;
use App\Models\AffiliateLogs;
use App\Models\GamesKey;
use Carbon\Carbon;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Contracts\HasTable;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

class DetailsAffiliate extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.affiliateDetails';

    protected static ?string $title = 'Affiliate History'; // Translated from 'Histórico do afiliado'
    protected static ?string $model = AffiliateLogs::class;

    protected static ?string $slug = 'affiliate/details/{provider}'; // Translated from 'afiliado/details/{provider}'
    protected ?string $id = null;
  
    public ?array $data = [
        "id" => null
    ];

    /**
     * @dev  
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin'); // Controls full access to the page
    }
    
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin'); // Controls visibility of specific elements
    }
 
    public function mount($provider){
        if($this->data['id'] == null){
            $this->data['id'] = $provider;
        }
    }

    public function table(Table $table): Table
    {  
        return $table
            ->query(self::$model::query())
            ->columns([
                TextColumn::make("commission_type")
                    ->label("Commission Type") // Translated from "Tipo de Comissão"
                    ->formatStateUsing(function($record) {
                        if ($record->commission_type == "revshare") {
                            return "RevShare";
                        } else {
                            return "CPA";
                        }  
                    })
                    ->default("Undefined"), // Translated from "Indefinido"

                TextColumn::make("commission")
                    ->label("Commission Value") // Translated from "Valor da comissão"
                    ->formatStateUsing(function($record) {
                        $count = number_format($record->commission, 2, ",", ",");
                        return "R$". $count;
                    })
                    ->default(0),

                TextColumn::make("type")
                    ->label("Type")
                    ->formatStateUsing(function($record){
                        if($record->type == "decrement"){
                            return "Earnings"; // Translated from "Ganho"
                        } else {
                            return "Loss"; // Translated from "Perca"
                        }
                    }),

                TextColumn::make("created_at")->label("Date")->dateTime() // Translated from "Data"
            ])
            ->actions([])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label("Created From"), // Translated from "Criado a partir de"
                        DatePicker::make('created_until')->label("Created Until"), // Translated from "Criado até"
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->where("user_id", $this->data['id'])
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Created From ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Created Until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    })
            ]);
    }
}
