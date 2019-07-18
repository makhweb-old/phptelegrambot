<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/product/{locale}/{id}', function ($locale, $id) {
    return view('product', [
        'product' => App\Product::whereLang($locale)
            ->whereId($id)
            ->firstOrFail()
    ]);
});

Route::match(['get', 'post'], 'test', function () {
    $madeline = new danog\MadelineProto\API(
        storage_path('framework\sessions\TELEGRAM_SESSION_FILE')
    );

    $messages = $madeline->messages->getHistory([
        'peer' => config('app.telegram_channel'),
        'offset_id' => 0,
        'offset_date' => 0,
        'add_offset' => 0,
        'limit' => 100,
        'max_id' => 0,
        'min_id' => 0,
        'hash' => 0
    ]);
    dd($messages['messages']);
});
Route::any('{all}', function () {
    return view('app');
})->where(['all' => '.*']);
