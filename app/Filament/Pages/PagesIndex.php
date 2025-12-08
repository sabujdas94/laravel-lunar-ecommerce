<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PagesIndex extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Pages';

    protected static ?string $navigationGroup = 'CMS';

    public function mount(): void
    {
        // Redirect to the PageResource listing
        redirect('/admin/pages');
    }
}
