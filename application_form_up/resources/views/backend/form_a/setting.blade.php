@extends('adminlte::page')

@section('title', 'FLA管理画面 > フォーム A > 項目設定')

@section('content_header')
    <h1 class="d-inline">フォーム A</h1>
    <a href="{{ route('form_a.form') }}" target="_blank"><i class="fas fa-link ml-3 mr-2"></i>{{ route('form_a.form') }}</a>
@stop

@section('content')
<div class="row">
    <div class="col">
        <form action="{{ route('backend.form_a.settings.basic') }}" id="basic" class="form-horizontal" method="POST">
            @method('PUT')
            <div class="card">
                <div class="card-header bg-navy">
                    <div class="card-title">基本設定</div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">公開 / 非公開</label>
                        <div class="col-sm-10">
                            {{ Form::select('public', array_flip(config('common.public')), $setting->public, ['class' => 'form-control w-auto']) }}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-primary save_basic"><i class="fas fa-save mr-2"></i>保存する</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col">
        <form action="{{ route('backend.form_a.settings.item') }}" id="item" class="form-horizontal" method="POST">
            @method('PUT')
            <div class="card">
                <div class="card-header bg-navy">
                    <div class="card-title">項目設定</div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">検定試験</label>
                        <div class="col-sm-10">
                            <table id="exam_list" class="table table-striped mb-3">
                                <thead class="bg-gray">
                                    <tr>
                                        <th class="primary_key">#</th>
                                        <th class="enabled">有効</th>
                                        <th>検定試験名</th>
                                        <th>受験料（税込）</th>
                                        <th class="operation"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (is_array($setting->exam_list))
                                    @foreach ($setting->exam_list as $exam)
                                    <tr id="exam{{ $exam->id }}">
                                        <td>{{ $exam->id }}</td>
                                        <td><input type="checkbox" name="exam_enabled[]" value="{{ $exam->id }}"@if ($exam->enabled) checked @endif></td>
                                        <td>{{ $exam->name }}</td>
                                        <td>{{ number_format($exam->price) }}</td>
                                        <td class="text-right">
                                            <input type="hidden" name="exam_delete[]" value="">
                                            <input type="hidden" name="exam_id[]" value="{{ $exam->id }}">
                                            <input type="hidden" name="exam_name[]" value="{{ $exam->name }}">
                                            <input type="hidden" name="exam_price[]" value="{{ $exam->price }}">
                                            <a href="#examModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
                                            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                            <div class="text-right">
                                <a href="#examModal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-2"></i>追加</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">受験会場</label>
                        <div class="col-sm-10">
                            <table id="exam_venue_list" class="table table-striped mb-3">
                                <thead class="bg-gray">
                                    <tr>
                                        <th class="primary_key">#</th>
                                        <th class="enabled">有効</th>
                                        <th>受験会場名</th>
                                        <th class="operation"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (is_array($setting->exam_venue_list))
                                    @foreach ($setting->exam_venue_list as $exam_venue)
                                    <tr id="exam_venue{{ $exam_venue->id }}">
                                        <td>{{ $exam_venue->id }}</td>
                                        <td><input type="checkbox" name="exam_venue_enabled[]" value="{{ $exam_venue->id }}"@if ($exam_venue->enabled) checked @endif></td>
                                        <td>{{ $exam_venue->name }}</td>
                                        <td class="text-right">
                                            <input type="hidden" name="exam_venue_delete[]" value="">
                                            <input type="hidden" name="exam_venue_id[]" value="{{ $exam_venue->id }}">
                                            <input type="hidden" name="exam_venue_name[]" value="{{ $exam_venue->name }}">
                                            <a href="#examVenueModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
                                            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                            <div class="text-right">
                                <a href="#examVenueModal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-2"></i>追加</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">通学コース</label>
                        <div class="col-sm-10">
                            <div class="form-inline">
                                <div class="form-group">
                                    <input type="checkbox" name="normal_enabled" id="normal_enabled" value="1"@if ($setting->normal_enabled) checked @endif>
                                    <label for="normal_enabled" class="font-weight-normal ml-2 mr-5">受講申込を受け付ける</label>
                                </div>
                                <div class="form-group">
                                    <label for="normal_price" class="font-weight-normal">受講料（税込）</label>
                                    <input type="number" name="normal_price" id="normal_price" class="form-control ml-2 mr-2" value="{{ $setting->normal_price }}">
                                    <span class="text-muted">半角数字のみ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">通学コース　受講会場</label>
                        <div class="col-sm-10">
                            <table id="normal_venue_list" class="table table-striped mb-3">
                                <thead class="bg-gray">
                                    <tr>
                                        <th class="primary_key">#</th>
                                        <th class="enabled">有効</th>
                                        <th>都市</th>
                                        <th>受講会場名</th>
                                        <th>日程</th>
                                        <th class="operation"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (is_array($setting->normal_venue_list))
                                    @foreach ($setting->normal_venue_list as $normal_venue)
                                    <tr id="normal_venue{{ $normal_venue->id }}">
                                        <td>{{ $normal_venue->id }}</td>
                                        <td><input type="checkbox" name="normal_venue_enabled[]" value="{{ $normal_venue->id }}"@if ($normal_venue->enabled) checked @endif></td>
                                        <td>{{ $normal_venue->city_name }}</td>
                                        <td>{{ $normal_venue->name }}</td>
                                        <td>{{ $normal_venue->schedule }}</td>
                                        <td class="text-right">
                                            <input type="hidden" name="normal_venue_delete[]" value="">
                                            <input type="hidden" name="normal_venue_id[]" value="{{ $normal_venue->id }}">
                                            <input type="hidden" name="normal_venue_city[]" value="{{ $normal_venue->city }}">
                                            <input type="hidden" name="normal_venue_name[]" value="{{ $normal_venue->name }}">
                                            <input type="hidden" name="normal_venue_schedule[]" value="{{ $normal_venue->schedule }}">
                                            <a href="#normalVenueModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
                                            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                            <div class="text-right">
                                <a href="#normalVenueModal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-2"></i>追加</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">速習コース</label>
                        <div class="col-sm-10">
                            <div class="form-inline mt-1">
                                <div class="form-group">
                                    <input type="checkbox" name="fast_enabled" id="fast_enabled" value="1"@if ($setting->fast_enabled) checked @endif>
                                    <label for="fast_enabled" class="font-weight-normal ml-2">受講申込を受け付ける</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">速習コース　講座</label>
                        <div class="col-sm-10">
                            <table id="fast_course_list" class="table table-striped mb-3">
                                <thead class="bg-gray">
                                    <tr>
                                        <th class="primary_key">#</th>
                                        <th class="enabled">有効</th>
                                        <th>講座名</th>
                                        <th>受講料（税込）</th>
                                        <th>受講日数</th>
                                        <th class="operation"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (is_array($setting->fast_course_list))
                                    @foreach ($setting->fast_course_list as $fast_course)
                                    <tr id="fast_course{{ $fast_course->id }}">
                                        <td>{{ $fast_course->id }}</td>
                                        <td><input type="checkbox" name="fast_course_enabled[]" value="{{ $fast_course->id }}"@if ($fast_course->enabled) checked @endif></td>
                                        <td>{{ $fast_course->name }}</td>
                                        <td>{{ number_format($fast_course->price) }}</td>
                                        <td>{{ $fast_course->days ?? '' }}</td>
                                        <td class="text-right">
                                            <input type="hidden" name="fast_course_delete[]" value="">
                                            <input type="hidden" name="fast_course_id[]" value="{{ $fast_course->id }}">
                                            <input type="hidden" name="fast_course_name[]" value="{{ $fast_course->name }}">
                                            <input type="hidden" name="fast_course_price[]" value="{{ $fast_course->price }}">
                                            <input type="hidden" name="fast_course_days[]" value="{{ $fast_course->days ?? '' }}">
                                            <a href="#fastCourseModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
                                            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                            <div class="text-right">
                                <a href="#fastCourseModal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-2"></i>追加</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">速習コース　受講会場</label>
                        <div class="col-sm-10">
                            <table id="fast_venue_list" class="table table-striped mb-3">
                                <thead class="bg-gray">
                                    <tr>
                                        <th class="primary_key">#</th>
                                        <th class="enabled">有効</th>
                                        <th>都市</th>
                                        <th>受講会場名</th>
                                        <th>日程</th>
                                        <th class="operation"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (is_array($setting->fast_venue_list))
                                    @foreach ($setting->fast_venue_list as $fast_venue)
                                    <tr id="fast_venue{{ $fast_venue->id }}">
                                        <td>{{ $fast_venue->id }}</td>
                                        <td><input type="checkbox" name="fast_venue_enabled[]" value="{{ $fast_venue->id }}"@if ($fast_venue->enabled) checked @endif></td>
                                        <td>{{ $fast_venue->city_name }}</td>
                                        <td>{{ $fast_venue->name }}</td>
                                        <td>{{ $fast_venue->schedule }}</td>
                                        <td class="text-right">
                                            <input type="hidden" name="fast_venue_delete[]" value="">
                                            <input type="hidden" name="fast_venue_id[]" value="{{ $fast_venue->id }}">
                                            <input type="hidden" name="fast_venue_city[]" value="{{ $fast_venue->city }}">
                                            <input type="hidden" name="fast_venue_name[]" value="{{ $fast_venue->name }}">
                                            <input type="hidden" name="fast_venue_schedule[]" value="{{ $fast_venue->schedule }}">
                                            <a href="#fastVenueModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
                                            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                            <div class="text-right">
                                <a href="#fastVenueModal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-2"></i>追加</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">科目別 過去問題集</label>
                        <div class="col-sm-10">
                            <table id="workbook_list" class="table table-striped mb-3">
                                <thead class="bg-gray">
                                    <tr>
                                        <th class="primary_key">#</th>
                                        <th class="enabled">有効</th>
                                        <th>問題集名</th>
                                        <th>価格（税込）</th>
                                        <th class="operation"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (is_array($setting->workbook_list))
                                    @foreach ($setting->workbook_list as $workbook)
                                    <tr id="workbook{{ $workbook->id }}">
                                        <td>{{ $workbook->id }}</td>
                                        <td><input type="checkbox" name="workbook_enabled[]" value="{{ $workbook->id }}"@if ($workbook->enabled) checked @endif></td>
                                        <td>{{ $workbook->name }}</td>
                                        <td>{{ number_format($workbook->price) }}</td>
                                        <td class="text-right">
                                            <input type="hidden" name="workbook_delete[]" value="">
                                            <input type="hidden" name="workbook_id[]" value="{{ $workbook->id }}">
                                            <input type="hidden" name="workbook_name[]" value="{{ $workbook->name }}">
                                            <input type="hidden" name="workbook_price[]" value="{{ $workbook->price }}">
                                            <a href="#workbookModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
                                            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                            <div class="text-right">
                                <a href="#workbookModal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-2"></i>追加</a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-primary save_item"><i class="fas fa-save mr-2"></i>保存する</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 検定試験モーダル -->
