@extends('adminlte::page')

@section('title', 'FLA管理画面 > フォーム C > 項目設定')

@section('content_header')
    <h1 class="d-inline">フォーム C</h1>
    <a href="{{ route('form_c.form') }}" target="_blank"><i class="fas fa-link ml-3 mr-2"></i>{{ route('form_c.form') }}</a>
@stop

@section('content')
<div class="row">
    <div class="col">
        <form action="{{ route('backend.form_c.settings.basic') }}" id="basic" class="form-horizontal" method="POST">
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
        <form action="{{ route('backend.form_c.settings.item') }}" id="item" class="form-horizontal" method="POST">
            @method('PUT')
            <div class="card">
                <div class="card-header bg-navy">
                    <div class="card-title">項目設定</div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">食アド会員</label>
                        <div class="col-sm-10">
                            <table id="member_fee_list" class="table table-striped mb-3">
                                <thead class="bg-gray">
                                    <tr>
                                        <th class="primary_key">#</th>
                                        <th class="enabled">有効</th>
                                        <th>手続き名</th>
                                        <th>料金（税込）</th>
                                        <th class="operation"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (is_array($setting->member_fee_list))
                                    @foreach ($setting->member_fee_list as $member_fee)
                                    <tr id="member_fee{{ $member_fee->id }}">
                                        <td>{{ $member_fee->id }}</td>
                                        <td><input type="checkbox" name="member_fee_enabled[]" value="{{ $member_fee->id }}"@if ($member_fee->enabled) checked @endif></td>
                                        <td>{{ $member_fee->name }}</td>
                                        <td>{{ number_format($member_fee->price) }}</td>
                                        <td class="text-right">
                                            <input type="hidden" name="member_fee_delete[]" value="">
                                            <input type="hidden" name="member_fee_id[]" value="{{ $member_fee->id }}">
                                            <input type="hidden" name="member_fee_name[]" value="{{ $member_fee->name }}">
                                            <input type="hidden" name="member_fee_price[]" value="{{ $member_fee->price }}">
                                            <a href="#memberFeeModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
                                            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                            <div class="text-right">
                                <a href="#memberFeeModal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-2"></i>追加</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">食アドのお店</label>
                        <div class="col-sm-10">
                            <table id="shop_fee_list" class="table table-striped mb-3">
                                <thead class="bg-gray">
                                    <tr>
                                        <th class="primary_key">#</th>
                                        <th class="enabled">有効</th>
                                        <th>手続き名</th>
                                        <th>料金（税込）</th>
                                        <th class="operation"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (is_array($setting->shop_fee_list))
                                    @foreach ($setting->shop_fee_list as $shop_fee)
                                    <tr id="shop_fee{{ $shop_fee->id }}">
                                        <td>{{ $shop_fee->id }}</td>
                                        <td><input type="checkbox" name="shop_fee_enabled[]" value="{{ $shop_fee->id }}"@if ($shop_fee->enabled) checked @endif></td>
                                        <td>{{ $shop_fee->name }}</td>
                                        <td>{{ number_format($shop_fee->price) }}</td>
                                        <td class="text-right">
                                            <input type="hidden" name="shop_fee_delete[]" value="">
                                            <input type="hidden" name="shop_fee_id[]" value="{{ $shop_fee->id }}">
                                            <input type="hidden" name="shop_fee_name[]" value="{{ $shop_fee->name }}">
                                            <input type="hidden" name="shop_fee_price[]" value="{{ $shop_fee->price }}">
                                            <a href="#shopFeeModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
                                            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                            <div class="text-right">
                                <a href="#shopFeeModal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-2"></i>追加</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">食アドゼミナール</label>
                        <div class="col-sm-10">
                            <div class="form-inline mt-1">
                                <div class="form-group">
                                    <input type="checkbox" name="seminar_enabled" id="seminar_enabled" value="1"@if ($setting->seminar_enabled) checked @endif>
                                    <label for="seminar_enabled" class="font-weight-normal ml-2">受講申込を受け付ける</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">食アドゼミナール　会場</label>
                        <div class="col-sm-10">
                            <table id="seminar_venue_list" class="table table-striped mb-3">
                                <thead class="bg-gray">
                                    <tr>
                                        <th class="primary_key">#</th>
                                        <th class="enabled">有効</th>
                                        <th>会場名</th>
                                        <th>受講料表記</th>
                                        <th>受講料（税込）</th>
                                        <th class="operation"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (is_array($setting->seminar_venue_list))
                                    @foreach ($setting->seminar_venue_list as $seminar_venue)
                                    <tr id="seminar_venue{{ $seminar_venue->id }}">
                                        <td>{{ $seminar_venue->id }}</td>
                                        <td><input type="checkbox" name="seminar_venue_enabled[]" value="{{ $seminar_venue->id }}"@if ($seminar_venue->enabled) checked @endif></td>
                                        <td>{{ $seminar_venue->name }}</td>
                                        <td>{{ $seminar_venue->price_label }}</td>
                                        <td>{{ number_format($seminar_venue->price) }}</td>
                                        <td class="text-right">
                                            <input type="hidden" name="seminar_venue_delete[]" value="">
                                            <input type="hidden" name="seminar_venue_id[]" value="{{ $seminar_venue->id }}">
                                            <input type="hidden" name="seminar_venue_name[]" value="{{ $seminar_venue->name }}">
                                            <input type="hidden" name="seminar_venue_price_label[]" value="{{ $seminar_venue->price_label }}">
                                            <input type="hidden" name="seminar_venue_price[]" value="{{ $seminar_venue->price }}">
                                            <a href="#seminarVenueModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
                                            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                            <div class="text-right">
                                <a href="#seminarVenueModal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-2"></i>追加</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">食アドAcademy</label>
                        <div class="col-sm-10">
                            <div class="form-inline mt-1">
                                <input type="checkbox" name="academy_enabled" id="academy_enabled" value="1"@if ($setting->academy_enabled) checked @endif>
                                <label for="academy_enabled" class="font-weight-normal ml-2 mr-5">受講申込を受け付ける</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">食アドAcademy　タイトル</label>
                        <div class="col-sm-10">
                            <input type="text" name="academy_title" id="academy_title" class="form-control" value="{{ $setting->academy_title }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">食アドAcademy　講座</label>
                        <div class="col-sm-10">
                            <table id="academy_course_list" class="table table-striped mb-3">
                                <thead class="bg-gray">
                                    <tr>
                                        <th class="primary_key">#</th>
                                        <th class="enabled">有効</th>
                                        <th>講座名</th>
                                        <th>受講料表記</th>
                                        <th>受講料（税込）</th>
                                        <th class="operation"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (is_array($setting->academy_course_list))
                                    @foreach ($setting->academy_course_list as $academy_course)
                                    <tr id="academy_course{{ $academy_course->id }}">
                                        <td>{{ $academy_course->id }}</td>
                                        <td><input type="checkbox" name="academy_course_enabled[]" value="{{ $academy_course->id }}"@if ($academy_course->enabled) checked @endif></td>
                                        <td>{{ $academy_course->name }}</td>
                                        <td>{{ $academy_course->price_label }}</td>
                                        <td>{{ number_format($academy_course->price) }}</td>
                                        <td class="text-right">
                                            <input type="hidden" name="academy_course_delete[]" value="">
                                            <input type="hidden" name="academy_course_id[]" value="{{ $academy_course->id }}">
                                            <input type="hidden" name="academy_course_name[]" value="{{ $academy_course->name }}">
                                            <input type="hidden" name="academy_course_price_label[]" value="{{ $academy_course->price_label }}">
                                            <input type="hidden" name="academy_course_price[]" value="{{ $academy_course->price }}">
                                            <a href="#academyCourseModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
                                            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                            <div class="text-right">
                                <a href="#academyCourseModal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-2"></i>追加</a>
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

