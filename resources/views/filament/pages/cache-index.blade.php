<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                Cache Management
            </x-slot>

            <x-slot name="description">
                Click the action buttons to manage application caches. Each action will execute the corresponding operation.
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-filament::card>
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                            <x-filament::icon
                                icon="heroicon-o-home"
                                class="w-6 h-6 text-purple-600 dark:text-purple-400"
                            />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium">Clear Home Cache</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Clears the home page data cache.
                            </p>
                            <div class="mt-3">
                                <x-filament::button
                                    wire:click="mountAction('clearHomeCache')"
                                    color="gray"
                                    size="sm"
                                >
                                    Clear Now
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                </x-filament::card>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>