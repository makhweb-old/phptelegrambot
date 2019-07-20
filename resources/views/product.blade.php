<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <meta property="og:site_name" content="KFC 🍔" />
        <meta
            name="telegram:channel"
            content="{{ config('app.telegram_channel') }}"
        />
        <meta property="og:image" content="{{$product->photo_url}}" />
        <!-- Scripts -->

        <link
            href="https://fonts.googleapis.com/css?family=Nunito"
            rel="stylesheet"
        />

        <title>{{$product->getWithTranslations('name',request()->route('locale'))}}</title>

        <!-- Styles -->
        <link
            rel="stylesheet"
            href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
            integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
            crossorigin="anonymous"
        />
    </head>
    <body>
        <div id="app">
            <main class="py-4">
                <div class="container">
                    <div class="product">
                        <main role="main" class="container">
                            <h1>
                                {{$product->getWithTranslations('name',request()->route('locale'))}}
                            </h1>
                            <img
                                class="d-block mx-auto mw-100"
                                src="{{$product->photo_url}}"
                                alt=""
                            />
                            <p class="lead">
                                {{$product->getWithTranslations('description',request()->route('locale'))}}
                            </p>
                        </main>

                        <footer class="footer">
                            <div class="container">
                                <span class="text-muted"
                                    ><a
                                        href="https://t.me/{{
                                            trim(
                                                config('app.telegram_channel'),
                                                '@'
                                            )
                                        }}"
                                        >Telegram channel</a
                                    ></span
                                >
                            </div>
                        </footer>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
