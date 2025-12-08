<?php

namespace App\Filament\Resources\PromoPopupResource\Pages;

use App\Filament\Resources\PromoPopupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPromoPopup extends EditRecord
{
    protected static string $resource = PromoPopupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