<form class="modal" id="examModal" method="POST">
    <input type="hidden" name="id" value="">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h5 class="modal-title">検定試験</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible" style="display: none;">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
                    <h6><i class="icon fas fa-exclamation-triangle"></i> エラー</h6>
                    <div class="error">全ての項目を正しく入力してください。</div>
                </div>
                <div class="form-group row">
                    <label for="exam_name" class="col-sm-4 col-form-label">検定試験名<span class="text-danger pl-1">※</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="exam_name" id="exam_name" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="exam_price" class="col-sm-4 col-form-label">受験料（税込）<span class="text-danger pl-1">※</span></label>
                    <div class="col-sm-8">
                        <input type="number" name="exam_price" id="exam_price" class="form-control w-auto">
                        <span class="text-muted">半角数字のみ</span>
                        <!-- <span class="error text-danger">半角数字のみを入力してください。</span> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                <button type="button" id="set_exam" class="btn btn-primary"></button>
            </div>
        </div>
    </div>
</form>
<template id="exam_row">
    <tr id="">
        <td></td>
        <td><input type="checkbox" name="exam_enabled[]" value=""></td>
        <td></td>
        <td></td>
        <td class="text-right">
            <input type="hidden" name="exam_delete[]" value="">
            <input type="hidden" name="exam_id[]" value="">
            <input type="hidden" name="exam_name[]" value="">
            <input type="hidden" name="exam_price[]" value="">
            <a href="#examModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
        </td>
    </tr>
