<html lang="pl">
<head>
    <meta name="referrer" content="origin">
    <script src="https://dcsaascdn.net/js/dc-sdk-1.0.5.min.js"></script>
    <script>
        (function () {
            'use strict';

            var styles;

            if (localStorage.getItem('styles')) {
                styles = JSON.parse(localStorage.getItem('styles'));
                injectStyles(styles);
            }

            window.shopAppInstance = new ShopApp(function (app) {
                app.init(null, function (params, app) {
                    if (localStorage.getItem('styles') === null) {
                        injectStyles(params.styles);
                    }
                    localStorage.setItem('styles', JSON.stringify(params.styles));

                    app.show(null, function () {
                        app.adjustIframeSize();
                    });
                }, function (errmsg, app) {
                    alert(errmsg);
                });
            }, true);

            function injectStyles(styles) {
                var i;
                var el;
                var sLength;

                sLength = styles.length;
                for (i = 0; i < sLength; ++i) {
                    el = document.createElement('link');
                    el.rel = 'stylesheet';
                    el.type = 'text/css';
                    el.href = styles[i];
                    document.getElementsByTagName('head')[0].appendChild(el);
                }
            }
        }());
    </script>
</head>

<body>
<div>
    <div>
        <form action="{{route('configure.save')}}" method="post">
            @csrf
            <label for="website_id">
                WebsiteId
                <input type="text" id="website_id" name="website_id" max="128" value="{{$website_id ?? ''}}">
            </label>
            <input type="hidden" value="{{$shop->shop}}" name="shop_external_id">
            <br>
            <label for="substitute_product">
                Podmieniać produkt?
                <input type="checkbox" id="substitute_product" name="substitute_product"
                       @if($substitute_product) checked @endif>
            </label>
            <input type="submit">
            @if($errors->any())
                <div style="background-color: red;">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div style="background-color: green;">
                    Wszystko okej, zapisano zmiany.
                </div>
            @endif
        </form>
    </div>
</div>
</body>
</html>
