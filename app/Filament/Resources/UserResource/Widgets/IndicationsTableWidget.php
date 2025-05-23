<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\AffiliateHistory;
use App\Models\GGRGamesFiver;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class IndicationsTableWidget extends BaseWidget
{
    public User $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    protected static ?string $heading = 'CLIENTS BROUGHT BY THIS USER';

    protected static ?int $navigationSort = -1;

    protected int | string | array $columnSpan = 'full';

    /**
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(AffiliateHistory::query()->where('inviter', $this->record->id))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('INVITED USER')
                    ->color('info')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('INVITED USER EMAIL')
                    ->color('info')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('commission_type')
                    ->label('COMMISSION RECEIVED')    
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'cpa' => 'CPA COMMISSION',
                            default => 'UNKNOWN',
                        };
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('commission_paid')
                    ->label('AMOUNT RECEIVED')
                    ->money('BRL')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.status')
                    ->label('STATUS')    
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'active' => 'VALIDATED',
                            default => 'INVALIDATED',
                        };
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('INVITED ON')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Start Date'),
                        DatePicker::make('created_until')->label('End Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
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
                            $indicators['created_from'] = 'Start Date ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'End Date ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ;
    }


    /**
     * @return bool
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