</template>

<!-- 受験会場モーダル -->
<form class="modal" id="examVenueModal" method="POST">
    <input type="hidden" name="id" value="">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h5 class="modal-title">受験会場</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible" style="display: none;">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
                    <h6><i class="icon fas fa-exclamation-triangle"></i> エラー</h6>
                    <div class="error">全ての項目を正しく入力してください。</div>
                </div>
                <div class="form-group row">
                    <label for="exam_venue_name" class="col-sm-3 col-form-label">受験会場名<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="exam_venue_name" id="exam_venue_name" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                <button type="button" id="set_exam_venue" class="btn btn-primary"></button>
            </div>
        </div>
    </div>
</form>
<template id="exam_venue_row">
    <tr id="">
        <td></td>
        <td><input type="checkbox" name="exam_venue_enabled[]" value=""></td>
        <td></td>
        <td class="text-right">
            <input type="hidden" name="exam_venue_delete[]" value="">
            <input type="hidden" name="exam_venue_id[]" value="">
            <input type="hidden" name="exam_venue_name[]" value="">
            <a href="#examVenueModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
        </td>
    </tr>
</template>

<!-- 通学コース　受講会場モーダル -->
<form class="modal" id="normalVenueModal" method="POST">
    <input type="hidden" name="id" value="">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h5 class="modal-title">通学コース　受講会場</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible" style="display: none;">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
                    <h6><i class="icon fas fa-exclamation-triangle"></i> エラー</h6>
                    <div class="error">全ての項目を正しく入力してください。</div>
                </div>
                <div class="form-group row">
                    <label for="normal_venue_city" class="col-sm-3 col-form-label">都市<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-9">
                        {{ Form::select('normal_venue_city', ['' => ''] + array_flip(config('common.city')), '', ['id' => 'normal_venue_city', 'class' => 'form-control w-auto']) }}
                    </div>
                </div>
                <div class="form-group row">
                    <label for="normal_venue_name" class="col-sm-3 col-form-label">受講会場名<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="normal_venue_name" id="normal_venue_name" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="normal_venue_schedule" class="col-sm-3 col-form-label">日程<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="normal_venue_schedule" id="normal_venue_schedule" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                <button type="button" id="set_normal_venue" class="btn btn-primary"></button>
            </div>
        </div>
    </div>
