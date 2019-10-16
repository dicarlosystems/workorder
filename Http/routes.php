<?php

Route::group(['middleware' => ['web', 'lookup:user', 'auth:user'], 'namespace' => 'Modules\WorkOrder\Http\Controllers'], function()
{
    Route::resource('workorders', 'WorkOrderController');
    Route::post('workorders/bulk', 'WorkOrderController@bulk');
    Route::get('api/workorders', 'WorkOrderController@datatable');
});

Route::group(['middleware' => 'api', 'namespace' => 'Modules\WorkOrder\Http\ApiControllers', 'prefix' => 'api/v1'], function()
{
    Route::resource('workorders', 'WorkOrderApiController');
});
