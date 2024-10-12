<div class="p-6">
    @if ($record->files->isEmpty())
        <div class="flex items-center justify-center h-48">
            <p class="text-gray-500 text-lg">Tidak ada file yang diunggah untuk record ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($record->files as $file)
                @php
                    $fileTypeEnum = \App\Enums\FileType::tryFrom($file->file_type);
                @endphp
                <div
                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-lg transition-shadow flex flex-col p-6">
                    <div class="p-5 flex flex-col h-full">
                        <!-- Header dengan Ikon dan Judul File -->
                        <div class="flex items-center mb-4">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                                {{ $fileTypeEnum?->label() ?? ucwords(str_replace('_', ' ', $file->file_type)) }}
                            </h4>
                        </div>

                        <!-- Deskripsi File -->
                        <p class="text-gray-600 flex-grow dark:text-white">
                            {{ $fileTypeEnum?->description() ?? 'Deskripsi tidak tersedia.' }}
                        </p>

                        <!-- Tombol Aksi -->
                        <div class="mt-4">
                            <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <x-filament::icon name="heroicon-o-download" class="h-5 w-5 mr-2" />
                                Lihat / Unduh
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