</form>
<template id="normal_venue_row">
    <tr id="">
        <td></td>
        <td><input type="checkbox" name="normal_venue_enabled[]" value=""></td>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right">
            <input type="hidden" name="normal_venue_delete[]" value="">
            <input type="hidden" name="normal_venue_id[]" value="">
            <input type="hidden" name="normal_venue_city[]" value="">
            <input type="hidden" name="normal_venue_name[]" value="">
            <input type="hidden" name="normal_venue_schedule[]" value="">
            <a href="#normalVenueModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
        </td>
    </tr>
</template>

<!-- 速習コース　講座モーダル -->
<form class="modal" id="fastCourseModal" method="POST">
    <input type="hidden" name="id" value="">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h5 class="modal-title">速習コース　講座</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible" style="display: none;">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
                    <h6><i class="icon fas fa-exclamation-triangle"></i> エラー</h6>
                    <div class="error">全ての項目を正しく入力してください。</div>
                </div>
                <div class="form-group row">
                    <label for="fast_course_name" class="col-sm-4 col-form-label">講座名<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="fast_course_name" id="fast_course_name" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="fast_course_price" class="col-sm-4 col-form-label">受講料（税込）<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-8">
                        <input type="number" name="fast_course_price" id="fast_course_price" class="form-control w-auto">
                        <span class="text-muted">半角数字のみ</span>
                        <!-- <span class="error text-danger">半角数字のみを入力してください。</span> -->
                    </div>
                </div>
                <div class="form-group row">
                    <label for="fast_course_days" class="col-sm-4 col-form-label">受講日数<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-8">
                        <input type="number" name="fast_course_days" id="fast_course_days" class="form-control w-auto">
                        <span class="text-muted">半角数字のみ</span>
                        <!-- <span class="error text-danger">半角数字のみを入力してください。</span> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                <button type="button" id="set_fast_course" class="btn btn-primary"></button>
            </div>
        </div>
    </div>
</form>
<template id="fast_course_row">
    <tr id="">
        <td></td>
        <td><input type="checkbox" name="fast_course_enabled[]" value=""></td>
        <td></td>
        <td></td>
        <td class="text-right">
            <input type="hidden" name="fast_course_delete[]" value="">
            <input type="hidden" name="fast_course_id[]" value="">
            <input type="hidden" name="fast_course_name[]" value="">
            <input type="hidden" name="fast_course_price[]" value="">
            <input type="hidden" name="fast_course_days[]" value="">
            <a href="#fastCourseModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
        </td>
    </tr>
</template>

<!-- 速習コース　受講会場モーダル -->
<form class="modal" id="fastVenueModal" method="POST">
    <input type="hidden" name="id" value="">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h5 class="modal-title">速習コース　受講会場</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible" style="display: none;">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
                    <h6><i class="icon fas fa-exclamation-triangle"></i> エラー</h6>
                    <div class="error">全ての項目を正しく入力してください。</div>
                </div>
                <div class="form-group row">
                    <label for="fast_venue_city" class="col-sm-3 col-form-label">都市<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-9">
                        {{ Form::select('fast_venue_city', ['' => ''] + array_flip(config('common.city')), '', ['id' => 'fast_venue_city', 'class' => 'form-control w-auto']) }}
                    </div>
                </div>
                <div class="form-group row">
                    <label for="fast_venue_name" class="col-sm-3 col-form-label">受講会場名<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="fast_venue_name" id="fast_venue_name" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="fast_venue_schedule" class="col-sm-3 col-form-label">日程<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="fast_venue_schedule" id="fast_venue_schedule" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                <button type="button" id="set_fast_venue" class="btn btn-primary"></button>
            </div>
        </div>
    </div>
</form>
<template id="fast_venue_row">
    <tr id="">
        <td></td>
        <td><input type="checkbox" name="fast_venue_enabled[]" value=""></td>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right">
            <input type="hidden" name="fast_venue_delete[]" value="">
            <input type="hidden" name="fast_venue_id[]" value="">
            <input type="hidden" name="fast_venue_city[]" value="">
            <input type="hidden" name="fast_venue_name[]" value="">
            <input type="hidden" name="fast_venue_schedule[]" value="">
            <a href="#fastVenueModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
        </td>
    </tr>
</template>