<!--食アド会員モーダル -->
<form class="modal" id="memberFeeModal" method="POST">
    <input type="hidden" name="id" value="">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h5 class="modal-title">食アド会員</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible" style="display: none;">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
                    <h6><i class="icon fas fa-exclamation-triangle"></i> エラー</h6>
                    <div class="error">全ての項目を正しく入力してください。</div>
                </div>
                <div class="form-group row">
                    <label for="member_fee_name" class="col-sm-3 col-form-label">手続き名<span class="text-danger pl-1">※</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="member_fee_name" id="member_fee_name" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="member_fee_price" class="col-sm-3 col-form-label">料金（税込）<span class="text-danger pl-1">※</span></label>
                    <div class="col-sm-9">
                        <input type="number" name="member_fee_price" id="member_fee_price" class="form-control w-auto">
                        <span class="text-muted">半角数字のみ</span>
                        <!-- <span class="error text-danger">半角数字のみを入力してください。</span> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                <button type="button" id="set_member_fee" class="btn btn-primary"></button>
            </div>
        </div>
    </div>
</form>
<template id="member_fee_row">
    <tr id="">
        <td></td>
        <td><input type="checkbox" name="member_fee_enabled[]" value=""></td>
        <td></td>
        <td></td>
        <td class="text-right">
            <input type="hidden" name="member_fee_delete[]" value="">
            <input type="hidden" name="member_fee_id[]" value="">
            <input type="hidden" name="member_fee_name[]" value="">
            <input type="hidden" name="member_fee_price[]" value="">
            <a href="#memberFeeModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
        </td>
    </tr>
