@extends('layouts/app')
@push('css')
    <link rel="stylesheet" href="{{asset('css/active.css')}}">
@endpush
@section('content')
    <div>
        <img src="{{asset('assets/images/ads_logo.svg')}}" alt="Logo"/>

        <hr/>
    </div>

    <div class="main-content shadow">
        <h1>
            Jesteś już zarejestrowany w <br/>
            Retail Media Network
        </h1>

        <p>Swoją obecnością w RMN zarządzisz w panelu Onet Ads</p>

        <img src="{{asset('assets/images/gate_two.svg')}}" alt="Logo"/>

        <button onclick="window.open('https://konto.onet.pl/signin')">
            <img src="{{asset('assets/icons/send.svg')}}" alt="Send icon"/>
            Przejdź do Onet Ads
        </button>
    </div>
@endsection
