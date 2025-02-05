<div x-data="{ photoPreview: null }" class="space-y-4">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    <div class="flex items-center space-x-4">
        <div class="relative">
            <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="w-20 h-20 rounded-full object-cover">
            <div x-show="photoPreview" class="absolute inset-0 w-20 h-20 rounded-full bg-center bg-cover" x-bind:style="'background-image: url(\'' + photoPreview + '\');'"></div>
        </div>
        <div>
            <input type="file" wire:model="photo" class="hidden" x-ref="photo" x-on:change="
                const reader = new FileReader();
                reader.onload = (e) => {
                    photoPreview = e.target.result;
                };
                reader.readAsDataURL($event.target.files[0]);
            ">
            <button type="button"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-400 dark:focus:ring-offset-gray-900 transition-colors duration-200"
                    x-on:click="$refs.photo.click()">
                Escolher Foto
            </button>
        </div>
    </div>

    @error('photo')
    <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
    @enderror

    <div x-show="photoPreview" class="mt-4">
        <button type="button"
                wire:click="save"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-400 dark:focus:ring-offset-gray-900 transition-colors duration-200">
            Salvar Foto
        </button>
    </div>

    <div wire:loading wire:target="photo,save" class="text-sm text-gray-500 dark:text-gray-400">
        Processando...
    </div>
</div>