</template>

<!--食アドのお店モーダル -->
<form class="modal" id="shopFeeModal" method="POST">
    <input type="hidden" name="id" value="">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h5 class="modal-title">食アドのお店</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible" style="display: none;">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
                    <h6><i class="icon fas fa-exclamation-triangle"></i> エラー</h6>
                    <div class="error">全ての項目を正しく入力してください。</div>
                </div>
                <div class="form-group row">
                    <label for="shop_fee_name" class="col-sm-3 col-form-label">手続き名<span class="text-danger pl-1">※</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="shop_fee_name" id="shop_fee_name" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="shop_fee_price" class="col-sm-3 col-form-label">料金（税込）<span class="text-danger pl-1">※</span></label>
                    <div class="col-sm-9">
                        <input type="number" name="shop_fee_price" id="shop_fee_price" class="form-control w-auto">
                        <span class="text-muted">半角数字のみ</span>
                        <!-- <span class="error text-danger">半角数字のみを入力してください。</span> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                <button type="button" id="set_shop_fee" class="btn btn-primary"></button>
            </div>
        </div>
    </div>
</form>
<template id="shop_fee_row">
    <tr id="">
        <td></td>
        <td><input type="checkbox" name="shop_fee_enabled[]" value=""></td>
        <td></td>
        <td></td>
        <td class="text-right">
            <input type="hidden" name="shop_fee_delete[]" value="">
            <input type="hidden" name="shop_fee_id[]" value="">
            <input type="hidden" name="shop_fee_name[]" value="">
            <input type="hidden" name="shop_fee_price[]" value="">
            <a href="#shopFeeModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
        </td>
    </tr>
</template>

<!-- 食アドゼミナール　会場モーダル -->
<form class="modal" id="seminarVenueModal" method="POST">
    <input type="hidden" name="id" value="">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h5 class="modal-title">食アドゼミナール　会場</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible" style="display: none;">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
                    <h6><i class="icon fas fa-exclamation-triangle"></i> エラー</h6>
                    <div class="error">全ての項目を正しく入力してください。</div>
                </div>
                <div class="form-group row">
                    <label for="seminar_venue_name" class="col-sm-4 col-form-label">会場名<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="seminar_venue_name" id="seminar_venue_name" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="seminar_venue_price_label" class="col-sm-4 col-form-label">受講料表記<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="seminar_venue_price_label" id="seminar_venue_price_label" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="seminar_venue_price" class="col-sm-4 col-form-label">受講料（税込）<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-8">
                        <input type="number" name="seminar_venue_price" id="seminar_venue_price" class="form-control w-auto">
                        <span class="text-muted">半角数字のみ</span>
                        <!-- <span class="error text-danger">半角数字のみを入力してください。</span> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                <button type="button" id="set_seminar_venue" class="btn btn-primary"></button>
            </div>
        </div>
    </div>
</form>
<template id="seminar_venue_row">
    <tr id="">
        <td></td>
        <td><input type="checkbox" name="seminar_venue_enabled[]" value=""></td>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right">
            <input type="hidden" name="seminar_venue_delete[]" value="">
            <input type="hidden" name="seminar_venue_id[]" value="">
            <input type="hidden" name="seminar_venue_name[]" value="">
            <input type="hidden" name="seminar_venue_price_label[]" value="">
            <input type="hidden" name="seminar_venue_price[]" value="">
            <a href="#seminarVenueModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
            <a href="#" class="delete ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
        </td>
    </tr>
</template>

