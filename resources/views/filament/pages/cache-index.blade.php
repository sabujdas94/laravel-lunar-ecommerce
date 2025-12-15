<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                Cache Management
            </x-slot>

            <x-slot name="description">
                Use the action buttons above to manage application caches. Each action will execute the corresponding Artisan command.
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-filament::card>
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-lg">
                            <x-filament::icon
                                icon="heroicon-o-trash"
                                class="w-6 h-6 text-red-600 dark:text-red-400"
                            />
                        </div>
                        <div>
                            <h3 class="text-sm font-medium">Clear Cache</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Clears the application cache stored in the configured cache driver.
                            </p>
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                            <x-filament::icon
                                icon="heroicon-o-cog-6-tooth"
                                class="w-6 h-6 text-green-600 dark:text-green-400"
                            />
                        </div>
                        <div>
                            <h3 class="text-sm font-medium">Cache Config</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Caches configuration files for better performance in production.
                            </p>
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                            <x-filament::icon
                                icon="heroicon-o-eye-slash"
                                class="w-6 h-6 text-yellow-600 dark:text-yellow-400"
                            />
                        </div>
                        <div>
                            <h3 class="text-sm font-medium">Clear Views</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Clears all compiled view files to force recompilation.
                            </p>
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                            <x-filament::icon
                                icon="heroicon-o-map"
                                class="w-6 h-6 text-blue-600 dark:text-blue-400"
                            />
                        </div>
                        <div>
                            <h3 class="text-sm font-medium">Clear Routes</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Clears the route cache to reflect routing changes.
                            </p>
                        </div>
                    </div>
                </x-filament::card>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>