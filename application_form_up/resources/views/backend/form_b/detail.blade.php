@extends('adminlte::page')

@section('title', 'FLA管理画面 > フォーム B > 申込データ管理')

@section('content_header')
    <h1>フォーム B</h1>
@stop

@section('content')
<div class="row">
    <div class="col">
        <form action="{{ route('backend.form_b.applications.update', $application->id) }}" method="POST">
            @method('PUT')
            @csrf
            <div class="card">
                <div class="card-header bg-navy">
                    <div class="card-title">申込データ管理</div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-lg-2">#</label>
                        <div class="col-lg-10">{{ $application->id }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">申込日時</label>
                        <div class="col-lg-10">{{ $application->created_at }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">ステータス</label>
                        <div class="col-lg-10">
                        {{ Form::select('status', array_flip(config('common.status')), $application->status, ['id' => 'status', 'class' => 'form-control w-auto']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">ご質問</label>
                        <div class="col-lg-10">
                            過去に食生活アドバイザーの専用の払込取扱票請求をしたことがありますか？<br>
                            {{ $application->answer1 }}<br>
                            <br>
                            過去に食生活アドバイザーの受験をしたことがありますか？<br>
                            {{ $application->answer2 }}<br>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">氏名</label>
                        <div class="col-lg-10">{{ $application->sei }}　{{ $application->mei }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">フリガナ</label>
                        <div class="col-lg-10">{{ $application->sei_kana }}　{{ $application->mei_kana }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">生年月日</label>
                        <div class="col-lg-10">{{ $application->birthday }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">性別</label>
                        <div class="col-lg-10">{{ $application->gender }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">住所</label>
                        <div class="col-lg-10">
                            〒 {{ substr($application->zip, 0, 3) }} - {{ substr($application->zip, 3) }}<br>
                            {{ $application->pref }}<br>
                            {{ $application->city }}<br>
                            {{ $application->address1 }}<br>
                            {{ $application->address2 }}<br>
                            {{ $application->workplace }}<br>
                            {{ $application->department }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">電話番号</label>
                        <div class="col-lg-10">{{ $application->tel }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">メールアドレス</label>
                        <div class="col-lg-10">{{ $application->mailaddress }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">メモ</label>
                        <div class="col-lg-10">
                            <textarea name="memo" id="memo" class="form-control" rows="10">{{ $application->memo }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">最終更新日時</label>
                        <div class="col-lg-10">{{ $application->updated_at }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary"><i class="fas fa-save mr-2"></i>前の画面へ戻る</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>更新する</button>
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