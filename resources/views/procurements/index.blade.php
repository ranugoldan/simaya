@extends('layouts/default')

@section('title')
  {{ trans('general.procurements') }}
@stop

@section('header_right')
  @can('create', \App\Models\Procurement::class)
    <a href="{{ route('procurements.create') }}" class="btn btn-primary pull-right"></i> {{ trans('general.create') }}</a>
  @endcan
@stop

@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-body">
          <div class="table-responsive">
            <table
              data-columns="{{ \App\Presenters\ProcurementPresenter::dataTableLayout() }}"
              data-cookie-id-table="procurementTable"
              data-pagination="true"
              data-id-table="procurementTable"
              data-search="true"
              data-show-footer="true"
              data-side-pagination="server"
              data-show-columns="true"
              data-show-export="true"
              data-show-refresh="true"
              data-sort-order="asc"
              id="procurementTable"
              class="table table-striped snipe-table"
              data-url="{{ route('api.procurements.index') }}"
              data-export-options='{
                "fileName": "export-procurements-{{ date('Y-m-d') }}",
                "ignoreColumn": ["actions"]
              }'>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop

@section('moar_scripts')
  @include('partials.bootstrap-table', ['exportFile' => 'procurements-export', 'search' => true])
@stop