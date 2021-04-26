{{-- closure user column type --}}
@php
    $related_key = $related_key ?? null;

    $column['escaped'] = $column['escaped'] ?? false;
    $column['text'] = $column['function']($entry);
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';
    $column['link'] = $column['link']($crud, $column, $entry, $related_key);

    if(!empty($column['text'])) {
        $column['text'] = $column['prefix'].$column['text'].$column['suffix'];
    }
@endphp

@if ($column['text'])
    <a href="{{ $column['link'] }}" class="btn btn-brand btn-sm btn-table-user btn-github" type="button">
        <i class="la la-user"></i>
        <span>
            @if($column['escaped'])
                {{ $column['text'] }}
            @else
                {!! $column['text'] !!}
            @endif
        </span>
    </a>
@else
    <span>-</span>
@endif


