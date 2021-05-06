{{-- catalog image column type --}}
@php
    $value = data_get($entry, $column['name']);

    $column['height'] = $column['height'] ?? "150px";
    $column['width'] = $column['width'] ?? "auto";
    $column['radius'] = $column['radius'] ?? "3px";
    $column['prefix'] = $column['prefix'] ?? '';
@endphp

<div class="row pl-2">
    @forelse($value as $image)
        @php
            $href = $src = Storage::disk($column['disk'])->url($column['prefix'] . $image);

            $column['wrapper']['element'] = $column['wrapper']['element'] ?? 'a';
            $column['wrapper']['href'] = $column['wrapper']['href'] ?? $href;
            $column['wrapper']['target'] = $column['wrapper']['target'] ?? '_blank';
            $column['wrapper']['data-toggle'] = $column['wrapper']['data-toggle'] ?? 'lightbox';
        @endphp

        <div class="col-sm-auto p-1">
            @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
            <img src="{{ $href }}" style="max-height: {{ $column['height'] }};width: {{ $column['width'] }};border-radius: {{ $column['radius'] }};"/>
            @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
        </div>
    @empty
        -
    @endforelse
</div>
