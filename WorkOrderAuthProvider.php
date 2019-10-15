<?php

namespace Modules\WorkOrder\;

use App\Providers\AuthServiceProvider;

class WorkorderAuthProvider extends AuthServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \Modules\Workorder\Models\Workorder::class => \Modules\Workorder\Policies\WorkorderPolicy::class,
    ];
}
