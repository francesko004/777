<x-filament::page>
    <div class="flex flex-col items-center justify-center space-y-6">
        <!-- Title -->
        <h1 class="text-xl font-bold text-gray-700">
            Games and Providers Management
        </h1>

        <!-- Control Buttons -->
        <div class="space-y-4">
            <!-- Sync Games and Providers Button -->
            <button
                wire:click="syncGamesAndProviders"
                class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg shadow"
            >
                Import Games and Providers
            </button>

            <!-- Sync Providers Only Button -->
            <button
                wire:click="syncProvidersOnly"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow"
            >
                Import Providers
            </button>

            <!-- Sync Games Only Button -->
            <button
                wire:click="syncGamesOnly"
                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-3 px-6 rounded-lg shadow"
            >
                Import Games
            </button>

            <!-- Download Images Button -->
            <a
                href="https://imagensfivers.com/Dowload/Webp_Playfiver.zip"
                target="_blank"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow"
            >
                Download Images
            </a>

            <!-- Delete All Games and Providers Button -->
            <button
                wire:click="deleteAllData"
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg shadow"
            >
                Delete All Games and Providers
            </button>

            <!-- Download and Extract Images Button -->
            <button
                wire:click="downloadAndExtractZip"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow"
            >
                Download and Extract Images
            </button>
        </div>
    </div>
</x-filament::page>
