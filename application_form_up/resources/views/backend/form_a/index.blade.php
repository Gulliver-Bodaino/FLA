@extends('adminlte::page')

@section('title', 'FLA管理画面 > フォーム A > 申込データ管理')

@section('content_header')
    <h1>フォーム A</h1>
@stop

@section('content')
<div class="row">
    <div class="col">
        <form class="application_search" action="{{ route('backend.form_a.applications.index') }}" method="GET">
            <div class="card">
                <div class="card-header bg-navy">
                    <div class="card-title">申込データ管理</div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label for="start_date">申込日</label>
                            <div class="form-inline">
                                <div class="form-group">
                                    <input type="text" name="start_date" id="start_date" class="form-control date" value="{{ request('start_date') }}" autocomplete="off">
                                    ～
                                    <input type="text" name="end_date" id="end_date" class="form-control date" value="{{ request('end_date') }}" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label for="status">ステータス</label>
                            {{ Form::select('status', ['' => ''] + array_flip(config('common.status')), request('status'), ['id' => 'status', 'class' => 'form-control w-auto']) }}
                        </div>
                        <div class="col-lg-4">
                            <label for="name">トラッキングID</label>
                            <input type="text" name="tracking_id" id="tracking_id" class="form-control" value="{{ request('tracking_id') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-2">
                            <label for="sei">姓</label>
                            <input type="text" name="sei" id="sei" class="form-control" value="{{ request('sei') }}">
                        </div>
                        <div class="col-lg-2">
                            <label for="mei">名</label>
                            <input type="text" name="mei" id="mei" class="form-control" value="{{ request('mei') }}">
                        </div>
                        <div class="col-lg-4">
                            <label for="tel">電話番号</label>
                            <input type="text" name="tel" id="tel" class="form-control" value="{{ request('tel') }}">
                        </div>
                        <div class="col-lg-4">
                            <label for="memo">メモ</label>
                            <input type="text" name="memo" id="memo" class="form-control" value="{{ request('memo') }}">
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center bg-white">
                    <button type="button" class="search btn btn-primary" data-action="{{ route('backend.form_a.applications.index') }}"><i class="fas fa-search mr-2"></i>検索する</button>
                </div>
            </div>
        </form>
        <div class="card">
            <div class="card-header border-bottom-0">
                <h3 class="card-title">検索結果：{{ number_format($applications->total()) }} 件</h3>
                <div class="card-tools">
                    <a href="#" class="csv ml-3" data-action="{{ route('backend.form_a.applications.download_csv') }}"><i class="fas fa-file-csv mr-2"></i>検索結果をCSVダウンロード</a>
                </div>
            </div>
            <div class="card-body p-0">
                <table id="workbook_list" class="table table-striped">
                    <thead class="bg-gray">
                        <tr>
                            <th>#</th>
                            <th>申込日時</th>
                            <th>ステータス</th>
                            <th>トラッキングID</th>
                            <th>氏名</th>
                            <th>電話番号</th>
                            <th>メモ</th>
                            <th style="width: 95px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($applications as $application)
                        <tr>
                            <td>{{ $application->id }}</td>
                            <td>{{ $application->created_at }}</td>
                            <td>{{ $application->status_name }}</td>
                            <td>{{ $application->tracking_id }}</td>
                            <td>
                                {{ $application->sei }}
                                {{ $application->mei }}
                            </td>
                            <td>{{ $application->tel }}</td>
                            <td>{!! nl2br($application->memo) !!}</td>
                            <td>
                                <a href="{{ route('backend.form_a.applications.edit', $application->id) }}" class="ml-3"><i class="fas fa-angle-double-right mr-2"></i>詳細</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $applications->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('assets/bootstrap-datepicker-1.9.0-dist/css/bootstrap-datepicker.min.css') }}">
<style>
</style>
@stop

@section('js')
<script src="{{ asset('assets/bootstrap-datepicker-1.9.0-dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/bootstrap-datepicker-1.9.0-dist/locales/bootstrap-datepicker.ja.min.js') }}"></script>
<script>
    $('.date').datepicker({
        format: "yyyy-mm-dd",
        language: "ja",
        orientation: "bottom",
        autoclose: true
    });
    $('.search, .csv').click(function(event) {
        $('.application_search').attr('action', $(this).data('action'));
        $('.application_search').submit();
    });
</script>
@stop