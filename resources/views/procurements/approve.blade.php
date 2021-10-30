@extends('layouts/default')

@section('title')
  {{ trans('admin/procurements/general.approve_procurement') }}: {{ $procurement->procurement_tag }}
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
      <form id="create-form" method="post" action="{{ route('procurements.update_approve', ['procurementId' => $procurement->id]) ?? \Request::url() }}" class="form-horizontal" autocomplete="off" role="form" enctype="multipart/form-data">
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
              
              {{-- Status --}}
              <div class="row form-group">
                <div class="col-xs-3 text-right"><strong>{{ trans('general.status') }}</strong></div>
                <div class="col-xs-9">{{ \App\Helpers\Helper::formatProcurementStatus($procurement->status) }}</div>
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
                  
                  {{-- Quantity --}}
                  <div class="row form-group">
                    <div class="col-xs-3 text-right"><strong>{{ trans('general.quantity') }} #{{ $loop->index + 1 }}</strong></div>
                    <div class="col-xs-9">{{ $model->pivot->qty }}</div>
                  </div>
                  
                  {{-- Purchase cost --}}
                  <div class="row form-group">
                    <div class="col-xs-3 text-right"><strong>{{ trans('general.purchase_cost') }} #{{ $loop->index + 1 }}</strong></div>
                    <div class="col-xs-9">{{ $model->pivot->purchase_cost }}</div>
                  </div>
                @endforeach
              @endif

              {{-- Total --}}
              <div class="row form-group">
                <div class="col-xs-3 text-right"><strong>{{ trans('admin/procurements/general.total') }}</strong></div>
                <div class="col-xs-9">
                  @php
                    $total_cost = $procurement->models->sum(function($model) {
                      return $model->pivot->qty * $model->pivot->purchase_cost;
                    });
                  @endphp
                  {{ \App\Helpers\Helper::formatCurrencyOutput($total_cost) }}
                </div>
              </div>
                
              {{-- Locations --}}
              @if ($procurement->locations->isNotEmpty())
                <div class="row form-group">
                  <div class="col-xs-3 text-right"><strong>{{ trans('general.locations') }}</strong></div>
                  <div class="col-xs-9">
                    @foreach ($procurement->locations as $location)
                      @if (($loop->index + 1) == $procurement->locations->count())
                      {{ $location->name }}
                      @else
                      {{ $location->name }},
                      @endif
                    @endforeach
                  </div>
                </div>
              @endif

              {{-- User --}}
              @if ($procurement->user_id)
                <div class="row form-group">
                  <div class="col-xs-3 text-right"><strong>{{ trans('general.user') }}</strong></div>
                  <div class="col-xs-6">{{ $procurement->user->first_name }}  {{ $procurement->user->last_name }}</div>
                </div>
              @endif

              {{-- Department --}}
              @if ($procurement->department_id)
                <div class="row form-group">
                  <div class="col-xs-3 text-right"><strong>{{ trans('general.department') }}</strong></div>
                  <div class="col-xs-6">{{ $procurement->department->name }}</div>
                </div>
              @endif

              {{-- Created at --}}
              @if ($procurement->created_at != '')
                <div class="row form-group">
                  <div class="col-xs-3 text-right"><strong>{{ trans('general.created_at') }}</strong></div>
                  <div class="col-xs-6">{{ \App\Helpers\Helper::getFormattedDateObject($procurement->created_at, 'datetime', false) }}</div>
                </div>
              @endif

              {{-- Updated at --}}
              @if ($procurement->updated_at != '')
                <div class="row form-group">
                  <div class="col-xs-3 text-right"><strong>{{ trans('general.updated_at') }}</strong></div>
                  <div class="col-xs-6">{{ \App\Helpers\Helper::getFormattedDateObject($procurement->updated_at, 'datetime', false) }}</div>
                </div>
              @endif

              {{-- Procurement form --}}
              @include ('partials.forms.edit.image-upload', ['fieldname' => 'procurement_form', 'label' => trans('admin/procurements/general.procurement_form'), 'required' => true])
            </div>

            {{-- Submit button --}}
            @include('partials.forms.edit.submit')
          </div>

        </div>
      </form>
    </div>
  </div>
@stop