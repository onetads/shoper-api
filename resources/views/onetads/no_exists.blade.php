@extends('layouts/app')
@push('css')
    <link rel="stylesheet" href="{{asset('css/inactive.css')}}">
@endpush
@section('content')
    <div>
        <img src="{{asset('assets/images/ads_logo.svg')}}" alt="Logo"/>

        <hr/>
    </div>

    <div class="hero-section container-spacing shadow">
        <div class="left-side">
            <h1>Napędzamy sprzedaż w najlepszych sklepach</h1>

            <p>
                Retail Media Network by Onet Ads to dynamicznie <br/>
                rozwijająca się sieć reklamowa, łącząca sklepy internetowe z
                producentami.
            </p>

            <button onclick="openLinks('{{route('onetads.thanks')}}', 'https://konto.onet.pl/signin')">
                <img src="{{asset('assets/icons/send_black.svg')}}" alt="Send icon"/>
                Dołącz do RMN - zarejestruj się w Onet Ads
            </button>

            <div class="new-tab">
                <img
                    src="{{asset('assets/icons/new_tab.svg')}}"
                    alt="New Tab icon"
                    style="fill: red"
                />
                <span>Zostanie otworzona nowa zakładka</span>
            </div>
        </div>

        <div class="right-side">
            <img src="{{asset('assets/images/rmn_partner.svg')}}" alt="Logo"/>
        </div>
    </div>

    <div class="why-us-container shadow container-spacing">
        <div class="main-text">
            <h1>Dlaczego warto?</h1>

            <p>
                Tworzymy innowacyjne, pionierskie na polskim rynku usługi reklamowe
                oparte o przestrzenie najlepszych retailerów, wzmocnione siłą Onetu i
                serwisów RAS Polska. Sprawdź, jak to działa:
            </p>
        </div>

        <div class="reason-container">
            <div class="left-side">
                <img src="{{asset('assets/images/benefits_first.svg')}}"/>
            </div>
            <div class="right-side">
                <h4>Zarabiaj dodatkowo na powierzchniach swojego sklepu</h4>

                <p>
                    Retail Media Network to nowy kanał przychodowy dla Twojego biznesu.
                    Obecność w sieci zapewni Ci dodatkowe budżety od reklamodawców. Co
                    ważne, ruch z kampanii pozostaje w Twoim sklepie.
                </p>
            </div>
        </div>

        <div class="reason-container">
            <div class="left-side">
                <img src="{{asset('assets/images/benefits_two.svg')}}"/>
            </div>
            <div class="right-side">
                <h4>Zyskaj kaloryczny ruch z powierzchni RASP</h4>

                <p>
                    Retail Media Network to synergia powierzchni reklamowych sklepów i
                    RASP. W naszych serwisach emitujemy formaty reklamowe, które kierują
                    użytkowników do sklepów obecnych w sieci. Dla Ciebie to źródło
                    wartościowego ruchu.
                </p>
            </div>
        </div>

        <div class="reason-container">
            <div class="left-side">
                <img src="{{asset('assets/images/benefits_three.svg')}}"/>
            </div>
            <div class="right-side">
                <h4>Korzystaj z nowoczesnego panelu Onet Ads</h4>

                <p>
                    Swoją obecnością i aktywnością w Retail Media Network zarządzisz za
                    pomocą Onet Ads. To przyjazny i intuicyjny panel self-service.
                    Zapewniamy przejrzysty ekosystem, wystandaryzowane workflow, jasne
                    zasady współpracy i bieżące wsparcie, gdy tylko go potrzebujesz.
                </p>
            </div>
        </div>

        <div class="reason-container">
            <div class="left-side">
                <img src="{{asset('assets/images/benefits_four.svg')}}"/>
            </div>
            <div class="right-side">
                <h4>Przekonaj się o zaletach RMN dzięki bezpłatnej integracji</h4>

                <p>
                    Integracja z Retail Media Network nic nie kosztuje, nie wymagamy też
                    żadnej formy wyłączności biznesowej. Na potrzeby integracji
                    dostarczymy Ci niezbędną dokumentację i zapewnimy wsparcie
                    ekspertów.
                </p>
            </div>
        </div>
    </div>

    <div class="join-us-container container-spacing shadow">
        <div class="left-side">
            <h1>
                Już dziś dołącz do <br/>
                Retail Media Network <br/>
                w Onet Ads
            </h1>

            <button onclick="openLinks('{{route('onetads.thanks')}}', 'https://konto.onet.pl/signin')">
                <img src="{{asset('assets/icons/send_black.svg')}}" alt="Send icon"/>
                Przejdź do panelu Onet Ads
            </button>

            <div class="new-tab">
                <img
                    src="{{asset('assets/icons/new_tab.svg')}}"
                    alt="New Tab icon"
                    style="fill: red"
                />
                <span>Zostanie otworzona nowa zakładka</span>
            </div>
        </div>

        <div class="right-side">
            <img src="{{asset('assets/images/join_us.svg')}}" alt="Logo"/>
        </div>
    </div>
@endsection
@push('javascript')
    <script src="{{asset('js/scripts.js')}}"></script>
@endpush
