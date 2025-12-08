<?php

namespace App\Filament\Resources\PromoPopupResource\Pages;

use App\Filament\Resources\PromoPopupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPromoPopups extends ListRecords
{
    protected static string $resource = PromoPopupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
