@extends('layouts/default')

{{-- Page title --}}
@section('title')
  {{ trans('general.companies') }}
  @parent
@stop

@section('header_right')
  <a href="{{ route('companies.create') }}" class="btn btn-primary pull-right">
    {{ trans('general.create') }}</a>
@stop
{{-- Page content --}}
@section('content')
  <div class="row">
    <div class="col-md-9">
      <div class="box box-default">
        <div class="box-body">
          <div class="table-responsive">

            <table
              data-columns="{{ \App\Presenters\CompanyPresenter::dataTableLayout() }}"
              data-cookie-id-table="companiesTable"
              data-pagination="true"
              data-id-table="companiesTable"
              data-search="true"
              data-side-pagination="server"
              data-show-columns="true"
              data-show-export="true"
              data-show-refresh="true"
              data-sort-order="asc"
              id="companiesTable"
              class="table table-striped snipe-table"
              data-url="{{ route('api.companies.index') }}"
              data-export-options='{
                        "fileName": "export-companies-{{ date('Y-m-d') }}",
                        "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                        }'>

            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- side address column -->
    <div class="col-md-3">
      <h4>МВЗ</h4>
      <p>
        Можно использовать как информационное поле и для разделения доступа по МВЗ при включении соответствующей опции в настройках.
      </p>
  </div>

@stop

@section('moar_scripts')
  @include ('partials.bootstrap-table')
@stop
