<!-- html5 time input -->
@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')
@php


@endphp

<div class="row">
    <div class="col-sm-6">
        <input
            type="time"
            name="{{ $field['name'] }}[start]"
            value="{!! old(square_brackets_to_dots($field['name']))['start'] ?? $field['value']['start'] ?? $field['default'] ?? '' !!}"
            @include('crud::fields.inc.attributes')
        >
    </div>
    <div class="col-sm-6">
        <input
            type="time"
            name="{{ $field['name'] }}[end]"
            value="{!! old(square_brackets_to_dots($field['name']))['end'] ?? $field['value']['end'] ?? $field['default'] ?? '' !!}"
            @include('crud::fields.inc.attributes')
        >
    </div>
</div>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')
