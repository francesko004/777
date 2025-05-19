<?php

namespace App\Filament\Resources\GameResource\Pages;

use App\Filament\Resources\GameResource;
use App\Traits\Providers\PlayFiverTrait;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListGames extends ListRecords
{
    use PlayFiverTrait;

    /**
     * @var string
     */
    protected static string $resource = GameResource::class;

    /**
     * @return array|Action[]|\Filament\Actions\ActionGroup[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('New Game'),
        ];
    }

    /**
     * Load all providers
     * @dev  
     * @return void
     */
    protected static function LoadingProviderGames2Api()
    {
        dd("dsfsdsdf");
        self::GetAllProvidersGames2Api();
    }

    /**
     * Load all games
     * @dev  
     * @return void
     */
    protected static function LoadingGames2Api()
    {
        self::GetAllGamesGames2Api();
    }

    /**
     * Load games from provider
     */
    protected static function LoadingGames()
    {
        self::LoadingGamesWorldSlot();
    }
}
