// ... inside table() method

->columns([
    Tables\Columns\TextColumn::make('gameDetails.game_name')
        ->label('NOME DO JOGO')
        ->color('info')
        ->badge()
        ->searchable(),

    Tables\Columns\TextColumn::make('type')
        ->label('RESULTADO')
        ->badge()
        ->formatStateUsing(fn($state) => match($state) {
            'Perda' => 'APOSTA PERDIDA',
            'Ganho' => 'APOSTA GANHA',
            default => 'DESCONHECIDO',
        })
        ->color(fn($state) => match($state) {
            'Ganho' => 'success',
            'Perda' => 'danger',
            default => 'secondary',
        })
        ->searchable(),

    Tables\Columns\TextColumn::make('type_money')
        ->label('CARTEIRA USADA')
        ->badge()
        ->color('info')
        ->formatStateUsing(fn($state) => match($state) {
            'balance' => 'CARTEIRA DEPÓSITO',
            'balance_bonus' => 'CARTEIRA BÔNUS',
            'balance_withdrawal' => 'CARTEIRA SAQUE',
            default => 'CARTEIRA DESCONHECIDA',
        })
        ->searchable(),

    Tables\Columns\TextColumn::make('amount')
        ->label('VALOR DA APOSTA')
        ->money('BRL')
        ->badge()
        ->color('success')
        ->sortable(),

    Tables\Columns\TextColumn::make('providers')
        ->label('STATUS')
        ->badge()
        ->color(fn($state) => $state === 'Play Fiver' ? 'success' : 'warning')
        ->formatStateUsing(fn($state) => $state === 'Play Fiver' ? 'VALIDADO' : 'PENDENTE')
        ->searchable(),

    Tables\Columns\TextColumn::make('created_at')
        ->label('APOSTADO EM')
        ->dateTime()
        ->sortable(),
])

->filters([
    Filter::make('type_ganho')
        ->label('APOSTAS GANHAS')
        ->query(fn(Builder $query) => $query->where('type', '=', 'Ganho')),

    Filter::make('type_perda')
        ->label('APOSTAS PERDIDAS')
        ->query(fn(Builder $query) => $query->where('type', '=', 'Perda')),

    Filter::make('created_at')
        ->form([
            DatePicker::make('created_from')->label('Data Inicial'),
            DatePicker::make('created_until')->label('Data Final'),
        ])
        ->query(function (Builder $query, array $data): Builder {
            return $query
                ->when($data['created_from'] ?? null, fn(Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                ->when($data['created_until'] ?? null, fn(Builder $query, $date) => $query->whereDate('created_at', '<=', $date));
        })
        ->indicateUsing(function (array $data): array {
            $indicators = [];
            if (!empty($data['created_from'])) {
                $indicators['created_from'] = 'Criação Inicial ' . Carbon::parse($data['created_from'])->toFormattedDateString();
            }
            if (!empty($data['created_until'])) {
                $indicators['created_until'] = 'Criação Final ' . Carbon::parse($data['created_until'])->toFormattedDateString();
            }
            return $indicators;
        }),
]);
