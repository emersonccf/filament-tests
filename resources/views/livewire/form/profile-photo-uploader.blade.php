<div
    x-data="profilePhotoUploader()"
    x-on:livewire:load="init()"
    class="space-y-4"
>
    <div x-data="{ show: true }" x-show="show" {{-- x-init="setTimeout(() => show = false, 20000)" --}}> <!-- 10000 = 10 segundos -->
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
                <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
                <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </button>
            </div>
        @endif
    </div>

    <div class="flex items-center space-x-4">
        <div class="relative w-20 h-20">
            <div class="w-full h-full rounded-full overflow-hidden cursor-pointer group"
                 x-on:click="$refs.fileInput.click()"
                 x-bind:title="photoPreview ? 'Clique para alterar a foto' : 'Clique para adicionar uma foto'">
                <img
                    x-bind:src="photoPreview || '{{ auth()->user()->profile_photo_url ?: asset('storage/images/user-avatar.jpg') }}'"
                    alt="{{ auth()->user()->name }}"
                    class="w-full h-full object-cover"
                >
                <div
                    x-show="isUploading"
                    class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 text-white rounded-full"
                >
                    <span x-text="`${Math.round(progress)}%`"></span>
                </div>
                <!-- Tooltip -->
                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-full">
                    <span class="text-white text-xs text-center px-2">Clique para alterar</span>
                </div>
            </div>

            <!-- Ícone de salvar -->
            <div x-show="photoPreview && !isCurrentPhoto"
                 class="absolute -top-2 -right-2 p-1 bg-green-500 text-white rounded-full cursor-pointer shadow-md hover:bg-green-600 transition-colors duration-200"
                 x-on:click.stop="savePhoto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <!-- Ícone de remover -->
            <div x-show="isCurrentPhoto"
                 class="absolute -top-2 -right-2 p-1 bg-red-500 text-white rounded-full cursor-pointer shadow-md hover:bg-red-600 transition-colors duration-200"
                 x-on:click.stop="removePhoto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        </div>
        <input
            type="file"
            x-ref="fileInput"
            x-on:change="handleFileChange($event)"
            class="hidden"
            accept="image/*"
        >
    </div>

    @error('photo')
    <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
    @enderror

    <div x-show="isUploading" class="text-sm text-gray-500 dark:text-gray-400">
        Processando...
    </div>
</div>

<script>
    function profilePhotoUploader() {
        return {
            photoPreview: null,
            isUploading: false,
            progress: 0,
            isCurrentPhoto: true,
            init() {
                this.$wire.on('upload:finished', () => {
                    this.isUploading = false;
                    this.progress = 0;
                });
                this.$wire.on('photo:removed', () => {
                    this.photoPreview = null;
                    this.isCurrentPhoto = false;
                });
                this.$wire.on('profile-photo-updated', () => {
                    this.isCurrentPhoto = true;
                });
            },
            async handleFileChange(event) {
                const file = event.target.files[0];
                if (!file) return;

                this.photoPreview = URL.createObjectURL(file);
                this.isCurrentPhoto = false;
                this.isUploading = true;

                try {
                    const resizedFile = await this.resizeImage(file);
                    await this.$wire.upload('photo', resizedFile, (uploadedFileData) => {
                        this.progress = uploadedFileData.progress;
                    });
                    this.isUploading = false;
                } catch (error) {
                    console.error('Upload failed:', error);
                    this.isUploading = false;
                }
            },
            async savePhoto() {
                await this.$wire.savePhoto();
                this.isCurrentPhoto = true;
            },
            async removePhoto() {
                if (confirm('Tem certeza que deseja remover a foto?')) {
                    await this.$wire.removePhoto();
                }
            },
            async resizeImage(file) {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            const MAX_WIDTH = 1024;
                            const MAX_HEIGHT = 1024;
                            let width = img.width;
                            let height = img.height;

                            if (width > height) {
                                if (width > MAX_WIDTH) {
                                    height *= MAX_WIDTH / width;
                                    width = MAX_WIDTH;
                                }
                            } else {
                                if (height > MAX_HEIGHT) {
                                    width *= MAX_HEIGHT / height;
                                    height = MAX_HEIGHT;
                                }
                            }

                            canvas.width = width;
                            canvas.height = height;
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob((blob) => {
                                resolve(new File([blob], file.name, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                }));
                            }, 'image/jpeg', 0.7); // Adjust quality here (0.7 = 70% quality)
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }
        }
    }
</script>
