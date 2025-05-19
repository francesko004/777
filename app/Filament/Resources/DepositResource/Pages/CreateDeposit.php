<?php

namespace App\Filament\Resources\DepositResource\Pages;

use App\Filament\Resources\DepositResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateDeposit extends CreateRecord
{
    protected static string $resource = DepositResource::class;

    /**
     * I can manipulate the data before creation
     * @param array $data
     * @return Model
     */
    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create($data);
    }
}
