@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <span>
                <a href="{{ route('mypage') }}">マイページ</a> > お届け先変更
            </span>

            <div class="container mt-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">注文番号</th>
                            <th scope="col">購入日時</th>
                            <th scope="col">合計金額</th>
                            <th scope="col">詳細</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paginator as $billing)
                        <tr>
                            <td>{{ $billing['code'] }}</td>
                            <td>{{ $billing['created_at']}}</td>
                            <td>{{ $billing['total']}}</td>
                            <td>
                                <a href="{{ route('mypage.cart_history_show', ['id' => $billing['id']]) }}">
                                    {{ $billing['id'] }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $paginator->links() }}
        </div>
    </div>
</div>

@endsection
