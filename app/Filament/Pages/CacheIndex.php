<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Filament\Actions\Action;

class CacheIndex extends Page
{
    protected static string $view = 'filament.pages.cache-index';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Cache';

    protected static ?string $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 999;

    public function getHeading(): string
    {
        return 'Cache';
    }

    public function getBreadcrumbs(): array
    {
        return [
            '/admin' => 'CMS',
            '' => 'Cache',
        ];
    }

    

    protected function notify(string $type, string $message): void
    {
        \Filament\Notifications\Notification::make()
            ->title($message)
            ->{$type}()
            ->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
