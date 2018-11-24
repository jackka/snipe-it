<?php

# Asset costcenter Management
Route::group([ 'prefix' => 'costcenters', 'middleware' => ['auth'] ], function () {

    Route::get('{costcenterId}/clone', [ 'as' => 'clone/costcenter', 'uses' => 'AssetCostcentersController@getClone' ]);
    Route::post('{costcenterId}/clone', 'AssetCostcentersController@postCreate');
    Route::get('{costcenterId}/view', [ 'as' => 'view/costcenter', 'uses' => 'AssetCostcentersController@getView' ]);
    Route::get('{costcenterID}/restore', [ 'as' => 'restore/costcenter', 'uses' => 'AssetCostcentersController@getRestore', 'middleware' => ['authorize:superuser'] ]);
    Route::get('{costcenterId}/custom_fields', ['as' => 'custom_fields/costcenter','uses' => 'AssetCostcentersController@getCustomFields']);
    Route::post('bulkedit', ['as' => 'costcenters.bulkedit.index','uses' => 'BulkAssetCostcentersController@edit']);
    Route::post('bulksave', ['as' => 'costcenters.bulkedit.store','uses' => 'BulkAssetCostcentersController@update']);
    Route::post('bulkdelete', ['as' => 'costcenters.bulkdelete.store','uses' => 'BulkAssetCostcentersController@destroy']);
});

Route::resource('costcenters', 'AssetCostcentersController', [
    'middleware' => ['auth'],
    'parameters' => ['costcenter' => 'costcenter_id']
]);
