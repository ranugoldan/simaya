@extends('layouts/edit-form', [
  'createText'    => trans('admin/procurements/form.create'),
  'updateText'    => trans('admin/procurements/form.update'),
  'topSubmit'     => true,
  'helpText'      => trans('admin/procurements/form.help'),
  'helpPosition'  => 'right',
  'formAction'    => (isset($item->id)) ? route('procurements.update', ['procurement' => $item->id]) : route('procurements.store'),
])

@section('inputFields')
  {{-- Procurement Tag --}}
  {{-- <div class="form-group {{ $errors->has('procurement_tag') ? 'has-error' : '' }}">
    <label for="procurement_tag" class="col-md-3 control-label">{{ trans('a') }}</label>

    @if ($item->id) --}}
      {{-- editing an existing procurement --}}
      {{-- <div class="col-md-7 col-sm-12 {{ (\App\Helpers\Helper::checkIfRequired($item, 'procurement_tag')) ? 'required' : '' }}">
        <input type="text" class="form-control" name="procurement_tags[1]" id="procurement_tag" value="{{ Request::old('procurement_tag', $item->procurement_tag) }}" data-validation="required">
        {!! $errors->first('procurement_tags', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
        {!! $errors->first('procurement_tag', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
      </div>
    @else --}}
      {{-- creating a new procurement --}}
      {{-- <div class="col-md-7 col-sm-12" {{ (\App\Helpers\Helper::checkIfRequired($item, 'procurement_tag')) ? ' required' : '' }}>
        <input type="text" class="form-control" name="procurement_tags[1]" id="procurement_tag" value="{{ Request::old('procurement_tag', \App\Models\Procurement::autoincrement_procurement()) }}" data-validation="required">
        {!! $errors->first('procurement_tags', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
        {!! $errors->first('procurement_tag', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
      </div>
    @endif
  </div> --}}

  <div class="form-group {{ $errors->has('procurement_tag') ? 'has-error' : '' }}">
    <label for="procurement_tag" class="col-md-3 control-label">{{ trans('admin/procurements/form.tag') }}</label>
    <div class="col-md-7 col-sm-12{{  (\App\Helpers\Helper::checkIfRequired($item, 'procurement_tag')) ? ' required' : '' }}">
        <input class="form-control" type="text" name="procurement_tag" aria-label="procurement_tag" id="procurement_tag" value="{{ old('procurement_tag', $item->procurement_tag) }}"{!!  (\App\Helpers\Helper::checkIfRequired($item, 'procurement_tag')) ? ' data-validation="required" required' : '' !!} />
        {!! $errors->first('procurement_tag', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
  </div>

  {{-- Status --}}
  <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
    <label for="status" class="col-md-3 control-label">{{ trans('admin/procurements/form.status') }}</label>
    <div class="col-md-7 col-sm-12 {{ (\App\Helpers\Helper::checkIfRequired($item, 'status')) ? ' required' : '' }}">
      <input type="text" class="form-control" name="status" aria-label="status" id="status" value="{{ old('status', $item->status) }}" {!!  (\App\Helpers\Helper::checkIfRequired($item, 'status')) ? 'data-validation="required" required' : '' !!}>
      {!! $errors->first('status', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
  </div>

  {{-- Models --}}
  <div id="model_id" class="form-group {{ $errors->has('model_id') ? 'has-error' : '' }}">
    {{ Form::label('model_id', trans('admin/procurements/form.model'), array('class' => 'col-md-3 control-label')) }}

    <div class="col-md-7 required">
      <select name="model_id" id="model_select_id" class="js-data-ajax" data-endpoint="models" data-placeholder="{{ trans('general.select_model') }}" style="width: 100%" aria-label="model_id" data-validation="required" multiple required>
        @if ($model_id = old('model_id', ($item->model_id ?? request('model_id') ?? '')))
          <option value="{{ $model_id }}" selected="selected">
            {{ (\App\Models\AssetModel::find($model_id)) ? \App\Models\AssetModel::find($model_id)->name : '' }}
          </option>
        @else
          <option value="" role="option">{{ trans('general.select_model') }}</option>
        @endif
      </select>
    </div>

    <div class="col-md-1 col-sm-1 text-left">
      @can('create', \App\Models\AssetModel::class)
        <a href="{{ route('modal.show', 'model') }}" data-toggle="modal" data-target="#createModal" data-select="model_select_id" class="btn btn-sm btn-primary">New</a>
        <span class="mac_spinner" style="padding-left: 10px; color: green; display: none; width: 30px;">
          <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
        </span>
      @endcan
    </div>

    {{-- <div class="col-md-2 col-sm-12">
      <button class="add_field_button btn btn-default btn-sm">
        <i class="fa fa-plus"></i>
      </button>
    </div> --}}

    {!! $errors->first('model_id', '<div class="col-md-8 col-md-offset-3"><span class="alert-msg" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span></div>') !!}
  </div>

  {{-- <div class="input_fields_wrap"></div> --}}

  {{-- Assets --}}
  <div id="asset_id" class="form-group {{ $errors->has('asset_id') ? 'has-error' : '' }}">
    {{ Form::label('asset_id', trans('admin/procurements/form.asset'), array('class' => 'col-md-3 control-label')) }}

    <div class="col-md-7">
      <select name="asset_id" id="asset_id_select" class="js-data-ajax select2" data-endpoint="hardware" data-placeholder="{{ trans('general.select_asset') }}" aria-label="asset_id" style="width: 100%" data-asset-status-type="" multiple>
        @if ((!isset($unselect)) && ($asset_id = old('asset_id', (isset($asset) ? $asset->id  : (isset($item) ? $item->asset_id : '')))))
          <option value="{{ $asset_id }}" selected="selected" role="option" aria-selected="true">
            {{ (\App\Models\Asset::find($asset_id)) ? \App\Models\Asset::find($asset_id)->present()->fullName : '' }}
          </option>
        @else
          @if (!isset($multiple))
            <option value="" role="option">{{ trans('general.select_asset') }}</option>
          @endif
        @endif
      </select>
    </div>
  </div>

  {!! $errors->first('asset_id', '<div class="col-md-8 col-md-offset-3"><span class="alert-msg" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span></div>') !!}

  {{-- Supplier --}}
  @include('partials.forms.edit.supplier-select', ['translated_name' => trans('general.supplier'), 'fieldname' => 'supplier_id'])

  {{-- QTY --}}
  @include('partials.forms.edit.quantity')

  {{-- Purchase Cost --}}
  @include('partials.forms.edit.purchase_cost')

  {{-- Location --}}
  <div id="location_id" class="form-group {{ $errors->has('location_id') ? ' has-error' : '' }}">
    {{ Form::label('location_id', trans('general.location'), array('class' => 'col-md-3 control-label')) }}

    <div class="col-md-6">
      <select name="location_id" id="location_id_location_select" class="js-data-ajax" data-endpoint="locations" data-placeholder="{{ trans('general.select_location') }}" style="width: 100%" aria-label="location_id" {!! ((isset($item)) && (\App\Helpers\Helper::checkIfRequired($item, 'location_id'))) ? 'data-validation="required" required' : '' !!}>
        @if ($location_id = old('location_id', (isset($item)) ? $item->location_id : ''))
          <option value="{{ $location_id }}" selected="selected" role="option" aria-hidden="true">
            {{ (\App\Models\Location::find($location_id)) ? \App\Models\Location::find($location_id)->name : '' }}
          </option>
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
  @include('partials.forms.edit.department-select', ['translated_name' => trans('general.department'), 'fieldname' => 'department_id'])

  {{-- User --}}
  @include('partials.forms.edit.user-select', ['translated_name' => trans('general.user'), 'fieldname' => 'user_id'])
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


  // $(function () {
  //     //grab custom fields for this model whenever model changes.
  //     $('#model_select_id').on("change", fetchCustomFields);

  //     //initialize assigned user/loc/asset based on statuslabel's statustype
  //     user_add($(".status_id option:selected").val());

  //     //whenever statuslabel changes, update assigned user/loc/asset
  //     $(".status_id").on("change", function () {
  //         user_add($(".status_id").val());
  //     });

  // });


  // Add another asset tag + serial combination if the plus sign is clicked
  $(document).ready(function() {

      var max_fields      = 100; //maximum input boxes allowed
      var wrapper         = $(".input_fields_wrap"); //Fields wrapper
      var add_button      = $(".add_field_button"); //Add button ID
      var x               = 1; //initial text box count




      // $(add_button).click(function(e){ //on add input button click

      //     e.preventDefault();

      //     var auto_tag        = $("#asset_tag").val().replace(/[^\d]/g, '');
      //     var box_html        = '';


      //     // Check that we haven't exceeded the max number of asset fields
      //     if (x < max_fields) {

      //         if (auto_tag!='') {
      //             auto_tag = parseInt(auto_tag) + parseInt(x);
      //         } else {
      //             auto_tag = '';
      //         }

      //         x++; //text box increment

      //         box_html += '<span class="fields_wrapper">';
      //         box_html += '<div class="form-group"><label for="asset_tag" class="col-md-3 control-label">{{ trans('admin/hardware/form.tag') }} ' + x + '</label>';
      //         box_html += '<div class="col-md-7 col-sm-12 required">';
      //         box_html += '<input type="text"  class="form-control" name="asset_tags[' + x + ']" value="{{ (($snipeSettings->auto_increment_prefix!='') && ($snipeSettings->auto_increment_assets=='1')) ? $snipeSettings->auto_increment_prefix : '' }}'+ auto_tag +'" data-validation="required">';
      //         box_html += '</div>';
      //         box_html += '<div class="col-md-2 col-sm-12">';
      //         box_html += '<a href="#" class="remove_field btn btn-default btn-sm"><i class="fa fa-minus"></i></a>';
      //         box_html += '</div>';
      //         box_html += '</div>';
      //         box_html += '</div>';
      //         box_html += '<div class="form-group"><label for="serial" class="col-md-3 control-label">{{ trans('admin/hardware/form.serial') }} ' + x + '</label>';
      //         box_html += '<div class="col-md-7 col-sm-12">';
      //         box_html += '<input type="text"  class="form-control" name="serials[' + x + ']">';
      //         box_html += '</div>';
      //         box_html += '</div>';
      //         box_html += '</span>';
      //         $(wrapper).append(box_html);

      //     // We have reached the maximum number of extra asset fields, so disable the button
      //     } else {
      //         $(".add_field_button").attr('disabled');
      //         $(".add_field_button").addClass('disabled');
      //     }
      // });

      // $(wrapper).on("click",".remove_field", function(e){ //user clicks on remove text
      //     $(".add_field_button").removeAttr('disabled');
      //     $(".add_field_button").removeClass('disabled');
      //     e.preventDefault();
      //     console.log(x);

      //     $(this).parent('div').parent('div').parent('span').remove();
      //     x--;
      // })
  });


</script>
@stop