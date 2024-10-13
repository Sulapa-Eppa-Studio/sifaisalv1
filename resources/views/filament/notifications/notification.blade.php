@php
    use Filament\Notifications\Livewire\Notifications;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
    use Illuminate\Support\Arr;

    $color = $getColor() ?? '#369663'; // Menggunakan nilai hex dari $getColor()
    $isInline = $isInline();
    $status = $getStatus();
    $title = $getTitle();
    $hasTitle = filled($title);
    $date = $getDate();
    $hasDate = filled($date);
    $body = $getBody();
    $hasBody = filled($body);
@endphp

<x-filament-notifications::notification :notification="$notification" :x-transition:enter-start="
        Arr::toCssClasses([
            'opacity-0',
            ($this instanceof Notifications)
            ? match (static::$alignment) {
                Alignment::Start, Alignment::Left => '-translate-x-12',
                Alignment::End, Alignment::Right => 'translate-x-12',
                Alignment::Center => match (static::$verticalAlignment) {
                    VerticalAlignment::Start => '-translate-y-12',
                    VerticalAlignment::End => 'translate-y-12',
                    default => null,
                },
                default => null,
            }
            : null,
        ])
    " :x-transition:leave-end="
        Arr::toCssClasses([
            'opacity-0',
            'scale-95' => ! $isInline,
        ])
    " @class([
        'w-full overflow-hidden transition duration-300',
        'text-white', // Menambahkan kelas text-white
        ...match ($isInline) {
            true => ['fi-inline'],
            false => [
                'max-w-sm rounded-xl shadow-lg ring-1',
                'ring-gray-950/5 dark:ring-white/10',
                'fi-status-' . $status => $status,
            ],
        },
    ]) style="background-color: {{ $color }};">
    <div @class([
        'flex w-full gap-3 p-4',
        // Tidak perlu mengatur kelas background di sini
    ])>
        @if ($icon = $getIcon())
            <x-filament-notifications::icon :color="$getIconColor()" :icon="$icon" :size="$getIconSize()" />
        @endif

        <div class="mt-0.5 grid flex-1">
            @if ($hasTitle)
                <x-filament-notifications::title @class(['text-white'])>
                    {!! str($title)->sanitizeHtml()->toHtmlString() !!}
                </x-filament-notifications::title>
            @endif

            @if ($hasDate)
                <x-filament-notifications::date @class(['mt-1' => $hasTitle, 'text-white'])>
                    {{ $date }}
                </x-filament-notifications::date>
            @endif

            @if ($hasBody)
                <x-filament-notifications::body @class(['mt-1' => $hasTitle || $hasDate, 'text-white'])>
                    {!! str($body)->sanitizeHtml()->toHtmlString() !!}
                </x-filament-notifications::body>
            @endif

            @if ($actions = $getActions())
                <x-filament-notifications::actions :actions="$actions" @class(['mt-3' => $hasTitle || $hasDate || $hasBody]) />
            @endif
        </div>

        <x-filament-notifications::close-button />
    </div>
</x-filament-notifications::notification>
