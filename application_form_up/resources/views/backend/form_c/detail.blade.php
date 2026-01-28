@extends('adminlte::page')

@section('title', 'FLA管理画面 > フォーム C > 申込データ管理')

@section('content_header')
    <h1>フォーム C</h1>
@stop

@section('content')
<div class="row">
    <div class="col">
        <form action="{{ route('backend.form_c.applications.update', $application->id) }}" method="POST">
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
                        <label class="col-lg-2">食生活アドバイザー会員</label>
                        <div class="col-lg-10">
                            {{ $application->member }}<br>
                            {{ $application->member_number }}
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
                        <label class="col-lg-2">食アド会員</label>
                        <div class="col-lg-10">
                        @if ($application->member_fee_id)
                            {{ $application->member_fee_name }}
                            {{ number_format($application->member_fee_price) }}円
                        @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">食アドのお店</label>
                        <div class="col-lg-10">
                        @if ($application->shop_fee_id)
                            {{ $application->shop_fee_name }}
                            {{ number_format($application->shop_fee_price) }}円
                        @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">食アドゼミナール</label>
                        <div class="col-lg-10">
                        @if ($application->seminar_venue)
                            @foreach ($application->seminar_venue_list as $seminar_venue)
                            ・{{ $seminar_venue->name }}　{{ $seminar_venue->price_label }}<br>
                            @endforeach
                        @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2">食アドAcademy</label>
                        <div class="col-lg-10">
                        @if ($application->academy_course)
                            @foreach ($application->academy_course_list as $academy_course)
                            ・{{ $academy_course->name }}　{{ $academy_course->price_label }}<br>
                            @endforeach
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
                        <label class="col-lg-2">支払い方法</label>
                        <div class="col-lg-10">
                            @if ($application->tracking_id || $application->sps_transaction_id)
                                クレジットカード決済<br>
                                トラッキングID：{{ $application->tracking_id }}<br>
                                SpsTransactionID：{{ $application->sps_transaction_id }}
                            @else
                                郵便局支払い
                            @endif
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