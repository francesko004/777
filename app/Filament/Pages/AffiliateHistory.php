<?php

namespace App\Filament\Pages;

use App\Models\AffiliateHistory as ModelsAffiliateHistory;
use App\Models\AffiliateLogs;
use App\Models\GamesKey;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DetailsAction; // Typo fixed: "DetailsActin" → "DetailsAction"
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;

class AffiliateHistory extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.affiliate';

    protected static ?string $title = 'Affiliates'; // Translated from 'Afiliados'
    protected static ?string $model = User::class;

    protected static ?string $slug = 'affiliate'; // Translated from 'afiliado'

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

    public ?array $data = [];

    public function table(Table $table): Table
    {  
        return $table
            ->query(self::$model::query())
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make("id")->label("ID"),
                TextColumn::make("name")->label("Name")->searchable(), // Translated from "Nome"
                TextColumn::make("email")->label("Email")->searchable(),
                TextColumn::make("wallet.refer_rewards")->label("Balance")->money("BRL")->sortable(), // Translated from "Saldo"
                
                TextColumn::make("affiliateHistory.logs")->label("RevShare")->formatStateUsing(function($record) {
                    $count = 0;
                    $affiliates = ModelsAffiliateHistory::where("inviter", $record->id)->where("status", 1)->get();
                    foreach ($affiliates as $item) {
                        if ($item->commission_type == "revshare") {
                            $count += $item->commission;
                        }
                    }
                    return "R$". number_format($count, 2, ".", ",");
                })->default(0)->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query->leftJoin('affiliate_histories', 'affiliate_histories.user_id', '=', 'users.id')
                        ->orderByRaw("FIELD(affiliate_histories.commission_type, 'revshare') " . $direction)
                        ->select('affiliate_histories.commission_type', 'affiliate_histories.commission', 'users.*');
                }),

                TextColumn::make("affiliateHistory.commission")->label("CPA")->formatStateUsing(function($record) {
                    $count = 0;
                    $affiliates = ModelsAffiliateHistory::where("inviter", $record->id)->where("status", 1)->get();
                    foreach ($affiliates as $item) {
                        if ($item->commission_type == "cpa") {
                            $count += $item->commission;
                        }
                    }
                    return "R$". number_format($count, 2, ".", ",");
                })->default(0)->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query->leftJoin('affiliate_histories', 'affiliate_histories.user_id', '=', 'users.id')
                        ->orderByRaw("FIELD(affiliate_histories.commission_type, 'cpa') " . $direction)
                        ->select('affiliate_histories.commission_type', 'affiliate_histories.commission', 'users.*');
                }),

                TextColumn::make("inviter")->label("Affiliates")->formatStateUsing(function ($record) { // Translated from "Afiliados"
                    return User::where("inviter", $record->id)->count();
                })->default(0),

                TextColumn::make("created_at")->label("Created At")->dateTime()->sortable() // Translated from "Criado em"
            ])
            ->actions([
                DetailsAction::make(), // Fixed typo
            ]);
    }
}
