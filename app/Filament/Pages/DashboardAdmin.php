<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverview;
use App\Livewire\WalletOverview;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class DashboardAdmin extends \Filament\Pages\Dashboard
{
    use HasFiltersForm, HasFiltersAction;

    /**
     * @return string|\Illuminate\Contracts\Support\Htmlable|null
     */
    public function getSubheading(): string| null|\Illuminate\Contracts\Support\Htmlable
    {
        return "Welcome back, Admin! Your dashboard is ready for you.";
    }

    /**
     * @param Form $form
     * @return Form
     */
    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('ONDA GAMES CREATED THIS PLATFORM FOR YOU')
                    ->description(new HtmlString('
                        <div style="font-weight: 600; display: flex; align-items: center;">
                            LEARN MORE ABOUT US. JOIN OUR IGAMING COMMUNITY. ACCESS NOW! 
                            <a class="dark:text-white" 
                               style="
                                    font-size: 14px;
                                    font-weight: 600;
                                    width: 127px;
                                    display: flex;
                                    background-color: #f800ff;
                                    padding: 10px;
                                    border-radius: 11px;
                                    justify-content: center;
                                    margin-left: 10px;
                               " 
                               href="https://ondagames.com " 
                               target="_blank">
                                OFFICIAL SITE
                            </a>
                            <a class="dark:text-white" 
                               style="
                                    font-size: 14px;
                                    font-weight: 600;
                                    width: 127px;
                                    display: flex;
                                    background-color: #f800ff;
                                    padding: 10px;
                                    border-radius: 11px;
                                    justify-content: center;
                                    margin-left: 10px;
                               " 
                               href="https://t.me/ondagames_oficial " 
                               target="_blank">
                                TELEGRAM GROUP
                            </a>
                        </div>
                    ')),
            ]);
    }

    /**
     * @return array|\Filament\Actions\Action[]|\Filament\Actions\ActionGroup[]
     */
    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->label('Filter')
                ->form([
                    DatePicker::make('startDate')->label('Start Date'),
                    DatePicker::make('endDate')->label('End Date'),
                ]),
        ];
    }

    /**
     * @return string[]
     */
    public function getWidgets(): array
    {
        return [
            WalletOverview::class,
            StatsOverview::class,
        ];
    }
}
