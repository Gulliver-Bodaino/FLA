@extends('adminlte::page')

@section('title', 'FLA管理画面 > フォーム A > 申込データ管理')

@section('content_header')
    <h1>フォーム A</h1>
@stop

@section('content')
<div class="row">
    <div class="col">
        <form action="{{ route('backend.form_a.applications.update', $application->id) }}" method="POST">
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
                            過去に食生活アドバイザーの願書請求をしたことがありますか？<br>
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
                        <label class="col-lg-2">職業</label>
                        <div class="col-lg-10">{{ $application->job }}</div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">検定試験</label>
                        <div class="col-lg-10">
                        @if ($application->exam_id)
                            {{ $application->exam_name }}<br>
                            {{ number_format($application->exam_price) }}円<br>
                            {{ $application->exam_venue_name }}
                        @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">通学コース</label>
                        <div class="col-lg-10">
                        @if ($application->normal)
                            2級講座
                            {{ number_format($application->normal_price) }}円<br>
                            {{ $application->normal_venue_city_name }}<br>
                            {{ $application->normal_venue_name }}<br>
                            {{ $application->normal_venue_schedule }}
                        @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">速習コース</label>
                        <div class="col-lg-10">
                        @if ($application->fast)
                            {{ $application->fast_course_name }}<br>
                            {{ number_format($application->fast_course_price) }}円<br>
                            @foreach ($application->fast_venue_list as $fast_venue)
                            ・{{ $fast_venue->city_name }}　{{ $fast_venue->name }}　{{ $fast_venue->schedule }}<br>
                            @endforeach
                        @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">科目別 過去問題集</label>
                        <div class="col-lg-10">
                        @if ($application->workbook_id)
                            {{ $application->workbook_name }}<br>
                            {{ number_format($application->workbook_price) }}円
                        @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">お支払い合計</label>
                        <div class="col-lg-10">
                            {{ number_format($application->total) }}円
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">クレジットカード決済</label>
                        <div class="col-lg-10">
                            トラッキングID：{{ $application->tracking_id }}<br>
                            SpsTransactionID：{{ $application->sps_transaction_id }}
                        </div>
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