<!-- 科目別 過去問題集モーダル -->
<form class="modal" id="workbookModal" method="POST">
    <input type="hidden" name="id" value="">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h5 class="modal-title">科目別 過去問題集</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible" style="display: none;">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
                    <h6><i class="icon fas fa-exclamation-triangle"></i> エラー</h6>
                    <div class="error">全ての項目を正しく入力してください。</div>
                </div>
                <div class="form-group row">
                    <label for="workbook_name" class="col-sm-3 col-form-label">問題集名<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="workbook_name" id="workbook_name" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="workbook_price" class="col-sm-3 col-form-label">価格（税込）<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-9">
                        <input type="number" name="workbook_price" id="workbook_price" class="form-control w-auto">
                        <span class="text-muted">半角数字のみ</span>
                        <!-- <span class="error text-danger">半角数字のみを入力してください。</span> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                <button type="button" id="set_workbook" class="btn btn-primary"></button>
            </div>
        </div>
    </div>
</form>
<template id="workbook_row">
    <tr id="">
        <td></td>
        <td><input type="checkbox" name="workbook_enabled[]" value=""></td>
        <td></td>
        <td></td>
        <td class="text-right">
            <input type="hidden" name="workbook_delete[]" value="">
            <input type="hidden" name="workbook_id[]" value="">
            <input type="hidden" name="workbook_name[]" value="">
            <input type="hidden" name="workbook_price[]" value="">
            <a href="#workbookModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
        </td>
    </tr>
</template>
@stop

@section('css')
<style>
</style>
@stop

@section('js')
<script>
function formatNumber(value) {
    return String(value).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
}
function getNewId(target) {
	let list = [0];
	$(target + ' tbody tr').each(function() {
		let id = $(this).attr('id').match(/(\d+)$/)[1];
		list.push(id);
	});

    return Math.max(...list) + 1;
}
function validateModal(target) {
    let values = [];

    let empty_flag = false;
    $(target + ' .form-control').each(function() {
        let value = $(this).val();
        console.log(value);
        if (value == '') {
            empty_flag = true;
        } else {

        }
        /*
        return true;
        values.push(value);

        if ($(this).attr('type') == 'number') {
            if (value.match(/[^0-9]/)) {
                alert('error');
            }
        }
        */
	});

    if (empty_flag) {
        $(target + ' div.error').text('全ての項目を正しく入力して下さい。');
        $(target + ' .alert-danger').show();
        return false;
    }

    $(target + ' .alert-danger').hide();

    return true; 

}
$(document).on('click', 'a.delete', function(e) {
    const tr = $(this).parent().parent();
    const target_delete = tr.find(':hidden[name$="delete[]"]');
    if (target_delete.val() == '1') {
        tr.removeClass('bg-danger');
        target_delete.val('');
    } else {
        tr.addClass('bg-danger');
        target_delete.val('1');
    }
    return false;
});

// 検定試験
$('#examModal').on('show.bs.modal', function (event) {
    let id = $(event.relatedTarget).parent().parent().attr('id');
    if (id) {
        id = id.match(/(\d+)$/)[1];
        const exam_name = $('#exam' + id + ' :hidden[name="exam_name[]"]').val();
        const exam_price = $('#exam' + id + ' :hidden[name="exam_price[]"]').val();
        $('#examModal input:hidden[name=id]').val(id);
        $('#exam_name').val(exam_name);
        $('#exam_price').val(exam_price);
        $('#set_exam').text('編集内容を反映する');
    } else {
        $('#examModal input:hidden[name=id]').val('');
        $('#exam_name').val('');
        $('#exam_price').val('');
        $('#set_exam').text('追加する');
    }
});
$('button#set_exam').click(function(e) {
    if (!validateModal('#examModal')) {
        return false;
    }

    let id = $('#examModal input:hidden[name=id]').val();
    const exam_name = $('#exam_name').val();
    const exam_price = $('#exam_price').val();

    if (id == '') {
        id = getNewId('#exam_list');
        let row = $($('#exam_row').html());
        row.attr('id', 'exam' + id);
        row.find('td:eq(0)').text(id);
        row.find('td:eq(2)').text(exam_name);
        row.find('td:eq(3)').text(formatNumber(exam_price));
        row.find(':hidden[name="exam_id[]"]').val(id);
        row.find(':checkbox[name="exam_enabled[]"]').val(id);
        row.find(':checkbox[name="exam_enabled[]"]').prop('checked', true);
        row.find(':hidden[name="exam_name[]"]').val(exam_name);
        row.find(':hidden[name="exam_price[]"]').val(exam_price);
        $('#exam_list tbody').append(row);
    } else {
        let row = $('tr#exam' + id);
        row.find('td:eq(2)').text(exam_name);
        row.find('td:eq(3)').text(formatNumber(exam_price));
        row.find(':hidden[name="exam_name[]"]').val(exam_name);
        row.find(':hidden[name="exam_price[]"]').val(exam_price);
    }

    $('#examModal').modal('hide');

    return true;
});

