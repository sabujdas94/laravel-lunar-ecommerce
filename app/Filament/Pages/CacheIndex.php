<?php

namespace App\Filament\Pages;

use App\Services\DataVersion;
use Dflydev\DotAccessData\Data;
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

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function clearHomeCacheAction(): Action
    {
        return Action::make('clearHomeCache')
            ->label('Clear Now')
            ->color('gray')
            ->size('sm')
            ->requiresConfirmation()
            ->modalHeading('Clear Home Page Cache')
            ->modalDescription('Are you sure you want to clear the home page cache? This action cannot be undone.')
            ->modalSubmitActionLabel('Yes, clear it')
            ->action(function () {
                $this->clearHomeCache();
            });
    }

    public function clearHomeCache(): void
    {
        // TODO: Write your custom cache clearing logic here
        // Example:
        // \Illuminate\Support\Facades\Cache::forget('home_page_data');
        // \Illuminate\Support\Facades\Cache::forget('another_cache_key');
        // Or call a service/repository method:
        // app(\App\Services\CacheService::class)->clearHomePageCache();

        DataVersion::bump('home_page');
        
        $this->notify('success', 'Home page cache cleared successfully!');
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
