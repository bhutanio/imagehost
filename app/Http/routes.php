<?php

Route::group(['prefix' => 'auth'], function () {
    Route::auth();
});

Route::get('a/{hash}', 'Image\ViewImagesController@album');
Route::get('i/{hash}', 'Image\ViewImagesController@image');

Route::post('image/upload', 'Image\UploadImageController@ajaxUpload')->middleware(['ajax']);
Route::delete('image/delete/{id}', 'Image\UploadImageController@ajaxDelete')->middleware(['ajax']);

Route::post('image/create', 'Image\UploadImageController@create');

Route::get('/', function () {
    meta()->setMeta('ImageZ', 'ImageZ - Free and Secure Image Hosting & Photo Sharing');
    return view('home');
});
