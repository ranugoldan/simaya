@extends('layouts/default')

{{-- Page title --}}
@section('title')

 {{ $manufacturer->name }}
 {{ trans('general.manufacturer') }}
@parent
@stop

@section('header_right')
  <div class="btn-group pull-right">
     <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">{{ trans('button.actions') }}
     <span class="caret"></span>
      </button>
      <ul class="dropdown-menu">
        <li><a href="{{ route('manufacturers.edit', $manufacturer->id) }}">{{ trans('admin/manufacturers/table.update') }}</a></li>
        <li><a href="{{ route('manufacturers.create') }}">{{ trans('admin/manufacturers/table.create') }}</a></li>
      </ul>
  </div>
@stop

{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="nav-tabs-custom">

      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#assets" data-toggle="tab">Assets</a>
        </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade in active" id="assets">

          <table
                  data-columns="{{ \App\Presenters\AssetPresenter::dataTableLayout() }}"
                  data-cookie-id-table="assetsListingTable"
                  data-pagination="true"
                  data-id-table="assetsListingTable"
                  data-search="true"
                  data-show-footer="true"
                  data-side-pagination="server"
                  data-show-columns="true"
                  data-show-export="true"
                  data-show-refresh="true"
                  data-sort-order="asc"
                  id="assetsListingTable"
                  class="table table-striped snipe-table"
                  data-url="{{ route('api.assets.index', ['manufacturer_id' => $manufacturer->id, 'itemtype' => 'assets']) }}"
                  data-export-options='{
              "fileName": "export-manufacturers-{{ str_slug($manufacturer->name) }}-assets-{{ date('Y-m-d') }}",
              "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
              }'>
          </table>

        </div> <!-- /.tab-pane assets -->

      </div> <!-- /.tab-content -->
    </div>  <!-- /.nav-tabs-custom -->
  </div><!-- /. col-md-12 -->
</div> <!-- /.row -->
@stop

@section('moar_scripts')
@include ('partials.bootstrap-table', ['exportFile' => 'manufacturer' . $manufacturer->name . '-export', 'search' => false])

@stop
