@extends('layouts/edit-form', [
    'createText' => trans('admin/buildings/general.create') ,
    'updateText' => trans('admin/buildings/general.update'),
    'helpPosition'  => 'right',
    // 'helpText' => trans('help.buildings'),
    'formAction' => (isset($item->id)) ? route('buildings.update', ['buildings' => $item->id]) : route('buildings.store'),
])
{{-- Page content --}}
@section('inputFields')

@include ('partials.forms.edit.company-select', ['translated_name' => trans('general.company'), 'fieldname' => 'company_id'])
@include ('partials.forms.edit.name', ['translated_name' => trans('admin/buildings/table.title')])
@include ('partials.forms.edit.location-select', ['translated_name' => trans('general.location'), 'fieldname' => 'location_id'])
@include ('partials.forms.edit.area', ['translated_name' => trans('general.area')])
@include ('partials.forms.edit.purchase_cost')

<!-- Image -->
{{-- @if ($item->image)
    <div class="form-group {{ $errors->has('image_delete') ? 'has-error' : '' }}">
        <label class="col-md-3 control-label" for="image_delete">{{ trans('general.image_delete') }}</label>
        <div class="col-md-5">
            {{ Form::checkbox('image_delete') }}
            <img src="{{ Storage::disk('public')->url(app('buildings_upload_path').e($item->image)) }}"  class="img-responsive" />
            {!! $errors->first('image_delete', '<span class="alert-msg">:message</span>') !!}
        </div>
    </div>
@endif

@include ('partials.forms.edit.image-upload') --}}
@stop
