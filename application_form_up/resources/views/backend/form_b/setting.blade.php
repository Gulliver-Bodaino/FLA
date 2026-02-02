@extends('adminlte::page')

@section('title', 'FLA管理画面 > フォーム B > 項目設定')

@section('content_header')
<div>
    <h1 class="d-inline">フォーム B</h1>
    <a href="{{ route('form_b.form') }}" target="_blank"><i class="fas fa-link ml-3 mr-2"></i>{{ route('form_b.form') }}</a>
</div>
@stop

@section('content')
<div class="row">
    <div class="col">
        <form action="{{ route('backend.form_b.settings.basic') }}" id="basic" class="form-horizontal" method="POST">
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
@stop

@section('css')
<style>
</style>
@stop

@section('js')
<script>

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

</script>
@stop