<!-- 食アドAcademy　講座モーダル -->
<form class="modal" id="academyCourseModal" method="POST">
    <input type="hidden" name="id" value="">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h5 class="modal-title">食アドAcademy　講座</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible" style="display: none;">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
                    <h6><i class="icon fas fa-exclamation-triangle"></i> エラー</h6>
                    <div class="error">全ての項目を正しく入力してください。</div>
                </div>
                <div class="form-group row">
                    <label for="academy_course_name" class="col-sm-4 col-form-label">講座名<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="academy_course_name" id="academy_course_name" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="academy_course_price_label" class="col-sm-4 col-form-label">受講料表記<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="academy_course_price_label" id="academy_course_price_label" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="academy_course_price" class="col-sm-4 col-form-label">受講料（税込）<span class="text-danger pl-2">※</span></label>
                    <div class="col-sm-8">
                        <input type="number" name="academy_course_price" id="academy_course_price" class="form-control w-auto">
                        <span class="text-muted">半角数字のみ</span>
                        <!-- <span class="error text-danger">半角数字のみを入力してください。</span> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                <button type="button" id="set_academy_course" class="btn btn-primary"></button>
            </div>
        </div>
    </div>
</form>
<template id="academy_course_row">
    <tr id="">
        <td></td>
        <td><input type="checkbox" name="academy_course_enabled[]" value=""></td>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right">
            <input type="hidden" name="academy_course_delete[]" value="">
            <input type="hidden" name="academy_course_id[]" value="">
            <input type="hidden" name="academy_course_name[]" value="">
            <input type="hidden" name="academy_course_price_label[]" value="">
            <input type="hidden" name="academy_course_price[]" value="">
            <a href="#academyCourseModal" data-toggle="modal"><i class="fas fa-edit mr-2"></i>編集</a>
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

// 食アド会員
$('#memberFeeModal').on('show.bs.modal', function (event) {
    let id = $(event.relatedTarget).parent().parent().attr('id');
    if (id) {
        id = id.match(/(\d+)$/)[1];
        const member_fee_name = $('#member_fee' + id + ' :hidden[name="member_fee_name[]"]').val();
        const member_fee_price = $('#member_fee' + id + ' :hidden[name="member_fee_price[]"]').val();
        $('#memberFeeModal input:hidden[name=id]').val(id);
        $('#member_fee_name').val(member_fee_name);
        $('#member_fee_price').val(member_fee_price);
        $('#set_member_fee').text('編集内容を反映する');
    } else {
        $('#memberModal input:hidden[name=id]').val('');
        $('#member_fee_name').val('');
        $('#member_fee_price').val('');
        $('#set_member_fee').text('追加する');
    }
});
$('button#set_member_fee').click(function(e) {
    if (!validateModal('#memberFeeModal')) {
        return false;
    }

    let id = $('#memberFeeModal input:hidden[name=id]').val();
    const member_fee_name = $('#member_fee_name').val();
    const member_fee_price = $('#member_fee_price').val();

    if (id == '') {
        id = getNewId('#member_fee_list');
        let row = $($('#member_fee_row').html());
        row.attr('id', 'member_fee' + id);
        row.find('td:eq(0)').text(id);
        row.find('td:eq(2)').text(member_fee_name);
        row.find('td:eq(3)').text(formatNumber(member_fee_price));
        row.find(':hidden[name="member_fee_id[]"]').val(id);
        row.find(':checkbox[name="member_fee_enabled[]"]').val(id);
        row.find(':checkbox[name="member_fee_enabled[]"]').prop('checked', true);
        row.find(':hidden[name="member_fee_name[]"]').val(member_fee_name);
        row.find(':hidden[name="member_fee_price[]"]').val(member_fee_price);
        $('#member_fee_list tbody').append(row);
    } else {
        let row = $('tr#member_fee' + id);
        row.find('td:eq(2)').text(member_fee_name);
        row.find('td:eq(3)').text(formatNumber(member_fee_price));
        row.find(':hidden[name="member_fee_name[]"]').val(member_fee_name);
        row.find(':hidden[name="member_fee_price[]"]').val(member_fee_price);
    }

    $('#memberFeeModal').modal('hide');

    return true;
});

