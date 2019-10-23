<?php

Route::group(['middleware' => ['web', 'lookup:user', 'auth:user'], 'namespace' => 'Modules\WorkOrder\Http\Controllers'], function()
{
    Route::get('workorder', function() {
        return redirect('workorders');
    });
    
    Route::resource('workorders', 'WorkOrderController');
    Route::post('workorders/bulk', 'WorkOrderController@bulk');
    Route::post('workorders/{workorder}/addnote', 'WorkOrderController@addNote')->name('workorders.addnote');
    Route::get('api/workorders', 'WorkOrderController@datatable');

    Route::get('settings/workorder', 'WorkOrderController@showSettings');
    Route::post('settings/workorder', 'WorkOrderController@saveSettings');
});

Route::group(['middleware' => 'api', 'namespace' => 'Modules\WorkOrder\Http\ApiControllers', 'prefix' => 'api/v1'], function()
{
    Route::resource('workorders', 'WorkOrderApiController');
});
