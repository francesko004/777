<?php

namespace App\Filament\Resources\SubAffiliateResource\Pages;

use App\Filament\Resources\SubAffiliateResource;
use App\Models\CasinoGamesSlotgrator;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateSubAffiliate extends CreateRecord
{
    protected static string $resource = SubAffiliateResource::class;

    /**
     * You can manipulate the data before creation
     * 
     * @param array $data
     * @return Model
     */
    protected function handleRecordCreation(array $data): Model
    {
        // Assign the current authenticated user's ID as the affiliate_id
        $data['affiliate_id'] = auth()->user()->id;

        // Create and return the new model record with the manipulated data
        return static::getModel()::create($data);
    }
}
