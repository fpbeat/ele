<!-- PAGE OR LINK field -->
<!-- Used in Backpack\MenuCRUD -->

<?php
$field['options'] = [
    'page_link' => trans('backpack::crud.page_link'),
    'external_link' => trans('backpack::crud.external_link'),
];
$field['allows_null'] = false;
$page_data = $field['page_data'];

$entry_link = $field['name']['link'] ?? 'link';
$entry_type = $field['name']['type'] ?? 'type';
$entry_page_id = $field['name']['page_id'] ?? 'page_id';
?>

@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')

<div class="row" data-init-function="bpFieldInitPageOrLinkElement">

    <div class="col-sm-4">
        <select data-identifier="page_or_link_select" name="{!! $entry_type !!}" @include('crud::fields.inc.attributes')>

            @if (isset($field['allows_null']) && $field['allows_null']==true)
                <option value="">-</option>
            @endif

            @if (count($field['options']))
                @foreach ($field['options'] as $key => $value)
                    <option value="{{ $key }}" @if (isset($entry) && $key==$entry->$entry_type) selected @endif>{{ $value }}</option>
                @endforeach
            @endif
        </select>
    </div>
    <div class="col-sm-8">
        <!-- external link input -->
        <div
            class="page_or_link_value page_or_link_external_link <?php if (!isset($entry) || $entry->$entry_type != 'external_link') {
                echo 'd-none';
            } ?>">
            <input
                type="url"
                class="form-control"
                name="{!! $entry_link !!}"
                placeholder="{{ trans('backpack::crud.page_link_placeholder') }}"

                @if (!isset($entry) || $entry->$entry_type !='external_link')
                disabled="disabled"
                @endif

                @if (isset($entry) && $entry->$entry_type =='external_link' && isset($entry->$entry_link) && $entry->$entry_link!='')
                value="{{ $entry->$entry_link }}"
                @endif
            >
        </div>
        <!-- page slug input -->
        <div class="page_or_link_value page_or_link_page <?php if (isset($entry) && $entry->$entry_type != 'page_link') {echo 'd-none';} ?>">

            <select class="form-control" name="{!! $entry_page_id !!}">
                @if (!count($page_data))
                    <option value="">-</option>
                @else
                    @foreach ($page_data as $key => $page)
                        <option value="{{ $key }}"
                                @if (isset($entry) && isset($entry->$entry_page_id) && $key==$entry->$entry_page_id)
                                selected
                            @endif
                        >{{ $page }}</option>
                    @endforeach
                @endif

            </select>
        </div>
    </div>
</div>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif

@include('crud::fields.inc.wrapper_end')


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')

    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <script>
            function bpFieldInitPageOrLinkElement(element) {
                element.find('[data-identifier=page_or_link_select]').change(function (e) {
                    $(this).closest('.row').find(".page_or_link_value input").attr('disabled', 'disabled');
                    $(this).closest('.row').find(".page_or_link_value select").attr('disabled', 'disabled');
                    $(this).closest('.row').find(".page_or_link_value").removeClass("d-none").addClass("d-none");

                    switch ($(this).val()) {
                        case 'external_link':
                            $(this).closest('.row').find(".page_or_link_external_link input").removeAttr('disabled');
                            $(this).closest('.row').find(".page_or_link_external_link").removeClass('d-none');
                            break;

                        default: // page_link
                            $(this).closest('.row').find(".page_or_link_page select").removeAttr('disabled');
                            $(this).closest('.row').find(".page_or_link_page").removeClass('d-none');

                            $(this).closest('.row').find(".page_or_link_page select").change(function () {
                                var text = $(this).closest('.row').find(".page_or_link_page select option:selected").text();
                                var name = ($(this).closest('.form-group').prev('.form-group').find('input'));

                                name.val($.trim(text.replace(/^[-\s]+/g, '')));
                            });
                    }
                });

                element.find('[data-identifier=page_or_link_select]').trigger('change');


            }
        </script>
    @endpush

@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
