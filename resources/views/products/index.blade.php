@foreach($products as $product)
    <!-- 商品の名前などの各カラムの内容を表示 -->
    {{$product->name}}
    {{$product->description}}
    {{$product->price}}
    <a href="{{route('products.show', $product)}}">Show</a>
    <a href="{{route('products.edit', $product)}}">edit</a>
    <!-- 削除リクエストを送信するフォームを作成 -->
    <!-- Deleteボタンが押された際に削除するかを確認 -->
    <form action="/products/{{ $product->id }}" method="POST" onsubmit="if(confirm('Delete? Are you sure?')) { return true } else { return false };">
        <input type="hidden" name="method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit">Delete</button>
    </form>
@endforeach

<!-- routeヘルパーを使うことで/products/createへのリンクを作成 -->
<a href="{{route('products.create')}}">New</a>