// 食アドのお店
$('#shopFeeModal').on('show.bs.modal', function (event) {
    let id = $(event.relatedTarget).parent().parent().attr('id');
    if (id) {
        id = id.match(/(\d+)$/)[1];
        const shop_fee_name = $('#shop_fee' + id + ' :hidden[name="shop_fee_name[]"]').val();
        const shop_fee_price = $('#shop_fee' + id + ' :hidden[name="shop_fee_price[]"]').val();
        $('#shopFeeModal input:hidden[name=id]').val(id);
        $('#shop_fee_name').val(shop_fee_name);
        $('#shop_fee_price').val(shop_fee_price);
        $('#set_shop_fee').text('編集内容を反映する');
    } else {
        $('#shopFeeModal input:hidden[name=id]').val('');
        $('#shop_fee_name').val('');
        $('#shop_fee_price').val('');
        $('#set_shop_fee').text('追加する');
    }
});
$('button#set_shop_fee').click(function(e) {
    if (!validateModal('#shopFeeModal')) {
        return false;
    }

    let id = $('#shopFeeModal input:hidden[name=id]').val();
    const shop_fee_name = $('#shop_fee_name').val();
    const shop_fee_price = $('#shop_fee_price').val();

    if (id == '') {
        id = getNewId('#shop_fee_list');
        let row = $($('#shop_fee_row').html());
        row.attr('id', 'shop_fee' + id);
        row.find('td:eq(0)').text(id);
        row.find('td:eq(2)').text(shop_fee_name);
        row.find('td:eq(3)').text(formatNumber(shop_fee_price));
        row.find(':hidden[name="shop_fee_id[]"]').val(id);
        row.find(':checkbox[name="shop_fee_enabled[]"]').val(id);
        row.find(':checkbox[name="shop_fee_enabled[]"]').prop('checked', true);
        row.find(':hidden[name="shop_fee_name[]"]').val(shop_fee_name);
        row.find(':hidden[name="shop_fee_price[]"]').val(shop_fee_price);
        $('#shop_fee_list tbody').append(row);
    } else {
        let row = $('tr#shop_fee' + id);
        row.find('td:eq(2)').text(shop_fee_name);
        row.find('td:eq(3)').text(formatNumber(shop_fee_price));
        row.find(':hidden[name="shop_fee_name[]"]').val(shop_fee_name);
        row.find(':hidden[name="shop_fee_price[]"]').val(shop_fee_price);
    }

    $('#shopFeeModal').modal('hide');

    return true;
});


// 食アドゼミナール　会場
$('#seminarVenueModal').on('show.bs.modal', function (event) {
    let id = $(event.relatedTarget).parent().parent().attr('id');
    if (id) {
        id = id.match(/(\d+)$/)[1];
        const seminar_venue_name        = $('#seminar_venue' + id + ' :hidden[name="seminar_venue_name[]"]').val();
        const seminar_venue_price_label = $('#seminar_venue' + id + ' :hidden[name="seminar_venue_price_label[]"]').val();
        const seminar_venue_price       = $('#seminar_venue' + id + ' :hidden[name="seminar_venue_price[]"]').val();
        $('#seminarVenueModal input:hidden[name=id]').val(id);
        $('#seminar_venue_name').val(seminar_venue_name);
        $('#seminar_venue_price_label').val(seminar_venue_price_label);
        $('#seminar_venue_price').val(seminar_venue_price);
        $('#set_seminar_venue').text('編集内容を反映する');
    } else {
        $('#seminarVenueModal input:hidden[name=id]').val('');
        $('#seminar_venue_name').val('');
        $('#seminar_venue_price_label').val('');
        $('#seminar_venue_price').val('');
        $('#set_seminar_venue').text('追加する');
    }
});
$('button#set_seminar_venue').click(function(e) {
    if (!validateModal('#seminarVenueModal')) {
        return false;
    }

    let id = $('#seminarVenueModal input:hidden[name=id]').val();
    const seminar_venue_name        = $('#seminar_venue_name').val();
    const seminar_venue_price_label = $('#seminar_venue_price_label').val();
    const seminar_venue_price       = $('#seminar_venue_price').val();

    if (id == '') {
        id = getNewId('#seminar_venue_list');
        let row = $($('#seminar_venue_row').html());
        row.attr('id', 'seminar_venue' + id);
        row.find('td:eq(0)').text(id);
        row.find('td:eq(2)').text(seminar_venue_name);
        row.find('td:eq(3)').text(seminar_venue_price_label);
        row.find('td:eq(4)').text(formatNumber(seminar_venue_price));
        row.find(':hidden[name="seminar_venue_id[]"]').val(id);
        row.find(':checkbox[name="seminar_venue_enabled[]"]').val(id);
        row.find(':checkbox[name="seminar_venue_enabled[]"]').prop('checked', true);
        row.find(':hidden[name="seminar_venue_name[]"]').val(seminar_venue_name);
        row.find(':hidden[name="seminar_venue_price_label[]"]').val(seminar_venue_price_label);
        row.find(':hidden[name="seminar_venue_price[]"]').val(seminar_venue_price);
        $('#seminar_venue_list tbody').append(row);
    } else {
        let row = $('tr#seminar_venue' + id);
        row.find('td:eq(2)').text(seminar_venue_name);
        row.find('td:eq(3)').text(seminar_venue_price_label);
        row.find('td:eq(4)').text(formatNumber(seminar_venue_price));
        row.find(':hidden[name="seminar_venue_name[]"]').val(seminar_venue_name);
        row.find(':hidden[name="seminar_venue_price_label[]"]').val(seminar_venue_price_label);
        row.find(':hidden[name="seminar_venue_price[]"]').val(seminar_venue_price);
    }

    $('#seminarVenueModal').modal('hide');

    return true;
});

