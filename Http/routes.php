<?php

Route::group(['middleware' => ['web', 'lookup:user', 'auth:user'], 'namespace' => 'Modules\WorkOrder\Http\Controllers'], function()
{
    Route::resource('workorder', 'WorkOrderController');
    Route::post('workorder/bulk', 'WorkOrderController@bulk');
    Route::get('api/workorder', 'WorkOrderController@datatable');
});

Route::group(['middleware' => 'api', 'namespace' => 'Modules\WorkOrder\Http\ApiControllers', 'prefix' => 'api/v1'], function()
{
    Route::resource('workorder', 'WorkOrderApiController');
});
