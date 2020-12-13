@extends('layouts/default')

{{-- Page title --}}
@section('title')
 {{ $land->name }}
 {{ trans('general.land') }}
@parent
@stop

@section('header_right')
<a href="{{ URL::previous() }}" class="btn btn-primary pull-right">
  {{ trans('general.back') }}</a>
@stop


{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="box box-default">
      @if ($land->id)
      <div class="box-header with-border">
        <div class="box-heading">
          <h2 class="box-title"> {{ $land->name }}</h2>
        </div>
      </div><!-- /.box-header -->
      @endif

      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <div class="table table-responsive">

              <table
                      data-cookie-id-table="landCheckedoutTable"
                      data-pagination="true"
                      data-id-table="landCheckedoutTable"
                      data-search="false"
                      data-side-pagination="server"
                      data-show-columns="true"
                      data-show-export="true"
                      data-show-footer="true"
                      data-show-refresh="true"
                      data-sort-order="asc"
                      data-sort-name="name"
                      id="landCheckedoutTable"
                      class="table table-striped snipe-table"
                      data-url="{{route('api.land.showUsers', $land->id)}}"
                      data-export-options='{
                "fileName": "export-land-{{ str_slug($land->name) }}-checkedout-{{ date('Y-m-d') }}",
                "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                }'>
                <thead>
                  <tr>
                    <th data-searchable="false" data-sortable="false" data-field="name">{{ trans('general.user') }}</th>
                    <th data-searchable="false" data-sortable="false" data-field="created_at" data-formatter="dateDisplayFormatter">{{ trans('general.date') }}</th>
                    <th data-searchable="false" data-sortable="false" data-field="admin">{{ trans('general.admin') }}</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div> <!-- /.col-md-12-->

        </div>
      </div>
    </div> <!-- /.box.box-default-->
  </div> <!-- /.col-md-9-->
  {{-- <div class="col-md-3">
    @if ($land->image!='')
      <div class="col-md-12 text-center" style="padding-bottom: 15px;">
        <a href="{{ Storage::disk('public')->url('land/'.e($land->image)) }}" data-toggle="lightbox">
            <img src="{{ Storage::disk('public')->url('land/'.e($land->image)) }}" class="img-responsive img-thumbnail" alt="{{ $land->name }}"></a>
      </div>
    @endif

    @if ($land->purchase_date)
      <div class="col-md-12" style="padding-bottom: 5px;">
        <strong>{{ trans('general.purchase_date') }}: </strong>
        {{ $land->purchase_date }}
      </div>
    @endif

    @if ($land->purchase_cost)
      <div class="col-md-12" style="padding-bottom: 5px;">
        <strong>{{ trans('general.purchase_cost') }}:</strong>
        {{ $snipeSettings->default_currency }}
        {{ \App\Helpers\Helper::formatCurrencyOutput($land->purchase_cost) }}
      </div>
    @endif

    @if ($land->item_no)
      <div class="col-md-12" style="padding-bottom: 5px;">
        <strong>{{ trans('admin/land/general.item_no') }}:</strong>
        {{ $land->item_no }}
      </div>
    @endif

    @if ($land->model_number)
      <div class="col-md-12" style="padding-bottom: 5px;">
        <strong>{{ trans('general.model_no') }}:</strong>
        {{ $land->model_number }}
      </div>
    @endif

    @if ($land->manufacturer)
      <div class="col-md-12" style="padding-bottom: 5px;">
        <strong>{{ trans('general.manufacturer') }}:</strong>
        {{ $land->manufacturer->name }}
      </div>
    @endif

    @if ($land->order_number)
      <div class="col-md-12" style="padding-bottom: 5px;">
        <strong>{{ trans('general.order_number') }}:</strong>
        {{ $land->order_number }}
      </div>
    @endif

    @can('checkout', \App\Models\Land::class)
    <div class="col-md-12">
        <a href="{{ route('checkout/land', $land->id) }}" style="padding-bottom:5px;" class="btn btn-primary btn-sm" {{ (($land->numRemaining() > 0 ) ? '' : ' disabled') }}>{{ trans('general.checkout') }}</a>
    </div>
    @endcan
  </div> <!-- /.col-md-3--> --}}
</div> <!-- /.row-->

@stop

@section('moar_scripts')
@include ('partials.bootstrap-table', ['exportFile' => 'land' . $land->name . '-export', 'search' => false])
@stop
