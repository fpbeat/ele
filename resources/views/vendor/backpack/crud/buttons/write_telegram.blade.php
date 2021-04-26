@if ($entry->can_write_telegram)
    <a href="{{ url($crud->route.'/'.$entry->getKey().'/write') }}" class="btn btn-sm btn-link btn-gr"><i class="la la-telegram"></i> Написать</a>
@else
    <button class="btn btn-sm btn-link btn-gr" disabled><i class="la la-telegram"></i> Написать</button>
@endif
