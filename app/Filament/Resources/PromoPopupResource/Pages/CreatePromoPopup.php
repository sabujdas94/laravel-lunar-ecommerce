<?php

namespace App\Filament\Resources\PromoPopupResource\Pages;

use App\Filament\Resources\PromoPopupResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePromoPopup extends CreateRecord
{
    protected static string $resource = PromoPopupResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
