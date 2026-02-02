@extends('adminlte::page')

@section('title', 'FLA管理画面 > フォーム C > 自動返信メール設定')

@section('content_header')
    <h1>フォーム C</h1>
@stop

@section('content')
<div class="row">
    <div class="col">

        @if (request('saved') === 'on')
        <div class="alert alert-primary alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-check-circle"></i> 成功</h5>
            保存しました。
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-exclamation-triangle"></i> エラー</h5>
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('backend.form_c.settings.replymail.update') }}" id="mail" class="form-horizontal" method="POST">
            @method('PUT')
            @csrf
            <div class="card">
                <div class="card-header bg-navy">
                    <div class="card-title">自動返信メール設定</div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">送信者</label>
                        <div class="col-sm-10 pt-1">
                            {{ $mail->from_name ?? '' }}
                            &lt;{{ $mail->from_address ?? '' }}&gt;
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Cc</label>
                        <div class="col-sm-10">
                            <textarea name="cc_address" class="form-control" rows="5">{{ old('cc_address', $replymail->cc_address ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Bcc</label>
                        <div class="col-sm-10">
                            <textarea name="bcc_address" class="form-control" rows="5">{{ old('bcc_address', $replymail->bcc_address ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">件名<span class="text-danger pl-1">※</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="subject" class="form-control" value="{{ old('subject', $replymail->subject ?? '') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">本文<span class="text-danger pl-1">※</span></label>
                        <div class="col-sm-10">
                            <textarea name="body" class="form-control" rows="30">{{ old('body', $body ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>保存する</button>
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
</script>
@stop