<?php

namespace Modules\WorkOrder\Http\ApiControllers;

use App\Http\Controllers\BaseAPIController;
use Modules\Workorder\Repositories\WorkorderRepository;
use Modules\Workorder\Http\Requests\WorkorderRequest;
use Modules\Workorder\Http\Requests\CreateWorkorderRequest;
use Modules\Workorder\Http\Requests\UpdateWorkorderRequest;

class WorkorderApiController extends BaseAPIController
{
    protected $WorkorderRepo;
    protected $entityType = 'workorder';

    public function __construct(WorkorderRepository $workorderRepo)
    {
        parent::__construct();

        $this->workorderRepo = $workorderRepo;
    }

    /**
     * @SWG\Get(
     *   path="/workorder",
     *   summary="List workorder",
     *   operationId="listWorkorders",
     *   tags={"workorder"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of workorder",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Workorder"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $data = $this->workorderRepo->all();

        return $this->listResponse($data);
    }

    /**
     * @SWG\Get(
     *   path="/workorder/{workorder_id}",
     *   summary="Individual Workorder",
     *   operationId="getWorkorder",
     *   tags={"workorder"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="workorder_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single workorder",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Workorder"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function show(WorkorderRequest $request)
    {
        return $this->itemResponse($request->entity());
    }




    /**
     * @SWG\Post(
     *   path="/workorder",
     *   summary="Create a workorder",
     *   operationId="createWorkorder",
     *   tags={"workorder"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="workorder",
     *     @SWG\Schema(ref="#/definitions/Workorder")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New workorder",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Workorder"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function store(CreateWorkorderRequest $request)
    {
        $workorder = $this->workorderRepo->save($request->input());

        return $this->itemResponse($workorder);
    }

    /**
     * @SWG\Put(
     *   path="/workorder/{workorder_id}",
     *   summary="Update a workorder",
     *   operationId="updateWorkorder",
     *   tags={"workorder"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="workorder_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="workorder",
     *     @SWG\Schema(ref="#/definitions/Workorder")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated workorder",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Workorder"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function update(UpdateWorkorderRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $workorder = $this->workorderRepo->save($request->input(), $request->entity());

        return $this->itemResponse($workorder);
    }


    /**
     * @SWG\Delete(
     *   path="/workorder/{workorder_id}",
     *   summary="Delete a workorder",
     *   operationId="deleteWorkorder",
     *   tags={"workorder"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="workorder_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted workorder",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Workorder"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function destroy(UpdateWorkorderRequest $request)
    {
        $workorder = $request->entity();

        $this->workorderRepo->delete($workorder);

        return $this->itemResponse($workorder);
    }

}
