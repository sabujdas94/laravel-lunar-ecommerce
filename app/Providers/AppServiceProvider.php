<?php

namespace App\Providers;

use App\Lunar\CreatePageExtension;
use App\Lunar\EditPageExtension;
use App\Lunar\PageListExtension;
use App\Lunar\PageViewExtension;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Facades\Payments;
use App\PaymentTypes\CODPayment;
use Lunar\Models\Order;
use App\Observers\OrderObserver;
use Lunar\Shipping\ShippingPlugin;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        LunarPanel::panel(function ($panel) {
            return $panel->path('admin')
                ->plugin(new ShippingPlugin())
                ->pages([
                    \App\Filament\Pages\PagesIndex::class,
                ])
                ->resources([
                    \App\Filament\Resources\PageResource::class,
                ]);
        })->register();
                
        \Lunar\Facades\Telemetry::optOut();

        // Register custom Cart model
        $this->app->bind(\Lunar\Models\Cart::class, function () {
            return new \App\Models\Cart();
        });

        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Payments::extend('cash-on-delivery', function ($app) {
            return $app->make(CODPayment::class);
        });

        // LunarPanel::extensions([
        //     \Lunar\Admin\Filament\Resources\CustomerGroupResource\Pages\CreateCustomerGroup::class => CreatePageExtension::class,
        //     \Lunar\Admin\Filament\Resources\ProductResource\Pages\EditProduct::class => EditPageExtension::class,
        //     \Lunar\Admin\Filament\Resources\ProductResource\Pages\ListProducts::class => PageListExtension::class,
        //     \Lunar\Admin\Filament\Resources\OrderResource\Pages\ManageOrder::class => PageViewExtension::class,
        // ]);

        // Register Order observer for COD payment status updates
        Order::observe(OrderObserver::class);
    }
}
