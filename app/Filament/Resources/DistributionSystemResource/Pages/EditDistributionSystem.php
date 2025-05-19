<?php

namespace App\Filament\Resources\DistributionSystemResource\Pages;

use App\Filament\Resources\DistributionSystemResource;
use App\Models\DistributionSystem;
use Filament\Resources\Pages\EditRecord;

class EditDistributionSystem extends EditRecord
{
    protected static string $resource = DistributionSystemResource::class;

    /**
     * When mounting the page, we fetch or create the single record.
     */
    public function mount($record = null): void
    {
        // If a record already exists, get the first one
        $found = DistributionSystem::first();

        // If no record exists, create one with default values
        if (! $found) {
            $found = DistributionSystem::create([
                'meta_arrecadacao' => 0,
                'percentual_distribuicao' => 0,
                'rtp_arrecadacao' => 0,
                'rtp_distribuicao' => 0,
                'total_arrecadado' => 0,
                'total_distribuido' => 0,
                'modo' => 'arrecadacao',
                'ativo' => false,
            ]);
        }

        // Set $record to the ID of the found/created record
        $record = $found->id;

        // Call the original EditRecord's mount method
        parent::mount($record);
    }

    /**
     * Redirect back to the index route after saving
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
