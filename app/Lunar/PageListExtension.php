<?php
namespace App\Lunar;

use Filament\Actions;
use Filament\Actions\LinkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Lunar\Admin\Support\Extending\ListPageExtension;
use Lunar\Admin\Filament\Widgets;

class PageListExtension extends ListPageExtension
{
    public function heading($title): string
    {
        return $title . ' - Example';
    }

    public function subheading($title): string
    {
        return $title . ' - Example';
    }

    public function headerWidgets(array $widgets): array
    {
        $widgets = [
            ...$widgets,
            Widgets\Dashboard\Orders\OrderStatsOverview::make(),
        ];

        return $widgets;
    }

    public function headerActions(array $actions): array
    {
        $createUrl = url(trim(request()->getRequestUri(), '/') . '/create');

        $actions = [
            ...$actions,
            Actions\Action::make('Create')
                ->label('Create')
                ->action(fn () => redirect($createUrl)),
            Actions\ActionGroup::make([
                Actions\Action::make('View on Storefront'),
                Actions\Action::make('Copy Link'),
                Actions\Action::make('Duplicate'),
            ]),
        ];

        return $actions;
    }
    
    public function paginateTableQuery(Builder $query, int $perPage = 25): Paginator
    {
        return $query->paginate($perPage);
    }

    public function footerWidgets(array $widgets): array
    {
        $widgets = [
            ...$widgets,
            Widgets\Dashboard\Orders\LatestOrdersTable::make(),
        ];

        return $widgets;
    }
}