// 食アドAcademy　講座
$('#academyCourseModal').on('show.bs.modal', function (event) {
    let id = $(event.relatedTarget).parent().parent().attr('id');
    if (id) {
        id = id.match(/(\d+)$/)[1];
        const academy_course_name        = $('#academy_course' + id + ' :hidden[name="academy_course_name[]"]').val();
        const academy_course_price_label = $('#academy_course' + id + ' :hidden[name="academy_course_price_label[]"]').val();
        const academy_course_price       = $('#academy_course' + id + ' :hidden[name="academy_course_price[]"]').val();
        $('#academyCourseModal input:hidden[name=id]').val(id);
        $('#academy_course_name').val(academy_course_name);
        $('#academy_course_price_label').val(academy_course_price_label);
        $('#academy_course_price').val(academy_course_price);
        $('#set_academy_course').text('編集内容を反映する');
    } else {
        $('#academyCourseModal input:hidden[name=id]').val('');
        $('#academy_course_name').val('');
        $('#academy_course_price_label').val('');
        $('#academy_course_price').val('');
        $('#set_academy_course').text('追加する');
    }
});
$('button#set_academy_course').click(function(e) {
    if (!validateModal('#academyCourseModal')) {
        return false;
    }

    let id = $('#academyCourseModal input:hidden[name=id]').val();
    const academy_course_name        = $('#academy_course_name').val();
    const academy_course_price_label = $('#academy_course_price_label').val();
    const academy_course_price       = $('#academy_course_price').val();

    if (id == '') {
        id = getNewId('#academy_course_list');
        let row = $($('#academy_course_row').html());
        row.attr('id', 'academy_course' + id);
        row.find('td:eq(0)').text(id);
        row.find('td:eq(2)').text(academy_course_name);
        row.find('td:eq(3)').text(academy_course_price_label);
        row.find('td:eq(4)').text(formatNumber(academy_course_price));
        row.find(':hidden[name="academy_course_id[]"]').val(id);
        row.find(':checkbox[name="academy_course_enabled[]"]').val(id);
        row.find(':checkbox[name="academy_course_enabled[]"]').prop('checked', true);
        row.find(':hidden[name="academy_course_name[]"]').val(academy_course_name);
        row.find(':hidden[name="academy_course_price_label[]"]').val(academy_course_price_label);
        row.find(':hidden[name="academy_course_price[]"]').val(academy_course_price);
        $('#academy_course_list tbody').append(row);
    } else {
        let row = $('tr#academy_course' + id);
        row.find('td:eq(2)').text(academy_course_name);
        row.find('td:eq(3)').text(academy_course_price_label);
        row.find('td:eq(4)').text(formatNumber(academy_course_price));
        row.find(':hidden[name="academy_course_name[]"]').val(academy_course_name);
        row.find(':hidden[name="academy_course_price_label[]"]').val(academy_course_price_label);
        row.find(':hidden[name="academy_course_price[]"]').val(academy_course_price);
    }

    $('#academyCourseModal').modal('hide');

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