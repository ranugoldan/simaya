@extends('layouts/default')

{{-- Page title --}}
@section('title')

 {{ $category->name }}
 {{ ucwords($category_type_route) }}

@parent
@stop

@section('header_right')
<div class="btn-group pull-right">
  <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">{{ trans('button.actions') }}
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
    <li><a href="{{ route('categories.edit', ['category' => $category->id]) }}">{{ trans('admin/categories/general.edit') }}</a></li>
    <li><a href="{{ route('categories.create') }}">{{ trans('general.create') }}</a></li>
  </ul>
</div>
@stop

{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="box box-default">
      <div class="box-body">

        <table

                @if ($category->category_type=='asset')

                  data-columns="{{ \App\Presenters\AssetPresenter::dataTableLayout() }}"
                  data-cookie-id-table="categoryAssetsTable"
                  id="categoryAssetsTable"
                  data-id-table="categoryAssetsTable"
                  data-export-options='{
                    "fileName": "export-{{ str_slug($category->name) }}-assets-{{ date('Y-m-d') }}",
                    "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                    }'
                @endif

                data-pagination="true"
                data-search="true"
                data-show-footer="true"
                data-side-pagination="server"
                data-show-columns="true"
                data-show-export="true"
                data-show-refresh="true"
                data-sort-order="asc"
                class="table table-striped snipe-table"
                data-url="{{ route('api.'.$category_type_route.'.index',['category_id'=> $category->id]) }}">
            
      </table>

      </div>
    </div>
  </div>
</div>
@stop

@section('moar_scripts')
@include ('partials.bootstrap-table')
@stop
