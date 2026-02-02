@extends('adminlte::page')

@section('title', 'FLA管理画面 > アカウント管理')

@section('content_header')
    <h1>アカウント管理</h1>
@stop

@section('content')
<div class="row">
    <div class="col">
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

        <form action="{{ $action }}" method="POST">
            @csrf
            <input type="hidden" name="_method" value="{{ $method }}">
            <div class="card">
                <div class="card-header bg-navy">
                    <div class="card-title">{{ $title }}</div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">
                            アカウント名<span class="text-danger pl-1">※</span>
                        </label>
                        <div class="col-lg-10">
                            <input type="text" name="name" id="name" class="form-control w-auto" value="{{ old('name', $user->name) }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">
                            メールアドレス<span class="text-danger pl-1">※</span>
                        </label>
                        <div class="col-lg-10">
                            <input type="text" name="email" id="email" class="form-control w-auto" value="{{ old('email', $user->email) }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">
                            パスワード
                            @if (Route::is('backend.users.create')) <span class="text-danger pl-1">※</span> @endif
                        </label>
                        <div class="col-lg-10">
                            <input type="password" name="password" id="password" class="form-control w-auto" value="{{ old('password', '') }}">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('backend.users.index') }}" class="btn btn-secondary"><i class="fas fa-save mr-2"></i>前の画面へ戻る</a>
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