// 受験会場
$('#examVenueModal').on('show.bs.modal', function (event) {
    let id = $(event.relatedTarget).parent().parent().attr('id');
    if (id) {
        id = id.match(/(\d+)$/)[1];
        const exam_venue_name = $('#exam_venue' + id + ' :hidden[name="exam_venue_name[]"]').val();
        $('#examVenueModal input:hidden[name=id]').val(id);
        $('#exam_venue_name').val(exam_venue_name);
        $('#set_exam_venue').text('編集内容を反映する');
    } else {
        $('#examVenueModal input:hidden[name=id]').val('');
        $('#exam_venue_name').val('');
        $('#set_exam_venue').text('追加する');
    }
});
$('button#set_exam_venue').click(function(e) {
    if (!validateModal('#examVenueModal')) {
        return false;
    }

    let id = $('#examVenueModal input:hidden[name=id]').val();
    const exam_venue_name = $('#exam_venue_name').val();

    if (id == '') {
        id = getNewId('#exam_venue_list');
        let row = $($('#exam_venue_row').html());
        row.attr('id', 'exam_venue' + id);
        row.find('td:eq(0)').text(id);
        row.find('td:eq(2)').text(exam_venue_name);
        row.find(':hidden[name="exam_venue_id[]"]').val(id);
        row.find(':checkbox[name="exam_venue_enabled[]"]').val(id);
        row.find(':checkbox[name="exam_venue_enabled[]"]').prop('checked', true);
        row.find(':hidden[name="exam_venue_name[]"]').val(exam_venue_name);
        $('#exam_venue_list tbody').append(row);
    } else {
        let row = $('tr#exam_venue' + id);
        row.find('td:eq(2)').text(exam_venue_name);
        row.find(':hidden[name="exam_venue_name[]"]').val(exam_venue_name);
    }

    $('#examVenueModal').modal('hide');

    return true;
});

// 通学コース　受講会場
$('#normalVenueModal').on('show.bs.modal', function (event) {
    let id = $(event.relatedTarget).parent().parent().attr('id');
    if (id) {
        id = id.match(/(\d+)$/)[1];
        const normal_venue_city = $('#normal_venue' + id + ' :hidden[name="normal_venue_city[]"]').val();
        const normal_venue_name = $('#normal_venue' + id + ' :hidden[name="normal_venue_name[]"]').val();
        const normal_venue_schedule = $('#normal_venue' + id + ' :hidden[name="normal_venue_schedule[]"]').val();
        $('#normalVenueModal input:hidden[name=id]').val(id);
        $('#normal_venue_city').val(normal_venue_city);
        $('#normal_venue_name').val(normal_venue_name);
        $('#normal_venue_schedule').val(normal_venue_schedule);
        $('#set_normal_venue').text('編集内容を反映する');
    } else {
        $('#normalVenueModal input:hidden[name=id]').val('');
        $('#normal_venue_city').val('');
        $('#normal_venue_name').val('');
        $('#normal_venue_schedule').val('');
        $('#set_normal_venue').text('追加する');
    }
});
$('button#set_normal_venue').click(function(e) {
    if (!validateModal('#normalVenueModal')) {
        return false;
    }

    let id = $('#normalVenueModal input:hidden[name=id]').val();
    const normal_venue_city      = $('#normal_venue_city').val();
    const normal_venue_city_name = $('#normal_venue_city option:selected').text();
    const normal_venue_name      = $('#normal_venue_name').val();
    const normal_venue_schedule  = $('#normal_venue_schedule').val();

    if (id == '') {
        id = getNewId('#normal_venue_list');
        let row = $($('#normal_venue_row').html());
        row.attr('id', 'normal_venue' + id);
        row.find('td:eq(0)').text(id);
        row.find('td:eq(2)').text(normal_venue_city_name);
        row.find('td:eq(3)').text(normal_venue_name);
        row.find('td:eq(4)').text(normal_venue_schedule);
        row.find(':hidden[name="normal_venue_id[]"]').val(id);
        row.find(':checkbox[name="normal_venue_enabled[]"]').val(id);
        row.find(':checkbox[name="normal_venue_enabled[]"]').prop('checked', true);
        row.find(':hidden[name="normal_venue_city[]"]').val(normal_venue_city);
        row.find(':hidden[name="normal_venue_name[]"]').val(normal_venue_name);
        row.find(':hidden[name="normal_venue_schedule[]"]').val(normal_venue_schedule);
        $('#normal_venue_list tbody').append(row);
    } else {
        let row = $('tr#normal_venue' + id);
        row.find('td:eq(2)').text(normal_venue_city_name);
        row.find('td:eq(3)').text(normal_venue_name);
        row.find('td:eq(4)').text(normal_venue_schedule);
        row.find(':hidden[name="normal_venue_city[]"]').val(normal_venue_city);
        row.find(':hidden[name="normal_venue_name[]"]').val(normal_venue_name);
        row.find(':hidden[name="normal_venue_schedule[]"]').val(normal_venue_schedule);
    }

    $('#normalVenueModal').modal('hide');

    return true;
});

