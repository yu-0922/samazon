@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-center">
    <div class="row w-75">
        <div class="col-5 offset-1">
            @if ($product->image !== null)
            <img src="{{ Storage::disk('s3')->url($product->image) }}" class="w-100 img-fluid">
            @else
            <img src="{{ asset('img/dummy.png')}}" class="w-100 img-fluid">
            @endif
        </div>
        <div class="col">
            <div class="d-flex flex-column">
                <h1 class="">
                    {{$product->name}}
                </h1>
                <p class="">
                    {{$product->description}}
                </p>
                <hr>
                <p class="d-flex align-items-end">
                    ￥{{$product->price}}(税込)
                </p>
                <hr>
            </div>
            @auth
            <!-- カートに追加する商品のIDなどをCartControllerのstoreアクションに送信 -->
            <form method="POST" action="{{route('carts.store')}}" class="m-3 align-items-end">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{$product->id}}">
                <input type="hidden" name="name" value="{{$product->name}}">
                <input type="hidden" name="price" value="{{$product->price}}">
                <input type="hidden" name="carriage" value="{{$product->carriage_flag}}">
                <div class="form-group row">
                    <label for="quantity" class="col-sm-2 col-form-label">数量</label>
                    <div class="col-sm-10">
                        <input type="number" id="quantity" name="qty" min="1" value="1" class="form-control w-25">
                    </div>
                </div>
                <input type="hidden" name="weight" value="0">
                <div class="row">
                    <div class="col-7">
                        <button type="submit" class="btn samazon-submit-button w-100">
                            <i class="fas fa-shopping-cart"></i>
                            カートに追加
                        </button>
                    </div>
                    <div class="col-5">
                        <!-- isFavoritedBy(Auth::user())でその商品がログインしているユーザーによってお気に入り登録されているか確認 -->
                        <!-- お気に入り登録されているか場合は、解除ボタンを押すとお気に入りから解除できる -->
                        @if($product->isFavoritedBy(Auth::user()))
                            <a href="/products/{{ $product->id }}/favorite" class="btn samazon-favorite-button text-favorite w-100">
                                <i class="fa fa-heart"></i>
                                お気に入り解除
                            </a>
                        @else
                            <a href="/products/{{ $product->id }}/favorite" class="btn samazon-favorite-button text-favorite w-100">
                                <i class="fa fa-heart"></i>
                                お気に入り
                            </a>
                        @endif
                    </div>
                </div>
            </form>
            @endauth
        </div>

        <div class="offset-1 col-11">
            <hr class="w-100">
            <h3 class="float-left">カスタマーレビュー</h3>
        </div>

        <div class="offset-1 col-10">
            <div class="row">
                @foreach($reviews as $review)
                <div class="offset-md-5 col-md-5">
                    <h3 class="review-score-color">{{ str_repeat('★', $review->score) }}</h3>
                    <p class="h3">{{$review->content}}</p>
                    <label>{{$review->created_at}}</label>
                </div>
                @endforeach
            </div>

            @auth
            <div class="row">
                <div class="offset-md-5 col-md-5">
                    <form method="POST" action="/products/{{ $product->id }}/reviews">
                        {{ csrf_field() }}
                            <h4>評価</h4>
                            <select name="score" class="form-control m-2 review-score-color">
                                <!-- 選んだ評価をそのまま数値としてコントローラーに送信 -->
                                <option value="5" class="review-score-color">★★★★★</option>
                                <option value="4" class="review-score-color">★★★★</option>
                                <option value="3" class="review-score-color">★★★</option>
                                <option value="2" class="review-score-color">★★</option>
                                <option value="1" class="review-score-color">★</option>
                            </select>
                            <h4>レビュー内容</h4>
                        <textarea name="content" class="form-control m-2"></textarea>
                        <button type="submit" class="btn samazon-submit-button ml-2">レビューを追加</button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </div>
</div>
@endsection
