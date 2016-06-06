<?php

Route::get('a/{hash}.{extension}', 'Image\ViewImagesController@album');
Route::get('i/{hash}.{extension}', 'Image\ViewImagesController@image');
Route::get('t/{hash}.{extension}', 'Image\ViewImagesController@thumbnail');