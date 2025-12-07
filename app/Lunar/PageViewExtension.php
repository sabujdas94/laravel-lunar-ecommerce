<?php
namespace App\Lunar;

use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Filament\Widgets;
use Lunar\Admin\Support\Extending\ViewPageExtension;

class PageViewExtension extends ViewPageExtension
{
    public function headerWidgets(array $widgets): array
    {
        $widgets = [
            ...$widgets,
            Widgets\Dashboard\Orders\OrderStatsOverview::make(),
        ];

        return $widgets;
    }

    public function heading($title, Model $model): string
    {
        return $title . ' - Example';
    }

    public function subheading($title, Model $model): string
    {
        return $title . ' - Example';
    }
    
    public function headerActions(array $actions): array
    {
        $actions = [
            ...$actions,
            Actions\ActionGroup::make([
                Actions\Action::make('Download PDF')
            ])
        ];

        return $actions;
    }

    public function extendsInfolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            ...$infolist->getComponents(true),
            TextEntry::make('custom_title'),
        ]);
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