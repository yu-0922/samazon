<nav class="navbar navbar-expand-md navbar-light shadow-sm samazon-header-container">
    <a class="navbar-brand" href="{{ url('/') }}">
        <img src="{{asset('img/logo.jpg')}}">
    </a>
    <form class="form-inline">
        <div class="form-group">
            <input class="form-control samazon-header-search-input">
        </div>
        <div class="input-group">
            <button type="submit" class="btn samazon-header-search-button"><i class="fas fa-search samazon-header-search-icon"></i></button>
        </div>
    </form>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav ml-auto mr-5 mt-2">
            <!-- Authentication Links -->
            @guest
            <li class="nav-item mr-5">
                <a class="nav-link" href="{{ route('register') }}"><label>新規登録</label></a>
            </li>
            <li class="nav-item mr-5">
                <a class="nav-link" href="{{ route('login') }}"><label>ログイン</label></a>
                </li>
            <hr>
            <li class="nav-item mr-5">
                <a class="nav-link" href="{{ route('login') }}"><i class="far fa-heart"></i></a>
            </li>
            <li class="nav-item mr-5">
                <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-shopping-cart"></i></a>
            </li>
            @else
            <!-- elseを使うことでログインしている状態でのみリンクが表示される -->
            <li class="nav-item mr-5">
                <!-- マイページへのリンクを作成 -->
                <a class="nav-link" href="{{ route('mypage') }}">
                    <i class="fas fa-user mr-1"></i><label>マイページ</label>
                </a>
            </li>
            <li class="nav-item mr-5">
                <a class="nav-link" href="{{ route('mypage.favorite') }}">
                    <i class="far fa-heart"></i>
                </a>
            </li>
            <li class="nav-item mr-5">
                <a class="nav-link" href="{{ route('carts.index') }}">
                    <i class="fas fa-shopping-cart"></i>
                </a>
            </li>
            @endguest
        </ul>
    </div>
</nav>
