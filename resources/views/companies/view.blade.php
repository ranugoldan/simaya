@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ $company->name }}
    @parent
@stop

{{-- Page content --}}
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">


                    <li class="active">
                        <a href="#asset_tab" data-toggle="tab">
                            <span class="hidden-lg hidden-md">
                            <i class="fa fa-barcode" aria-hidden="true"></i>
                            </span>
                            <span class="hidden-xs hidden-sm">{{ trans('general.assets') }}</span>
                        </a>
                    </li>

                    <li>
                        <a href="#users_tab" data-toggle="tab">
                            <span class="hidden-lg hidden-md">
                            <i class="fa fa-users"></i></span>
                            <span class="hidden-xs hidden-sm">{{ trans('general.people') }}</span>
                        </a>
                    </li>



                </ul>

                <div class="tab-content">

                    <div class="tab-pane fade in active" id="asset_tab">
                        <!-- checked out assets table -->
                        <div class="table-responsive">
                            <table
                                    data-columns="{{ \App\Presenters\AssetPresenter::dataTableLayout() }}"
                                    data-cookie-id-table="assetsListingTable"
                                    data-pagination="true"
                                    data-id-table="assetsListingTable"
                                    data-search="true"
                                    data-side-pagination="server"
                                    data-show-columns="true"
                                    data-show-export="true"
                                    data-show-refresh="true"
                                    data-sort-order="asc"
                                    id="assetsListingTable"
                                    class="table table-striped snipe-table"
                                    data-url="{{route('api.assets.index',['company_id' => $company->id]) }}"
                                    data-export-options='{
                              "fileName": "export-companies-{{ str_slug($company->name) }}-assets-{{ date('Y-m-d') }}",
                              "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                              }'>
                            </table>
                        </div>
                    </div><!-- /asset_tab -->

                    <div class="tab-pane" id="users_tab">
                        <div class="table-responsive">

                            <table
                                    data-columns="{{ \App\Presenters\UserPresenter::dataTableLayout() }}"
                                    data-cookie-id-table="usersTable"
                                    data-pagination="true"
                                    data-id-table="usersTable"
                                    data-search="true"
                                    data-side-pagination="server"
                                    data-show-columns="true"
                                    data-show-export="true"
                                    data-show-refresh="true"
                                    data-sort-order="asc"
                                    id="usersTable"
                                    class="table table-striped snipe-table"
                                    data-url="{{route('api.users.index',['company_id' => $company->id]) }}"
                                    data-export-options='{
                              "fileName": "export-companies-{{ str_slug($company->name) }}-users-{{ date('Y-m-d') }}",
                              "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                              }'>

                            </table>
                        </div>
                    </div><!-- /consumables-tab -->




                </div><!-- /.tab-content -->
            </div><!-- nav-tabs-custom -->
        </div>
    </div>

@stop
@section('moar_scripts')
    @include ('partials.bootstrap-table')

@stop

