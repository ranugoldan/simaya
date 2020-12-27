@extends('layouts/default')

{{-- Page title --}}
@section('title')
View Assets for  {{ $user->present()->fullName() }}
@parent
@stop

{{-- Account page content --}}
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="box box-default">

        @if ($user->id)
          <div class="box-header with-border">
            <div class="box-heading">
              <h2 class="box-title"> {{ trans('admin/users/general.assets_user', array('name' => $user->first_name)) }}</h2>
            </div>
          </div><!-- /.box-header -->
        @endif

      <div class="box-body">
        <!-- checked out assets table -->
          <div class="table-responsive">

            <table
                  data-cookie="true"
                  data-cookie-id-table="userAssets"
                  data-pagination="true"
                  data-id-table="userAssets"
                  data-search="true"
                  data-side-pagination="client"
                  data-show-columns="true"
                  data-show-export="true"
                  data-show-refresh="true"
                  data-sort-order="asc"
                  id="userAssets"
                  class="table table-striped snipe-table"
                  data-export-options='{
                  "fileName": "my-assets-{{ date('Y-m-d') }}",
                  "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                  }'>
              <thead>
              <tr>
                <th>#</th>
                <th class="col-md-3" data-switchable="true" data-visible="true">{{ trans('general.category') }}</th>
                <th class="col-md-2" data-switchable="true" data-visible="true">{{ trans('admin/hardware/table.asset_tag') }}</th>
                <th class="col-md-3" data-switchable="true" data-visible="true">{{ trans('general.name') }}</th>
                <th class="col-md-4" data-switchable="true" data-visible="true">{{ trans('admin/hardware/table.asset_model') }}</th>
                <th class="col-md-3" data-switchable="true" data-visible="true">{{ trans('admin/hardware/table.serial') }}</th>
                <th>{{ trans('general.image') }}</th>
              </tr>

              </thead>
              <tbody>
                @php
                  $counter = 1
                @endphp
                @foreach ($user->assets as $asset)
                <tr>
                  <td>{{ $counter }}</td>
                  <td>{{ $asset->model->category->name }}</td>
                  <td>{{ $asset->asset_tag }}</td>
                  <td>{{ $asset->name }}</td>
                  <td>
                    @if ($asset->physical=='1')
                      {{ $asset->model->name }}
                    @endif
                  </td>
                  <td>{{ $asset->serial }}</td>
                  <td>
                    @if (($asset->image) && ($asset->image!=''))
                      <img src="{{ Storage::disk('public')->url(app('assets_upload_path').e($asset->image)) }}" height="50" width="50">

                    @elseif (($asset->model) && ($asset->model->image!=''))
                      <img src="{{ Storage::disk('public')->url(app('models_upload_path').e($asset->model->image)) }}" height="50" width="50">
                    @endif
                  </td>
                </tr>
                @if($settings->show_assigned_assets)
                  @php
                    $assignedCounter = 1
                  @endphp
                  @foreach ($asset->assignedAssets as $asset)
                    <tr>
                      <td>{{ $counter }}.{{ $assignedCounter }}</td>
                      <td>{{ $asset->model->category->name }}</td>
                      <td>{{ $asset->asset_tag }}</td>
                      <td>{{ $asset->name }}</td>
                      <td>
                        @if ($asset->physical=='1')
                          {{ $asset->model->name }}
                        @endif
                      </td>
                      <td>{{ $asset->serial }}</td>
                      <td>
                        @if (($asset->image) && ($asset->image!=''))
                          <img src="{{ Storage::disk('public')->url(app('assets_upload_path').e($asset->image)) }}" height="50" width="50">

                        @elseif (($asset->model) && ($asset->model->image!=''))
                          <img src="{{ Storage::disk('public')->url(app('models_upload_path').e($asset->model->image)) }}" height="50" width="50">
                        @endif
                      </td>
                    </tr>
                    @php
                      $assignedCounter++
                    @endphp
                  @endforeach
                @endif
                @php
                  $counter++
                @endphp
                @endforeach
              </tbody>
            </table>
          </div> <!-- .table-responsive-->
      </div> <!-- .box-body-->
    </div><!--.box.box-default-->
  </div> <!-- .col-md-12-->
</div> <!-- .row-->


<div class="row">
  <div class="col-md-12">
    <div class="box box-default">
      @if ($user->id)
      <div class="box-header with-border">
        <div class="box-heading">
          <h2 class="box-title"> History</h2>
        </div>
      </div><!-- /.box-header -->
      @endif

      <div class="box-body">
        <div class="table-responsive">
          <table
                data-cookie-id-table="userActivityReport"
                data-pagination="true"
                data-id-table="userActivityReport"
                data-search="true"
                data-side-pagination="server"
                data-show-columns="true"
                data-show-export="true"
                data-show-refresh="true"
                data-sort-order="desc"
                id="userActivityReport"
                class="table table-striped snipe-table"
                data-url="{{route('api.activity.index', ['target_id' => $user->id, 'target_type' => 'User', 'order' => 'desc']) }}"
                data-export-options='{
                  "fileName": "my-history-{{ date('Y-m-d') }}",
                  "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                }'>
            <thead>
            <tr>
              <th data-switchable="true" data-visible="true" data-field="icon" style="width: 40px;" class="hidden-xs" data-formatter="iconFormatter">Icon</th>
              <th data-switchable="true" data-visible="true" class="col-sm-3" data-field="created_at" data-formatter="dateDisplayFormatter">{{ trans('general.date') }}</th>
              <th data-switchable="true" data-visible="true" class="col-sm-3" data-field="admin" data-formatter="usersLinkObjFormatter">{{ trans('general.admin') }}</th>
              <th data-switchable="true" data-visible="true" class="col-sm-3" data-field="action_type">{{ trans('general.action') }}</th>
              <th data-switchable="true" data-visible="true" class="col-sm-3" data-field="item" data-formatter="polymorphicItemFormatter">{{ trans('general.item') }}</th>
            </tr>
            </thead>
          </table>
        </div> <!--.table-responsive-->

      </div> <!-- .box-body-->
    </div><!--.box.box-default-->
  </div> <!-- .col-md-12-->
</div> <!-- .row-->

@stop

@section('moar_scripts')
  @include ('partials.bootstrap-table')
@stop
