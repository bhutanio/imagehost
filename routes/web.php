<?php

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'my'], function () {
        Route::get('/', 'My\MyImagesController@index');
        Route::get('albums', 'My\MyImagesController@albums');
        Route::get('images', 'My\MyImagesController@images');
    });

    Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
        Route::get('/', 'Admin\AdminImagesController@index');
        Route::get('albums', 'Admin\AdminImagesController@albums');
        Route::get('images', 'Admin\AdminImagesController@images');
    });
});

Auth::routes();

Route::get('a/{hash}', 'Image\ViewImagesController@album');
Route::get('i/{hash}', 'Image\ViewImagesController@image');

Route::post('image/upload', 'Image\UploadImageController@ajaxUpload')->middleware(['ajax']);
Route::delete('image/delete', 'Image\UploadImageController@ajaxDelete')->middleware(['ajax']);

Route::post('image/create', 'Image\UploadImageController@create');

Route::get('/', function () {
    meta()->setMeta('ImageZ', 'ImageZ - Free and Secure Image Hosting & Photo Sharing');

    return view('home');
});
