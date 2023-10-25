@extends('layouts/app')
@section('content')
    <div>
        <div>
            <form action="{{route('configure.save')}}" method="post">
                <label for="website_id">
                    WebsiteId
                    <input type="text" id="website_id" name="website_id" max="128" value="{{$website_id ?? ''}}">
                </label>
                <input type="hidden" value="{{$shop->shop}}" name="shop_external_id">
                <br>
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
@endsection