// 速習コース　講座
$('#fastCourseModal').on('show.bs.modal', function (event) {
    let id = $(event.relatedTarget).parent().parent().attr('id');
    if (id) {
        id = id.match(/(\d+)$/)[1];
        const fast_course_name = $('#fast_course' + id + ' :hidden[name="fast_course_name[]"]').val();
        const fast_course_price = $('#fast_course' + id + ' :hidden[name="fast_course_price[]"]').val();
        const fast_course_days = $('#fast_course' + id + ' :hidden[name="fast_course_days[]"]').val();
        $('#fastCourseModal input:hidden[name=id]').val(id);
        $('#fast_course_name').val(fast_course_name);
        $('#fast_course_price').val(fast_course_price);
        $('#fast_course_days').val(fast_course_days);
        $('#set_fast_course').text('編集内容を反映する');
    } else {
        $('#fastCourseModal input:hidden[name=id]').val('');
        $('#fast_course_name').val('');
        $('#fast_course_price').val('');
        $('#fast_course_days').val('');
        $('#set_fast_course').text('追加する');
    }
});
$('button#set_fast_course').click(function(e) {
    if (!validateModal('#fastCourseModal')) {
        return false;
    }

    let id = $('#fastCourseModal input:hidden[name=id]').val();
    const fast_course_name = $('#fast_course_name').val();
    const fast_course_price = $('#fast_course_price').val();
    const fast_course_days = $('#fast_course_days').val();

    if (id == '') {
        id = getNewId('#fast_course_list');
        let row = $($('#fast_course_row').html());
        row.attr('id', 'fast_course' + id);
        row.find('td:eq(0)').text(id);
        row.find('td:eq(2)').text(fast_course_name);
        row.find('td:eq(3)').text(formatNumber(fast_course_price));
        row.find('td:eq(4)').text(fast_course_days);
        row.find(':hidden[name="fast_course_id[]"]').val(id);
        row.find(':checkbox[name="fast_course_enabled[]"]').val(id);
        row.find(':checkbox[name="fast_course_enabled[]"]').prop('checked', true);
        row.find(':hidden[name="fast_course_name[]"]').val(fast_course_name);
        row.find(':hidden[name="fast_course_price[]"]').val(fast_course_price);
        row.find(':hidden[name="fast_course_days[]"]').val(fast_course_days);
        $('#fast_course_list tbody').append(row);
    } else {
        let row = $('tr#fast_course' + id);
        row.find('td:eq(2)').text(fast_course_name);
        row.find('td:eq(3)').text(formatNumber(fast_course_price));
        row.find('td:eq(4)').text(fast_course_days);
        row.find(':hidden[name="fast_course_name[]"]').val(fast_course_name);
        row.find(':hidden[name="fast_course_price[]"]').val(fast_course_price);
        row.find(':hidden[name="fast_course_days[]"]').val(fast_course_days);
    }

    $('#fastCourseModal').modal('hide');

    return true;
});

// 速習コース　受講会場
$('#fastVenueModal').on('show.bs.modal', function (event) {
    let id = $(event.relatedTarget).parent().parent().attr('id');
    if (id) {
        id = id.match(/(\d+)$/)[1];
        const fast_venue_city = $('#fast_venue' + id + ' :hidden[name="fast_venue_city[]"]').val();
        const fast_venue_name = $('#fast_venue' + id + ' :hidden[name="fast_venue_name[]"]').val();
        const fast_venue_schedule = $('#fast_venue' + id + ' :hidden[name="fast_venue_schedule[]"]').val();
        $('#fastVenueModal input:hidden[name=id]').val(id);
        $('#fast_venue_city').val(fast_venue_city);
        $('#fast_venue_name').val(fast_venue_name);
        $('#fast_venue_schedule').val(fast_venue_schedule);
        $('#set_fast_venue').text('編集内容を反映する');
    } else {
        $('#fastVenueModal input:hidden[name=id]').val('');
        $('#fast_venue_city').val('');
        $('#fast_venue_name').val('');
        $('#fast_venue_schedule').val('');
        $('#set_fast_venue').text('追加する');
    }
});
$('button#set_fast_venue').click(function(e) {
    if (!validateModal('#fastVenueModal')) {
        return false;
    }

    let id = $('#fastVenueModal input:hidden[name=id]').val();
    const fast_venue_city      = $('#fast_venue_city').val();
    const fast_venue_city_name = $('#fast_venue_city option:selected').text();
    const fast_venue_name      = $('#fast_venue_name').val();
    const fast_venue_schedule  = $('#fast_venue_schedule').val();

    if (id == '') {
        id = getNewId('#fast_venue_list');
        let row = $($('#fast_venue_row').html());
        row.attr('id', 'fast_venue' + id);
        row.find('td:eq(0)').text(id);
        row.find('td:eq(2)').text(fast_venue_city_name);
        row.find('td:eq(3)').text(fast_venue_name);
        row.find('td:eq(4)').text(fast_venue_schedule);
        row.find(':hidden[name="fast_venue_id[]"]').val(id);
        row.find(':checkbox[name="fast_venue_enabled[]"]').val(id);
        row.find(':checkbox[name="fast_venue_enabled[]"]').prop('checked', true);
        row.find(':hidden[name="fast_venue_city[]"]').val(fast_venue_city);
        row.find(':hidden[name="fast_venue_name[]"]').val(fast_venue_name);
        row.find(':hidden[name="fast_venue_schedule[]"]').val(fast_venue_schedule);
        $('#fast_venue_list tbody').append(row);
    } else {
        let row = $('tr#fast_venue' + id);
        row.find('td:eq(2)').text(fast_venue_city_name);
        row.find('td:eq(3)').text(fast_venue_name);
        row.find('td:eq(4)').text(fast_venue_schedule);
        row.find(':hidden[name="fast_venue_city[]"]').val(fast_venue_city);
        row.find(':hidden[name="fast_venue_name[]"]').val(fast_venue_name);
        row.find(':hidden[name="fast_venue_schedule[]"]').val(fast_venue_schedule);
    }

    $('#fastVenueModal').modal('hide');

    return true;
});

