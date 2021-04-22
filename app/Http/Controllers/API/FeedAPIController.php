<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFeedAPIRequest;
use App\Http\Requests\API\UpdateFeedAPIRequest;
use App\Models\Feed;
use App\Repositories\FeedRepository;
use App\Repositories\CovidCareUserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\FeedResource;
use Response;

/**
 * Class FeedController
 * @package App\Http\Controllers\API
 */

class FeedAPIController extends AppBaseController
{
    /** @var  FeedRepository */
    private $feedRepository;

    public function __construct(FeedRepository $feedRepo, CovidCareUserRepository $covidCareUserRepo)
    {
        $this->feedRepository = $feedRepo;
        $this->covidCareUserRepository = $covidCareUserRepo;
    }

    /**
     * Display a listing of the Feed.
     * GET|HEAD /feeds
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $feeds = $this->feedRepository->allQuery(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        )->orderBy('id','DESC')->get();

        return $this->sendResponse(FeedResource::collection($feeds), 'Feeds retrieved successfully');
    }

    /**
     * Store a newly created Feed in storage.
     * POST /feeds
     *
     * @param CreateFeedAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateFeedAPIRequest $request)
    {
        $input = $request->all();
        $covidCareUser = $this->covidCareUserRepository->findBy(['id'=>$input['user_id']])->first();
        $input['name']=$covidCareUser->name;
        $feed = $this->feedRepository->create($input);

        return $this->sendResponse(new FeedResource($feed), 'Feed saved successfully');
    }

    /**
     * Display the specified Feed.
     * GET|HEAD /feeds/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Feed $feed */
        $feed = $this->feedRepository->find($id);

        if (empty($feed)) {
            return $this->sendError('Feed not found');
        }

        return $this->sendResponse(new FeedResource($feed), 'Feed retrieved successfully');
    }

    /**
     * Update the specified Feed in storage.
     * PUT/PATCH /feeds/{id}
     *
     * @param int $id
     * @param UpdateFeedAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFeedAPIRequest $request)
    {
        $input = $request->all();

        /** @var Feed $feed */
        $feed = $this->feedRepository->find($id);

        if (empty($feed)) {
            return $this->sendError('Feed not found');
        }

        $feed = $this->feedRepository->update($input, $id);

        return $this->sendResponse(new FeedResource($feed), 'Feed updated successfully');
    }

    /**
     * Remove the specified Feed from storage.
     * DELETE /feeds/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Feed $feed */
        $feed = $this->feedRepository->find($id);

        if (empty($feed)) {
            return $this->sendError('Feed not found');
        }

        $feed->delete();

        return $this->sendSuccess('Feed deleted successfully');
    }
}
