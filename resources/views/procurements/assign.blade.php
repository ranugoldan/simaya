@extends('layouts/default')

@section('title')
  {{ trans('admin/procurements/general.assign_assets') }}: {{ $procurement->procurement_tag }}
  @parent
@stop

{{-- Right header --}}
@section('header_right')
  <a href="{{ URL::previous() }}" class="btn btn-primary pull-right">
    {{ trans('general.back') }}
  </a>
@stop

{{-- Page content --}}
@section('content')
  <div class="row">
    <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-12 col-sm-offset-0">
      <form id="create-form" method="post" action="{{ route('procurements.update_assign', ['procurementId' => $procurement->id]) ?? \Request::url() }}" class="form-horizontal" autocomplete="off" role="form" enctype="multipart/form-data">
        <div class="box box-default">

          {{-- Box header --}}
          <div class="box-header with-border text-right">
            <div class="col-md-12 box-title text-right" style="padding: 0px; margin: 0px;">
              <div class="col-md-12" style="padding: 0px; margin: 0px;">

                {{-- Box title --}}
                <div class="col-md-9 text-left">
                  @if ($procurement->id)
                    <h2 class="box-title text-left" style="padding-top: 8px;">
                      {{-- Empty --}}
                    </h2>
                  @endif
                </div>

                {{-- Box action buttons --}}
                <div class="col-md-3 text-right"  style="padding-right: 10px;">
                  <a href="{{ URL::previous() }}" class="btn btn-link text-left">
                    {{ trans('button.cancel') }}
                  </a>
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-check icon-white" aria-hidden="true"></i>
                    {{ trans('general.save') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          {{-- Box body --}}
          <div class="box-body">
            {{ method_field('PUT') }}
            {{ csrf_field() }}

            {{-- Fields --}}
            <div class="container">

              {{-- Procurement tag --}}
              <div class="row form-group">
                <div class="col-xs-3 text-right"><strong>{{ trans('admin/procurements/form.tag') }}</strong></div>
                <div class="col-xs-9">{{ $procurement->procurement_tag }}</div>
              </div>
              
              {{-- Supplier --}}
              @if ($procurement->supplier_id)
                <div class="row form-group">
                  <div class="col-xs-3 text-right"><strong>{{ trans('general.supplier') }}</strong></div>
                  <div class="col-xs-9">{{ $procurement->supplier->name }}</div>
                </div>
              @endif
              
              @if ($procurement->models->isNotEmpty())
                @foreach($procurement->models as $model)
                  {{-- Model --}}
                  <div class="row form-group">
                    <div class="col-xs-3 text-right"><strong>{{ trans('admin/procurements/form.model') }} #{{ $loop->index + 1 }}</strong></div>
                    <div class="col-xs-9">{{ $model->name }}</div>
                  </div>

                  {{-- Asset input --}}
                  <div class="row form-group">
                    <div class="col-xs-3 text-right"><strong>{{ trans('general.asset') }} #{{ $loop->index + 1 }}</strong></div>
                    <div class="col-xs-5 required">
                      <select name="asset_id[{{ $loop->index + 1 }}]" class="js-data-ajax select2" data-endpoint="hardware" data-placeholder="{{ trans('general.select_asset') }}" aria-label="asset_id[{{ $loop->index + 1 }}]" name="asset_id[{{ $loop->index + 1 }}]" style="width: 100%" required>
                        <option value="" role="option">{{ trans('general.select_asset') }}</option>
                      </select>
                    </div>
                  </div>
                @endforeach
              @endif

              {{-- Procurement form --}}
              @if ($procurement->procurement_form)
                <div class="form-group">
                  <label class="col-md-3 control-label">{{ trans('admin/procurements/general.procurement_form') }}</label>
                  <div class="col-md-5">
                    <label class="control-label"></label>
                    <div>
                      <a href="{{ Storage::disk('public')->url(app('procurement_form_upload_path').e($procurement->procurement_form)) }}" target="_blank">
                        <img src="{{ Storage::disk('public')->url(app('procurement_form_upload_path').e($procurement->procurement_form)) }}" class="img-responsive" />
                      </a>
                    </div>
                  </div>
                </div>
              @endif

            </div>

            {{-- Submit button --}}
            @include('partials.forms.edit.submit')
          </div>

        </div>
      </form>
    </div>
  </div>
@stop