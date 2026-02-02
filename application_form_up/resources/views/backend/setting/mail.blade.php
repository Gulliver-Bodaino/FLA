@extends('adminlte::page')

@section('title', 'FLA管理画面 > メール送信設定')

@section('content_header')
    <h1>メール送信設定</h1>
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

        <form action="{{ route('backend.settings.mail.update') }}" id="mail" class="form-horizontal" method="POST">
            @method('PUT')
            @csrf
            <div class="card">
                <div class="card-header bg-navy">
                    <div class="card-title">メール送信情報</div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">送信者名（差出人）</label>
                        <div class="col-sm-10">
                            <input type="text" name="from_name" class="form-control" value="{{ old('from_name', $mail->from_name ?? '') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">メールアドレス<span class="text-danger pl-1">※</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="from_address" class="form-control" value="{{ old('from_address', $mail->from_address ?? '') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">SMTPサーバー<span class="text-danger pl-1">※</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="mailers_smtp_host" class="form-control w-auto" value="{{ old('mailers_smtp_host', $mail->mailers_smtp_host ?? '') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">ポート番号<span class="text-danger pl-1">※</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="mailers_smtp_port" class="form-control w-auto" value="{{ old('mailers_smtp_port', $mail->mailers_smtp_port ?? '') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">アカウント<span class="text-danger pl-1">※</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="mailers_smtp_username" class="form-control w-auto" value="{{ old('mailers_smtp_username', $mail->mailers_smtp_username ?? '') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">パスワード<span class="text-danger pl-1">※</span></label>
                        <div class="col-sm-10">
                            <input type="password" name="mailers_smtp_password" class="form-control w-auto" value="{{ old('mailers_smtp_password', $mail->mailers_smtp_password ?? '') }}">
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