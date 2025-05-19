<?php

namespace App\Filament\Resources\DailyBonusConfigResource\Pages;

use App\Filament\Resources\DailyBonusConfigResource;
use App\Models\DailyBonusConfig;
use Filament\Resources\Pages\EditRecord;

class EditDailyBonusConfig extends EditRecord
{
    protected static string $resource = DailyBonusConfigResource::class;

    /**
     * Mounts the page. Loads the single record or creates it if not found.
     */
    public function mount($record = null): void
    {
        // Find the first (and only) record
        $found = DailyBonusConfig::first();

        if (!$found) {
            // If none exists, create one with default values
            $found = DailyBonusConfig::create([
                'bonus_value' => 10.00,
                'cycle_hours' => 24,
            ]);
        }

        // Set $record to the found ID
        $record = $found->id;

        parent::mount($record);
    }

    /**
     * After saving, redirect to the same page
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
