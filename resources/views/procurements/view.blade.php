@extends('layouts/default')

{{-- Page title --}}
@section('title')
  {{ trans('admin/procurements/general.view') }} {{ $procurement->procurement_tag }}
  @parent
@stop

{{-- Right header --}}
@section('header_right')
  {{-- Empty --}}
@stop

{{-- Page content --}}
@section('content')
  <div class="row">
    <div class="col-md-12">

      {{-- Tabs --}}
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active">
            <a href="#info" data-toggle="tab">
              <span class="hidden-lg hidden-md">
                <i class="fa fa-info-circle" aria-hidden="true"></i>
              </span>
              <span class="hidden-xs hidden-sm">
                {{ trans('general.details') }}
              </span>
            </a>
          </li>
        </ul>

        {{-- Tab content --}}
        <div class="tab-content">
  
          {{-- Details tab content --}}
          <div class="tab-pane fade in active" id="details">
            <div class="row">
              <div class="col-md-8">
                <div class="container row-striped">
  
                  {{-- Procurement tag --}}
                  @if ($procurement->procurement_tag)
                    <div class="row">
                      <div class="col-md-2">
                        <strong>{{ trans('admin/procurements/table.procurement_tag') }}</strong>
                      </div>
                      <div class="col-md-6">
                        {{ $procurement->procurement_tag }}
                      </div>
                    </div>
                  @endif
  
                  {{-- Status --}}
                  @if ($procurement->status)
                    <div class="row">
                      <div class="col-md-2">
                        <strong>{{ trans('general.status') }}</strong>
                      </div>
                      <div class="col-md-6">
                        {{ $procurement->status }}
                      </div>
                    </div>
                  @endif
  
                  {{-- Supplier --}}
                  @if ($procurement->supplier_id)
                    <div class="row">
                      <div class="col-md-2">
                        <strong>{{ trans('general.supplier') }}</strong>
                      </div>
                      <div class="col-md-6">
                        @can('superuser')
                          <a href="{{ route('suppliers.show', $procurement->supplier_id) }}">
                            {{ $procurement->supplier->name }}
                          </a>
                        @else
                          {{ $procurement->supplier->name }}
                        @endcan
                      </div>
                    </div>
                  @endif

                  @if ($procurement->models)
                    @foreach ($procurement->models as $model)
                      {{-- Model --}}
                      <div class="row">
                        <div class="col-md-2">
                          <strong>{{ trans('admin/procurements/general.model') }} #{{ $loop->index + 1 }}</strong>
                        </div>
                        <div class="col-md-6">
                          @can('view', \App\Models\AssetModel::class)
                            <a href="{{ route('models.show', $model->id) }}">
                              {{ $model->name }}
                            </a>
                          @else
                            {{ $model->name }}
                          @endcan
                        </div>
                      </div>

                      {{-- Quantity --}}
                      <div class="row">
                        <div class="col-md-2">
                          <strong>{{ trans('general.quantity') }} #{{ $loop->index + 1 }}</strong>
                        </div>
                        <div class="col-md-6">
                          {{ $model->pivot->qty }}
                        </div>
                      </div>

                      {{-- Purchase cost --}}
                      <div class="row">
                        <div class="col-md-2">
                          <strong>{{ trans('general.purchase_cost') }} #{{ $loop->index + 1}}</strong>
                        </div>
                        <div class="col-md-6">
                          {{ $model->pivot->purchase_cost }}
                        </div>
                      </div>
                    @endforeach

                    {{-- Total --}}
                    <div class="row">
                      <div class="col-md-2">
                        <strong>{{ trans('admin/procurements/general.total') }}</strong>
                      </div>
                      <div class="col-md-6">
                        @php
                          $total_cost = $procurement->models->sum(function($model) {
                            return $model->pivot->qty * $model->pivot->purchase_cost;
                          });
                        @endphp
                        {{ $total_cost }}
                      </div>
                    </div>
                  @endif

                  {{-- Locations --}}
                  @if ($procurement->locations)
                    <div class="row">
                      <div class="col-md-2">
                        <strong>{{ trans('general.locations') }}</strong>
                      </div>
                      <div class="col-md-6">
                        @can('superuser')
                          @foreach ($procurement->locations as $location)
                            @if (($loop->index + 1) == $procurement->locations->count())
                              <a href="{{ route('locations.show', $location->id) }}">{{ $location->name }}</a>
                            @else
                              <a href="{{ route('locations.show', $location->id) }}">{{ $location->name }}</a>,
                            @endif
                          @endforeach
                        @else
                          @foreach ($procurement->locations as $location)
                            @if (($loop->index + 1) == $procurement->locations->count())
                              {{ $location->name }}
                            @else
                              {{ $location->name }},
                            @endif
                          @endforeach
                        @endcan
                      </div>
                    </div>
                  @endif

                  {{-- User --}}
                  @if ($procurement->user_id)
                    <div class="row">
                      <div class="col-md-2">
                        <strong>{{ trans('general.user') }}</strong>
                      </div>
                      <div class="col-md-6">
                        @can('superuser')
                          <a href="{{ route('users.show', $procurement->user_id) }}">
                            {{ $procurement->user->first_name }} {{ $procurement->user->last_name }}
                          </a>
                        @else
                          {{ $procurement->user->first_name }} {{ $procurement->user->last_name }}
                        @endcan
                      </div>
                    </div>
                  @endif

                  {{-- Department --}}
                  @if ($procurement->department_id)
                    <div class="row">
                      <div class="col-md-2">
                        <strong>{{ trans('general.department') }}</strong>
                      </div>
                      <div class="col-md-6">
                        @can('superuser')
                          <a href="{{ route('departments.show', $procurement->department_id) }}">
                            {{ $procurement->department->name }}
                          </a>
                        @else
                          {{ $procurement->department->name }}
                        @endcan
                      </div>
                    </div>
                  @endif

                  {{-- Created at --}}
                  @if ($procurement->created_at != '')
                    <div class="row">
                      <div class="col-md-2">
                        <strong>{{ trans('general.created_at') }}</strong>
                      </div>
                      <div class="col-md-6">
                        {{ \App\Helpers\Helper::getFormattedDateObject($procurement->created_at, 'datetime', false) }}
                      </div>
                    </div>
                  @endif

                  {{-- Updated at --}}
                  @if ($procurement->updated_at != '')
                    <div class="row">
                      <div class="col-md-2">
                        <strong>{{ trans('general.updated_at') }}</strong>
                      </div>
                      <div class="col-md-6">
                        {{ \App\Helpers\Helper::getFormattedDateObject($procurement->updated_at, 'datetime', false) }}
                      </div>
                    </div>
                  @endif

                </div>
              </div>
            </div>
          </div> {{-- Details tab content ends --}}
        </div>
      </div>
    </div>
  </div>
@stop

@section('moar_scripts')
  @include('partials.bootstrap-table')
@stop