// 科目別 過去問題集
$('#workbookModal').on('show.bs.modal', function (event) {
    let id = $(event.relatedTarget).parent().parent().attr('id');
    if (id) {
        id = id.match(/(\d+)$/)[1];
        const workbook_name = $('#workbook' + id + ' :hidden[name="workbook_name[]"]').val();
        const workbook_price = $('#workbook' + id + ' :hidden[name="workbook_price[]"]').val();
        $('#workbookModal input:hidden[name=id]').val(id);
        $('#workbook_name').val(workbook_name);
        $('#workbook_price').val(workbook_price);
        $('#set_workbook').text('編集内容を反映する');
    } else {
        $('#workbookModal input:hidden[name=id]').val('');
        $('#workbook_name').val('');
        $('#workbook_price').val('');
        $('#set_workbook').text('追加する');
    }
});
$('button#set_workbook').click(function(e) {
    if (!validateModal('#workbookModal')) {
        return false;
    }

    let id = $('#workbookModal input:hidden[name=id]').val();
    const workbook_name = $('#workbook_name').val();
    const workbook_price = $('#workbook_price').val();

    if (id == '') {
        id = getNewId('#workbook_list');
        let row = $($('#workbook_row').html());
        row.attr('id', 'workbook' + id);
        row.find('td:eq(0)').text(id);
        row.find('td:eq(2)').text(workbook_name);
        row.find('td:eq(3)').text(formatNumber(workbook_price));
        row.find(':hidden[name="workbook_id[]"]').val(id);
        row.find(':checkbox[name="workbook_enabled[]"]').val(id);
        row.find(':checkbox[name="workbook_enabled[]"]').prop('checked', true);
        row.find(':hidden[name="workbook_name[]"]').val(workbook_name);
        row.find(':hidden[name="workbook_price[]"]').val(workbook_price);
        $('#workbook_list tbody').append(row);
    } else {
        let row = $('tr#workbook' + id);
        row.find('td:eq(2)').text(workbook_name);
        row.find('td:eq(3)').text(formatNumber(workbook_price));
        row.find(':hidden[name="workbook_name[]"]').val(workbook_name);
        row.find(':hidden[name="workbook_price[]"]').val(workbook_price);
    }

    $('#workbookModal').modal('hide');

    return true;
});



// 基本設定
$('button.save_basic').click(function(e) {
    const url = $('form#basic').attr('action');
    const fd = new FormData($('form#basic').get(0));

    $.ajax({
        type: 'POST',
        url: url,
        processData: false,
        contentType: false,
        data: fd,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
    }).done(function(data, textStatus, jqXHR) {
        alert('基本設定を保存しました。');
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert('基本設定の保存でエラーが発生しました。');
    }).always(function(jqXHR, textStatus, errorThrown) {

    });
});

// 項目設定
$('button.save_item').click(function(e) {
    const url = $('form#item').attr('action');
    const fd = new FormData($('form#item').get(0));

    $.ajax({
        type: 'POST',
        url: url,
        processData: false,
        contentType: false,
        data: fd,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
    }).done(function(data, textStatus, jqXHR) {
        $('table.table-striped tr.bg-danger').remove()
        alert('項目設定を保存しました。');
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert('項目設定の保存でエラーが発生しました。');
    }).always(function(jqXHR, textStatus, errorThrown) {

    });
});

</script>
@stop