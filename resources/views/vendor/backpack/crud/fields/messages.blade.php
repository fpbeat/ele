@include('crud::fields.inc.wrapper_start')

@php
    $column['format'] = $column['format'] ?? config('backpack.base.default_datetime_format');
@endphp

<div class="list-group">
    @forelse($field['values'] as $value)
        <div class="list-group-item flex-column align-items-start @if (!$value->is_sent) list-group-item-danger @endif">
            <p class="mb-1">{!! nl2br($value->message) !!}</p>
            <small class="text-muted">{{ \Carbon\Carbon::parse($value->created_at)->locale(App::getLocale())->isoFormat($column['format']) }} @if (!$value->is_sent) ({{ trans('backpack::crud.message_no_sent') }}) @endif</small>
        </div>
    @empty
        <div class="list-group">
            <div class="list-group-item text-center">{{ trans('backpack::crud.telegram_no_messages') }}</div>
        </div>
    @endforelse
</div>

@include('crud::fields.inc.wrapper_end')
