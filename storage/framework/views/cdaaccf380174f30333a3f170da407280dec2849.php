<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="author" content="Vitaly Kasymov">
        <meta name="description" content="">

        <title><?php echo e(config('app.name')); ?></title>

        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons" rel="stylesheet">
        <link href="<?php echo e(mix('/css/app.css')); ?>" rel="stylesheet">

        <script>
            window.Laravel = <?php echo json_encode([
                'siteName' => config('app.name'),
                'siteUrl' => config('app.url'),
                'apiUrl' => config('app.url') . '/api'
            ]); ?>;
        </script>
    </head>
    <body>
        <div id="app"></div>

        <script src="<?php echo e(mix('/js/app.js')); ?>"></script>
    </body>
</html>
<?php /**PATH C:\OSPanel\domains\localhost\resources\views/app.blade.php ENDPATH**/ ?>