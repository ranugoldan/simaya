@extends('layouts/edit-form', [
  'createText'    => trans('admin/procurements/form.create'),
  'updateText'    => trans('admin/procurements/form.update'),
  'topSubmit'     => true,
  'helpText'      => trans('admin/procurements/form.help'),
  'helpPosition'  => 'right',
  'formAction'    => (isset($item->id)) ? route('procurements.update', ['procurement' => $item->id]) : route('procurements.store'),
])

@php
  $x = 1;

  if ($item->models->isNotEmpty()) {
    $x = $item->models->count();
  }
@endphp

@section('inputFields')
  {{-- Procurement Tag --}}
  <div class="form-group {{ $errors->has('procurement_tag') ? 'has-error' : '' }}">
    <label for="procurement_tag" class="col-md-3 control-label">{{ trans('admin/procurements/form.tag') }}</label>
    <div class="col-md-7 col-sm-12{{  (\App\Helpers\Helper::checkIfRequired($item, 'procurement_tag')) ? ' required' : '' }}">
        <input class="form-control" type="text" name="procurement_tag" aria-label="procurement_tag" id="procurement_tag" value="{{ old('procurement_tag', $item->procurement_tag) }}"{!!  (\App\Helpers\Helper::checkIfRequired($item, 'procurement_tag')) ? ' data-validation="required" required' : '' !!} />
        {!! $errors->first('procurement_tag', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
  </div>

  {{-- Models --}}
  <div id="model_id" class="form-group {{ $errors->has('model_id') ? 'has-error' : '' }}">
    {{ Form::label('model_id', trans('admin/procurements/form.model'), array('class' => 'col-md-3 control-label')) }}

    <div class="col-md-7 required">
      <select name="model_id[1]" id="model_select_id" class="js-data-ajax" data-endpoint="models" data-placeholder="{{ trans('general.select_model') }}" style="width: 100%" aria-label="model_id" data-validation="required" required>
        @if ($model_id = old('model_id[1]', ($item->models[0]->id ?? request('model_id') ?? '')))
          <option value="{{ $model_id }}" selected="selected">
            {{ (\App\Models\AssetModel::find($model_id)) ? \App\Models\AssetModel::find($model_id)->name : '' }}
          </option>
        @else
          <option value="" role="option">{{ trans('general.select_model') }}</option>
        @endif
      </select>
      {!! $errors->first('model_id', '<div class="col-md-8 col-md-offset-3"><span class="alert-msg" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span></div>') !!}
    </div>
    
    <div class="col-md-2 col-sm-12">
      <button class="add_field_button btn btn-default btn-sm">
        <i class="fa fa-plus"></i>
      </button>
    </div>
  </div>
    
  {{-- QTY --}}
  <div class="form-group {{ $errors->has('qty') ? ' has-error' : '' }}">
    <label for="qty" class="col-md-3 control-label">{{ trans('general.quantity') }}</label>
    <div class="col-md-7 required">
      <div class="col-md-2" style="padding-left:0px">
        @if ($qty = old('qty', ($item->models[0]->pivot->qty ?? request('qty') ?? '')))
          <input class="form-control" type="text" name="qty[1]" aria-label="qty" id="qty" value="{{ old('qty[1]', ($item->models->isNotEmpty()) ? ($item->models[0]->pivot->qty) : '') }}" {!!  (\App\Helpers\Helper::checkIfRequired($item, 'qty')) ? ' data-validation="required" required' : '' !!}>
        @else
          <input type="text" class="form-control" name=qty[1] aria-label="qty" id="qty" value="" {!!  (\App\Helpers\Helper::checkIfRequired($item, 'qty')) ? ' data-validation="required" required' : '' !!}>
        @endif
      </div>
      {!! $errors->first('qty', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
  </div>

  {{-- Purchase Cost --}}
  <div class="form-group {{ $errors->has('purchase_cost') ? ' has-error' : '' }}">
    <label for="purchase_cost" class="col-md-3 control-label">{{ trans('general.value') }}</label>
    <div class="col-md-7 required">
      <div class="input-group col-md-5" style="padding-left: 0px;">
        @if ($purchase_cost = old('purchase_cost', ($item->models[0]->pivot->purchase_cost ?? request('purchase_cost') ?? '')))
          <input class="form-control" type="text" name="purchase_cost[1]" aria-label="purchase_cost" id="purchase_cost" value="{{ old('purchase_cost[1]', ($item->models->isNotEmpty()) ? \App\Helpers\Helper::formatCurrencyOutput($item->models[0]->pivot->purchase_cost) : '') }}" required>
        @else
          <input type="text" class="form-control" name=purchase_cost[1] aria-label="purchase_cost" id="purchase_cost" value="" required>
        @endif
        <span class="input-group-addon">
          @if (isset($currency_type))
            {{ $currency_type }}
          @else
            {{ $snipeSettings->default_currency }}
          @endif
        </span>
      </div>
      <div class="col-md-9" style="padding-left: 0px;">
        {!! $errors->first('purchase_cost', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span>') !!}
      </div>
    </div>
  </div>

  {{-- Asset --}}
  @if (isset($item->id) && ($item->status == 3))
    @can('assign', \App\Models\Procurement::class)
      <div class="form-group{{ $errors->has('asset_id') ? ' has-error' : '' }}">
        <label for="asset_id" class="col-md-3 control-label">{{ trans('admin/procurements/form.asset') }}</label>
        <div class="col-md-7">
          <select name="asset_id[1]" id="asset_select_id" class="js-data-ajax" data-endpoint="hardware" data-placeholder="{{ trans('general.select_asset') }}" style="width: 100%" aria-label="asset_id">
            @if ($asset_id = old('asset_id[1]', ($item->assets[0]->id ?? request('asset_id') ?? '')))
              <option value="{{ $asset_id }}" selected="selected">
                {{ (\App\Models\Asset::find($asset_id)) ? \App\Models\Asset::find($asset_id)->name : '' }}
              </option>
            @else
              <option value="" role="option">{{ trans('general.select_asset') }}</option>
            @endif
          </select>
          {!! $errors->first('asset_id', '<div class="col-md-8 col-md-offset-3"><span class="alert-msg" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span></div>') !!}
        </div>
      </div>
    @endcan
  @endif
  
  <div class="input_fields_wrap">
    @foreach ($item->models as $model)
      @if ($loop->index == 0)
        @continue
      @endif

      <span class="fields_wrapper">

        <div class="form-group">
          <label for="model_id" class="col-md-3 control-label">{{ trans('admin/procurements/form.model') }} #{{ $loop->index + 1 }}</label>
          <div class="col-md-7 col-sm-12 required">
            <select name="model_id[{{ $loop->index + 1 }}]" class="js-data-ajax model-ids" data-endpoint="models" data-placeholder="{{ trans('general.select_model') }}" style="width: 100%" aria-label="model_id" data-validation="required" required>
              <option value="{{ $model->id }}" selected="selected">
                {{ (\App\Models\AssetModel::find($model->id)) ? \App\Models\AssetModel::find($model->id)->name : '' }}
              </option>
            </select>
          </div>
          <div class="col-md-2 col-sm-12">
            <a href="#" class="remove_field btn btn-default btn-sm">
              <i class="fa fa-minus"></i>
            </a>
          </div>
        </div>

        <div class="form-group">
          <label for="qty" class="col-md-3 control-label">{{ trans('general.quantity') }} #{{ $loop->index + 1 }}</label>
          <div class="col-md-7 col-sm-12">
            <div class="col-md-2" style="padding-left: 0px">
              <input type="text" class="form-control" name="qty[{{ $loop->index + 1 }}]" aria-label="qty" value="{{ $model->pivot->qty }}" required>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="purchase_cost" class="col-md-3 control-label">{{ trans('general.value') }} #{{ $loop->index + 1 }}</label>
          <div class="col-md-9">
            <div class="input-group col-md-4" style="padding-left: 0px">
              <input type="text" class="form-control" name="purchase_cost[{{ $loop->index + 1 }}]" aria-label="purchase_cost" value="{{ $model->pivot->purchase_cost }}" required>
              <span class="input-group-addon">
                @if (isset($currency_type))
                  {{ $currency_type }}
                @else
                  {{ $snipeSettings->default_currency }}
                @endif
              </span>
            </div>
          </div>
        </div>

        @if (isset($item->id) && ($item->status == 3))
          @can('assign', \App\Models\Procurement::class)
            <div class="form-group">
              <label for="asset_id" class="col-md-3 control-label">{{ trans('admin/procurements/form.asset') }} #{{ $loop->index + 1 }}</label>
              <div class="col-md-7 col-sm-12">
                <select name="asset_id[{{ $loop->index + 1 }}]" class="js-data-ajax" data-endpoint="hardware" data-placeholder="{{ trans('general.select_asset') }}" style="width: 100%" aria-label="asset_id">
                  <option value="{{ $item->assets[$loop->index] }}" selected="selected">
                    {{ ($item->assets[$loop->index]) ? ($item->assets[$loop->index]->name) : '' }}
                  </option>
                </select>
              </div>
            </div>
          @endcan
        @endif
      </span>
    @endforeach
  </div>

  {{-- Supplier --}}
  @include('partials.forms.edit.supplier-select', ['translated_name' => trans('general.supplier'), 'fieldname' => 'supplier_id', 'required' => true])

  {{-- Location --}}
  <div id="location_id" class="form-group {{ $errors->has('location_id') ? ' has-error' : '' }}">
    {{ Form::label('location_id', trans('general.location'), array('class' => 'col-md-3 control-label')) }}

    <div class="col-md-6 required">
      <select name="location_id[]" id="location_id_location_select" class="js-data-ajax" data-endpoint="locations" data-placeholder="{{ trans('general.select_location') }}" style="width: 100%" aria-label="location_id" {!! ((isset($item)) && (\App\Helpers\Helper::checkIfRequired($item, 'location_id'))) ? 'data-validation="required" required' : '' !!} multiple>
        @php
          $location_ids = old('location_id') ?? $item->locations->map(function($location) {
            return $location->id;
          });
        @endphp
        @if ($location_ids)
          @foreach ($location_ids as $location_id)
            <option value="{{ $location_id }}" selected="selected" role="option" aria-hidden="true">
              {{ (\App\Models\Location::find($location_id)) ? \App\Models\Location::find($location_id)->name : '' }}
            </option>
          @endforeach
        @else
          <option value="" role="option">{{ trans('general.select_location') }}</option>
        @endif
      </select>
    </div>

    <div class="col-md-1 col-sm-1 text-left">
      @can('create', \App\Models\Location::class)
        @if ((!isset($hide_new)) || ($hide_new!='true'))
          <a href="{{ route('modal.show', 'location') }}" data-toggle="modal" data-target="#createModal" data-select="location_id_location_select" class="btn btn-sm btn-primary">New</a>
        @endif
      @endcan
    </div>

    {!! $errors->first('location_id', '<div class="col-md-8 col-md-offset-3"><span class="alert-msg" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span></div>' ) !!}

    @if (isset($help_text))
      <div class="col-md-7 col-sm-11 col-md-offset-3">
        <p class="help-block">{{ $help_text }}</p>
      </div>
    @endif
  </div>

  {{-- Department --}}
  @include('partials.forms.edit.department-select', ['translated_name' => trans('general.department'), 'fieldname' => 'department_id', 'required' => true])

  {{-- User --}}
  @include('partials.forms.edit.user-select', ['translated_name' => trans('general.user'), 'fieldname' => 'user_id', 'required' => true])

  {{-- Procurement form --}}
  @if ($item->status == 2 || $item->status == 3)
    @if ($item->procurement_form && $item->procurement_form!='')
      <div class="form-group">
        <label for="procurement_form_delete" class="col-md-3 control-label">{{ trans('admin/procurements/form.delete_form') }}</label>
        <div class="col-md-9">
          <label for="procurement_form_delete">
            {{ Form::checkbox('procurement_form_delete', '1', old('procurement_form_delete'), array('class' => 'minimal', 'aria-label' => 'required')) }}
          </label>
          <br>
          <img src="{{ url('/') }}/uploads/procurement_form/{{ $item->procurement_form }}" alt="Procurement for for {{ $item->procurement_tag }}" class="img-responsive">
          {!! $errors->first('procurement_form_delete', '<span class="alert-msg" aria-hidden="true"><br>:message</span>') !!}
        </div>
      </div>
    @endif

    @include ('partials.forms.edit.image-upload', ['fieldname' => 'procurement_form', 'label' => trans('admin/procurements/general.procurement_form')])
  @endif
@stop

@section('moar_scripts')
<script nonce="{{ csrf_token() }}">


  var transformed_oldvals={};

  function fetchCustomFields() {
      //save custom field choices
      var oldvals = $('#custom_fields_content').find('input,select').serializeArray();
      for(var i in oldvals) {
          transformed_oldvals[oldvals[i].name]=oldvals[i].value;
      }

      var modelid = $('#model_select_id').val();
      if (modelid == '') {
          $('#custom_fields_content').html("");
      } else {

          $.ajax({
              type: 'GET',
              url: "{{url('/') }}/models/" + modelid + "/custom_fields",
              headers: {
                  "X-Requested-With": 'XMLHttpRequest',
                  "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
              },
              _token: "{{ csrf_token() }}",
              dataType: 'html',
              success: function (data) {
                  $('#custom_fields_content').html(data);
                  $('#custom_fields_content select').select2(); //enable select2 on any custom fields that are select-boxes
                  //now re-populate the custom fields based on the previously saved values
                  $('#custom_fields_content').find('input,select').each(function (index,elem) {
                      if(transformed_oldvals[elem.name]) {
                          $(elem).val(transformed_oldvals[elem.name]).trigger('change'); //the trigger is for select2-based objects, if we have any
                      }

                  });
              }
          });
      }
  }

  function user_add(status_id) {

      if (status_id != '') {
          $(".status_spinner").css("display", "inline");
          $.ajax({
              url: "{{url('/') }}/api/v1/statuslabels/" + status_id + "/deployable",
              headers: {
                  "X-Requested-With": 'XMLHttpRequest',
                  "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
              },
              success: function (data) {
                  $(".status_spinner").css("display", "none");
                  $("#selected_status_status").fadeIn();

                  if (data == true) {
                      $("#assignto_selector").show();
                      $("#assigned_user").show();

                      $("#selected_status_status").removeClass('text-danger');
                      $("#selected_status_status").removeClass('text-warning');
                      $("#selected_status_status").addClass('text-success');
                      $("#selected_status_status").html('<i class="fa fa-check"></i> That status is deployable. This asset can be checked out.');


                  } else {
                      $("#assignto_selector").hide();
                      $("#selected_status_status").removeClass('text-danger');
                      $("#selected_status_status").removeClass('text-success');
                      $("#selected_status_status").addClass('text-warning');
                      $("#selected_status_status").html('<i class="fa fa-warning"></i> That asset status is not deployable. This asset cannot be checked out. ');
                  }
              }
          });
      }
  }

  $(document).ready(function() {

      var max_fields      = 100; //maximum input boxes allowed
      var wrapper         = $(".input_fields_wrap"); //Fields wrapper
      var add_button      = $(".add_field_button"); //Add button ID
      var x               = {{ $x }}; //initial text box count


      $(add_button).click(function(e){ //on add input button click
          e.preventDefault();
          var box_html        = '';

          // Check that we haven't exceeded the max number of asset fields
          if (x < max_fields) {
              x++; //text box increment

              box_html += '<span class="fields_wrapper">';
              box_html += '<div class="form-group"><label for="model_id" class="col-md-3 control-label">{{ trans('admin/procurements/form.model') }} #' + x + '</label>';
              box_html += '<div class="col-md-7 col-sm-12 required">';
              box_html += '<select name="model_id[' + x + ']" class="js-data-ajax model-ids" data-endpoint="models" data-placeholder="{{ trans('general.select_model') }}" style="width: 100%" aria-label="model_id" data-validation="required" required>';
              box_html += '<option value="" role="option"> {{ trans('general.select_model') }}';
              box_html += '</option>';
              box_html += '</select>';
              box_html += '</div>';
              box_html += '<div class="col-md-2 col-sm-12">';
              box_html += '<a href="#" class="remove_field btn btn-default btn-sm"><i class="fa fa-minus"></i></a>';
              box_html += '</div>';
              box_html += '</div>';
              box_html += '</div>';
              box_html += '<div class="form-group"><label for="qty" class="col-md-3 control-label">{{ trans('general.quantity') }} #' + x + '</label>';
              box_html += '<div class="col-md-7 col-sm-12 required">';
              box_html += '<div class="col-md-2" style="padding-left:0px">';
              box_html += '<input class="form-control" type="text" name="qty[' + x + ']" aria-label="qty" required>';
              box_html += '</div>';
              box_html += '</div>';
              box_html += '</div>';
              box_html += '<div class="form-group"><label for="purchase_cost" class="col-md-3 control-label">{{ trans('general.value') }} #' + x + '</label>';
              box_html += '<div class="col-md-7 required">';
              box_html += '<div class="input-group col-md-4" style="padding-left: 0px;">';
              box_html += '<input class="form-control" type="text" name="purchase_cost[' + x + ']" aria-label="purchase_cost" required>';
              box_html += '<span class="input-group-addon">';
              box_html += '@if(isset($currency_type)) {{ $currency_type }} @else {{ $snipeSettings->default_currency }} @endif';
              box_html += '</span>';
              box_html += '</div>';
              box_html += '</div>';
              box_html += '</div>';
              box_html += '</span>';
              $(wrapper).append(box_html);

              $(`select.js-data-ajax.model-ids:last`).select2({
                ajax: {
                  url: `${Ziggy.baseUrl}api/v1/models/selectlist`,
                  dataType: 'json',
                  delay: 250,
                  headers: {
                    "X-Requested-With": 'XMLHttpRequest',
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                  },
                  data: function(params) {
                    var data = {
                      search: params.term,
                      page: params.page || 1,
                    };
                    return data;
                  },
                  processResults: function(data, params) {
                    params.page = params.page || 1;

                    var answer = {
                      results: data.items,
                      pagination: {
                        more: data.pagination.more
                      }
                    };

                    return answer;
                  },
                  cache: true
                },
                escapeMarkup: function(markup) { return markup; },
                templateResult: formatDatalist,
                templateSelection: formatDataSelection
              });

          // We have reached the maximum number of extra asset fields, so disable the button
          } else {
              $(".add_field_button").attr('disabled');
              $(".add_field_button").addClass('disabled');
          }
      });

      $(wrapper).on("click",".remove_field", function(e){ //user clicks on remove text
          $(".add_field_button").removeAttr('disabled');
          $(".add_field_button").removeClass('disabled');
          e.preventDefault();
          console.log(x);

          $(this).parent('div').parent('div').parent('span').remove();
          x--;
      })

      function formatDatalist(datalist) {
        var loading_markup = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Loading...';
        
        if (datalist.loading) {
          return loading_markup;
        }

        var markup = "<div class='clearfix'>" ;
        markup +="<div class='pull-left' style='padding-right: 10px;'>";
        if (datalist.image) {
            markup += "<div style='width: 30px;'><img src='" + datalist.image + "' style='max-height: 20px; max-width: 30px;' alt='" +  datalist.text + "'></div>";
        } else {
            markup += "<div style='height: 20px; width: 30px;'></div>";
        }

        markup += "</div><div>" + datalist.text + "</div>";
        markup += "</div>";
        return markup;
      }

      function formatDataSelection(datalist) {
        return datalist.text.replace(/>/g, '&gt;')
          .replace(/</g, '&lt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }
  });


</script>
@stop