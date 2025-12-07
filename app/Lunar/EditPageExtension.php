<?php

namespace App\Lunar;

use Filament\Actions;
use Lunar\Admin\Filament\Widgets;
use Lunar\Admin\Support\Extending\EditPageExtension as ExtendingEditPageExtension;
use Illuminate\Database\Eloquent\Model;

class EditPageExtension extends ExtendingEditPageExtension
{
    public function heading($title, Model $record): string
    {
        return $title . ' - Example';
    }

    public function subheading($title, Model $record): string
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
        $actions = [
            ...$actions,
            Actions\ActionGroup::make([
                Actions\Action::make('View on Storefront'),
                Actions\Action::make('Copy Link'),
                Actions\Action::make('Duplicate'),
            ])
        ];

        return $actions;
    }

    public function formActions(array $actions): array
    {
        $actions = [
            ...$actions,
            Actions\Action::make('Update and Edit'),
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

    public function beforeFill(array $data): array
    {
        $data['model_code'] .= 'ABC';

        return $data;
    }

    public function beforeSave(array $data): array
    {
        return $data;
    }

    public function beforeUpdate(array $data, Model $record): array
    {
        return $data;
    }

    public function afterUpdate(Model $record, array $data): Model
    {
        return $record;
    }
    
    public function relationManagers(array $managers): array
    {
        return $managers;
    }
}