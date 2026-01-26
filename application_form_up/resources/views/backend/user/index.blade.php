@extends('adminlte::page')

@section('title', 'FLA管理画面 > アカウント管理')

@section('content_header')
    <h1>アカウント管理</h1>
@stop

@section('content')
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header border-bottom-0">
                <h3 class="card-title">{{ number_format($users->total()) }} 件</h3>
                <div class="card-tools">
                    <a href="{{ route('backend.users.create') }}" class="csv ml-3"><i class="fas fa-plus mr-2"></i>新規登録</a>
                </div>
            </div>
            <div class="card-body p-0">
                <table id="workbook_list" class="table table-striped">
                    <thead class="bg-gray">
                        <tr>
                            <th>#</th>
                            <th>アカウント名</th>
                            <th>メールアドレス</th>
                            <th style="width: 150px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <a href="{{ route('backend.users.edit', $user->id) }}"><i class="fas fa-edit mr-2"></i>編集</a>
                                <a href="#deleteModal" data-toggle="modal" data-id="{{ $user->id }}" data-name="{{ $user->name }}" class="ml-3"><i class="fas fa-trash mr-2"></i>削除</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $users->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
</div>
<!-- 削除モーダル -->
<form class="modal" id="deleteModal" action="{{ route('backend.users.destroy', '') }}" method="POST">
    @method('DELETE')
    @csrf
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy">
                <h5 class="modal-title"></h5>
            </div>
            <div class="modal-body">
                <p class="text-danger"></p>
                <p>#<span class="deleteTarget"></span>を削除しますか？</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                <button type="button" class="btn btn-danger">削除する</button>
            </div>
        </div>
    </div>
</form>

@stop

@section('css')
<style>
</style>
@stop

@section('js')
<script>
  var id = '';
  $('#deleteModal').on('show.bs.modal', function(event) {
      $('#deleteModal .modal-title').text($('.content-header h1').text());
      $('#deleteModal .text-danger').html('');
      id = $(event.relatedTarget).data('id');
      var name = $(event.relatedTarget).data('name');
      $(this).find('.modal-body .deleteTarget').text(id + '　' + name);
  });
  $('#deleteModal .btn-danger').on('click', function() {
      $('#deleteModal .text-danger').html('');

      var fd = new FormData();
      fd.append('_method', $('#deleteModal input[name=_method]').val());
      fd.append('_token',  $('#deleteModal input[name=_token]').val());

      $.ajax({
          url: $('#deleteModal').attr('action') + '/' + id,
          type: 'POST',
          data: fd,
          async: false,
          cache: false,
          contentType: false,
          processData: false,
          dataType: 'json'
      }).done(function(data, textStatus, jqXHR) {
          $('#deleteModal').modal('hide');
      }).fail(function(jqXHR, textStatus, errorThrown) {
          if (jqXHR.status === 422) {
              var message = '';
              for (var key in jqXHR.responseJSON.errors) {
                  message += jqXHR.responseJSON.errors[key][0] + '<br>';
              }
              $('#deleteModal .text-danger').html(message);
              return;
          }
      });
  });

</script>
@stop