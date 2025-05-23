<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '15s';
    protected static bool $isLazy = true;

    /*** @return array|Stat[]
     */
    protected function getStats(): array
    {
        $today = Carbon::today();

        // Calculations of balances for all players
        $saldodosplayers = DB::table('wallets')
            ->join('users', 'users.id', '=', 'wallets.user_id')
            ->sum(DB::raw('wallets.balance + wallets.balance_bonus + wallets.balance_withdrawal'));
    

        // Total deposited and withdrawn today
        $totalDepositedToday = DB::table('deposits')
            ->whereDate('created_at', $today)
            ->where('status', '1')
            ->sum('amount');
        $totalsacadoToday = DB::table('withdrawals')
            ->whereDate('created_at', $today)
            ->where('status', '1')
            ->sum('amount');

        // Affiliate earnings to be paid
        $totalReferRewardsLast7Days = Wallet::where('refer_rewards', '>=', 1)
            ->sum('refer_rewards');

        // Deposit calculations
        $depositCounts = DB::table('deposits')
            ->select('user_id', DB::raw('count(*) as deposit_count'))
            ->where('status', '1')
            ->groupBy('user_id')
            ->get();

        $numberOfUsersWithSingleDeposit = $depositCounts->where('deposit_count', 1)->count();
        $numberOfUsersWithTwoDeposits = $depositCounts->where('deposit_count', 2)->count();
        $numberOfUsersWithThreeDeposits = $depositCounts->where('deposit_count', 3)->count();
        $numberOfUsersWithFourOrMoreDeposits = $depositCounts->where('deposit_count', '>=', 4)->count();

        return [
            Stat::make('TOTAL REGISTRATIONS', User::count())
                ->description('Total registered users')
                ->descriptionIcon('heroicon-o-user')
                ->color('blue')
                ->chart([10, 20, 15, 30, 25, 40, 35])
                ->chartColor('rgba(59, 130, 246, 0.5)'),
            
            Stat::make('TOTAL DEPOSITED TODAY', \Helper::amountFormatDecimal($totalDepositedToday)) 
                ->description('Total deposited today')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('green')
                ->chart([30, 50, 40, 60, 80, 70, 90])
                ->chartColor('rgba(16, 185, 129, 0.5)'),
            
            Stat::make('TOTAL WITHDRAWN TODAY', \Helper::amountFormatDecimal($totalsacadoToday))
                ->description('Total withdrawn today')
                ->descriptionIcon('heroicon-o-arrow-down')
                ->color('red')
                ->chart([40, 30, 50, 60, 70, 90, 100])
                ->chartColor('rgba(239, 68, 68, 0.5)'),

            Stat::make('TOTAL PLAYER BALANCE', \Helper::amountFormatDecimal($saldodosplayers))
                ->description('Balance in player accounts')
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('purple')
                ->chart([15, 30, 25, 40, 35, 50, 45])
                ->chartColor('rgba(139, 92, 246, 0.5)'),

            Stat::make('TOTAL AFFILIATE BALANCE', \Helper::amountFormatDecimal($totalReferRewardsLast7Days))
                ->description('Accumulated affiliate earnings')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('yellow')
                ->chart([5, 15, 10, 20, 25, 30, 35])
                ->chartColor('rgba(250, 204, 21, 0.5)'),
            
            Stat::make('USERS WITH 1 DEPOSIT', $numberOfUsersWithSingleDeposit) // 
                ->description('Single deposits')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('orange')
                ->chart([10, 20, 15, 30, 25, 35, 45])
                ->chartColor('rgba(249, 115, 22, 0.5)'),
            
            Stat::make('USERS WITH 2 DEPOSITS', $numberOfUsersWithTwoDeposits)
                ->description('Double deposits')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('pink')
                ->chart([20, 30, 25, 35, 45, 50, 55])
                ->chartColor('rgba(236, 72, 153, 0.5)'),
            
            Stat::make('USERS WITH 3 DEPOSITS', $numberOfUsersWithThreeDeposits)
                ->description('Triple deposits')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('indigo')
                ->chart([15, 25, 20, 30, 35, 40, 50])
                ->chartColor('rgba(79, 70, 229, 0.5)'),

            Stat::make('USERS WITH 4+ DEPOSITS', $numberOfUsersWithFourOrMoreDeposits)
                ->description('Frequent deposits')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('teal')
                ->chart([25, 35, 30, 40, 45, 55, 60])
                ->chartColor('rgba(20, 184, 166, 0.5)'),
        ];
    }

    /**
     * @return bool
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
