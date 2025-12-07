<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PagesCreate extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-plus';

    protected static ?string $navigationLabel = 'Pages - Create';

    public static function getNavigationGroup(): ?string
    {
        return 'Pages';
    }

    public function mount(): void
    {
        // Redirect to the products create page. Adjust the path if your resource slug differs.
        redirect('/admin/resources/products/create');
    }
}
