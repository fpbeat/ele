@extends(backpack_view('layouts.top_left'))

@php
    $breadcrumbs = [
      trans('backpack::crud.admin') => backpack_url('dashboard'),
      trans('backpack::crud.setting') => false,
    ];
@endphp

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!} </span>
        </h2>
    </section>
@endsection

@section('content')

    <div class="row">
        <div class="{{ $crud->getCreateContentClass() }}">
            @include('crud::inc.grouped_errors')

            <form method="post" action="{{ url($crud->route) }}">
            {!! csrf_field() !!}
                @include('crud::form_content', [ 'fields' => $crud->fields(), 'action' => 'create' ])

                @include('crud::inc.form_save_buttons')
            </form>
        </div>
    </div>

@endsection


