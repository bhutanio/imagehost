<?php

Route::get('a/{hash}', 'Image\ViewImagesController@album');
Route::get('i/{hash}', 'Image\ViewImagesController@image');
Route::get('t/{hash}', 'Image\ViewImagesController@thumbnail');
