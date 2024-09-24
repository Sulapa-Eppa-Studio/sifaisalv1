<x-filament-widgets::widget>
    <x-filament::section>
        <x-filament::card>
            @php
                $avatarUrl = auth()->user()->profile_photo_url ?: asset('images/default_avatar.png');
            @endphp
            <div class="flex items-center space-x-4">
                <img src="{{ $avatarUrl }}" alt="{{ auth()->user()->name }}" class="w-16 h-16 rounded-full">
                <div>
                    <h2 class="text-xl font-bold">{{ $this->getHeading() }}</h2>
                    <p class="text-xl text-gray-600">{{ $this->getSubheading() }}</p>
                </div>
            </div>
        </x-filament::card>
    </x-filament::section>
</x-filament-widgets::widget>
