<?php
namespace App\Lunar;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Filament\Widgets;
use Lunar\Admin\Support\Extending\CreatePageExtension as ExtendingCreatePageExtension;

class CreatePageExtension extends ExtendingCreatePageExtension
{
    public function heading($title): string
    {
        return $title . ' - Example';
    }

    public function subheading($title): string
    {
        return $title . ' - Example';
    }
    
    public function getTabs(array $tabs): array
    {
        return [
            ...$tabs,
            'review' => Tab::make('Review')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'review')),
        ];
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
        $actions = [
            ...$actions,
            Actions\Action::make('Cancel'),
        ];

        return $actions;
    }

    public function formActions(array $actions): array
    {
        $actions = [
            ...$actions,
            Actions\Action::make('Create and Edit'),
        ];

        return $actions;
    }

    public function footerWidgets(array $widgets): array
    {
        $widgets = [
            ...$widgets,
            Widgets\Dashboard\Orders\LatestOrdersTable::make(),
        ];

        return $widgets;
    }

    public function beforeCreate(array $data): array
    {
        $data['model_code'] .= 'ABC';
        
        return $data;
    }

    public function beforeCreation(array $data): array
    {
        return $data;
    }

    public function afterCreation(Model $record, array $data): Model
    {
        return $record;
    }
}