<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCovidCareUserAPIRequest;
use App\Http\Requests\API\UpdateCovidCareUserAPIRequest;
use App\Models\CovidCareUser;
use App\Repositories\CovidCareUserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\CovidCareUserResource;
use Response;

/**
 * Class CovidCareUserController
 * @package App\Http\Controllers\API
 */

class CovidCareUserAPIController extends AppBaseController
{
    /** @var  CovidCareUserRepository */
    private $covidCareUserRepository;

    public function __construct(CovidCareUserRepository $covidCareUserRepo)
    {
        $this->covidCareUserRepository = $covidCareUserRepo;
    }

    /**
     * Display a listing of the CovidCareUser.
     * GET|HEAD /covidCareUsers
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $covidCareUsers = $this->covidCareUserRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(CovidCareUserResource::collection($covidCareUsers), 'Covid Care Users retrieved successfully');
    }

    /**
     * Store a newly created CovidCareUser in storage.
     * POST /covidCareUsers
     *
     * @param CreateCovidCareUserAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCovidCareUserAPIRequest $request)
    {
        $input = $request->all();
        $covidCareUser = $this->covidCareUserRepository->findBy(['name'=>$input['name'],'phone'=>$input['phone']])->first();
        if(empty($covidCareUser)){
            $covidCareUser = $this->covidCareUserRepository->create($input);
        }
        
        
        return $this->sendResponse(new CovidCareUserResource($covidCareUser), 'Covid Care User saved successfully');
    }

    /**
     * Display the specified CovidCareUser.
     * GET|HEAD /covidCareUsers/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CovidCareUser $covidCareUser */
        $covidCareUser = $this->covidCareUserRepository->find($id);

        if (empty($covidCareUser)) {
            return $this->sendError('Covid Care User not found');
        }

        return $this->sendResponse(new CovidCareUserResource($covidCareUser), 'Covid Care User retrieved successfully');
    }

    /**
     * Update the specified CovidCareUser in storage.
     * PUT/PATCH /covidCareUsers/{id}
     *
     * @param int $id
     * @param UpdateCovidCareUserAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCovidCareUserAPIRequest $request)
    {
        $input = $request->all();

        /** @var CovidCareUser $covidCareUser */
        $covidCareUser = $this->covidCareUserRepository->find($id);

        if (empty($covidCareUser)) {
            return $this->sendError('Covid Care User not found');
        }

        $covidCareUser = $this->covidCareUserRepository->update($input, $id);

        return $this->sendResponse(new CovidCareUserResource($covidCareUser), 'CovidCareUser updated successfully');
    }

    /**
     * Remove the specified CovidCareUser from storage.
     * DELETE /covidCareUsers/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CovidCareUser $covidCareUser */
        $covidCareUser = $this->covidCareUserRepository->find($id);

        if (empty($covidCareUser)) {
            return $this->sendError('Covid Care User not found');
        }

        $covidCareUser->delete();

        return $this->sendSuccess('Covid Care User deleted successfully');
    }
}
