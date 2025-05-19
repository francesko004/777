<?php

namespace App\Filament\Resources\MinesConfigResource\Pages;

use App\Filament\Resources\MinesConfigResource;
use App\Models\GameConfig;
use Filament\Resources\Pages\EditRecord;

class EditMinesConfig extends EditRecord
{
    protected static string $resource = MinesConfigResource::class;

    public function mount($record = null): void
    {
        // Try to load the single configuration record
        $found = GameConfig::first();

        // If none exists, create one with default values
        if (!$found) {
            $found = GameConfig::create([
                'bombs_count'             => 5,          // if this field is required, or remove it if unused
                'min_bet'                 => 1,
                'max_bet'                 => 100,
                'meta_arrecadacao'        => 0,
                'percentual_distribuicao' => 0,
                'modo_atual'              => 'arrecadacao',
                'total_arrecadado'        => 0,
                'total_distribuido'       => 0,
                'minas_distribuicao'      => 5,
                'minas_arrecadacao'       => 5,
                'x_por_mina'              => 0.10,
                'x_a_cada_5'              => 1.50,
                'bet_loss'                => 50,
                'modo_influenciador'      => false,
                'modo_perdedor'           => false,
                'start_cycle_at'          => now(),
            ]);
        }

        // Set the record to be edited
        $record = $found->id;

        parent::mount($record);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
