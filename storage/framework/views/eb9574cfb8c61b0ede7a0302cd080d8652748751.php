<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />

        <meta property="og:site_name" content="KFC 🍔" />
        <meta
            name="telegram:channel"
            content="<?php echo e(config('app.telegram_channel')); ?>"
        />
        <meta property="og:image" content="<?php echo e($product->photo_url); ?>" />
        <!-- Scripts -->

        <link
            href="https://fonts.googleapis.com/css?family=Nunito"
            rel="stylesheet"
        />

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
                                <?php echo e($product->getWithTranslations('name',request()->route('locale'))); ?>

                            </h1>
                            <img
                                class="d-block mx-auto mw-100"
                                src="<?php echo e($product->photo_url); ?>"
                                alt=""
                            />
                            <p class="lead">
                                <?php echo e($product->getWithTranslations('description',request()->route('locale'))); ?>

                            </p>
                        </main>

                        <footer class="footer">
                            <div class="container">
                                <span class="text-muted"
                                    ><a
                                        href="https://t.me/<?php echo e(trim(
                                                config('app.telegram_channel'),
                                                '@'
                                            )); ?>"
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
<?php /**PATH C:\OSPanel\domains\localhost\resources\views/product.blade.php ENDPATH**/ ?>