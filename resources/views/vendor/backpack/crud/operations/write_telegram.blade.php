@extends(backpack_view('layouts.top_left'))

@php
    $breadcrumbs = [
      trans('backpack::crud.admin') => backpack_url('dashboard'),
      $crud->entity_name_plural => url($crud->route),
      trans('backpack::crud.write') => false,
    ];
@endphp

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!} </span>
            <small>{!! $crud->getSubheading() ?? trans('backpack::crud.write_message')  !!} {{ $entry->full_name }}.</small>

            @if ($crud->hasAccess('list'))
                <small><a href="{{ url($crud->route) }}" class="hidden-print font-sm"><i
                            class="fa fa-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }}
                        <span>{{ $crud->entity_name_plural }}</span></a></small>
            @endif
        </h2>
    </section>
@endsection

@section('content')

    <div class="row">
        <div class="{{ $crud->getCreateContentClass() }}">
            @include('crud::inc.grouped_errors')

            <form method="post" action="{{ url($crud->route . '/' . $entry->getKey() . '/write') }}">
            {!! csrf_field() !!}
                @include('crud::form_content', [ 'fields' => $crud->fields(), 'action' => 'create' ])

                @include('crud::inc.form_write_telegram')
            </form>
        </div>
    </div>

@endsection


