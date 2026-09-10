<x-filament-panels::page>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Kamera Scanner -->
        <div class="bg-white p-4 rounded-xl shadow dark:bg-gray-800">
            <h2 class="text-lg font-bold mb-4">Kamera Scanner</h2>
            <div id="reader" width="100%"></div>
        </div>

        <!-- Form Input -->
        <div class="bg-white p-4 rounded-xl shadow dark:bg-gray-800">
            <form wire:submit="submit">
                {{ $this->form }}

                <div class="mt-4">
                    <x-filament::button type="submit">
                        Simpan Mutasi
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', function () {
            function onScanSuccess(decodedText, decodedResult) {
                // Set the value of the sku input and trigger Livewire update
                @this.set('data.sku', decodedText);
            }

            function onScanFailure(error) {
                // handle scan failure, usually better to ignore and keep scanning
            }

            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: {width: 250, height: 250} },
                /* verbose= */ false);
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        });
    </script>
</x-filament-panels::page>
