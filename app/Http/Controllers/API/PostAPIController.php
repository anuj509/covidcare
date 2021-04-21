<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePostAPIRequest;
use App\Http\Requests\API\UpdatePostAPIRequest;
use App\Http\Requests\API\UpdatePostStatusAPIRequest;
use App\Models\Post;
use App\Repositories\PostRepository;
use App\Repositories\CovidCareUserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\PostResource;
use Response;
use Carbon\Carbon;
/**
 * Class PostController
 * @package App\Http\Controllers\API
 */

class PostAPIController extends AppBaseController
{
    /** @var  PostRepository */
    private $postRepository;

    public function __construct(PostRepository $postRepo,CovidCareUserRepository $covidCareUserRepo)
    {
        $this->postRepository = $postRepo;
        $this->covidCareUserRepository = $covidCareUserRepo;
    }

    /**
     * Display a listing of the Post.
     * GET|HEAD /posts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $searchfields = array('name',
        'dob',
        'gender',
        'blood_group',
        'oxygen_level',
        'poc_name',
        'poc_phone',
        'patient_currently_admitted_at',
        'ward',
        'requirement',
        'oxygen',
        'plasma',
        'medicines',
        'bed',
        'other');
        $posts = $this->postRepository->findBy(
            ['closed_at'=>NULL,'requirement'=>'like%' . $request->get('category') . '%'],
            // $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        )->where($searchfields[0],'like','%'.$request->get('keyword').'%')->orderBy('id', 'DESC')->get();
        $i = 1;
        while(count($posts)==0 && $i < count($searchfields)){
            $posts = $this->postRepository->findBy(
                ["category"=>$request->get('category')],
                // $request->except(['skip', 'limit']),
                $request->get('skip'),
                $request->get('limit')
            )->where($searchfields[$i],'like','%'.$request->get('keyword').'%')->orderBy('id', 'DESC')->get();
            $i++;
        }
        $posts = $posts->toArray();
        foreach ($posts as $key => $post) {
            $params = array('oxygen','plasma','medicines','bed');
            foreach ($params as $k => $param) {
                $posts[$key][$param] = json_decode($post[$param],true);
            }
            $posts[$key]['requirement']=explode(',',$post['requirement']);
        }
        return $this->sendResponse($posts, 'Posts retrieved successfully');
    }

    /**
     * Store a newly created Post in storage.
     * POST /posts
     *
     * @param CreatePostAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePostAPIRequest $request)
    {
        $input = $request->all();
        $input['requirement'] = implode(",",$input['requirement']);
        if(array_key_exists('oxygen',$input)){
            $input['oxygen'] = json_encode($input['oxygen']);
        }else{
            $input['oxygen'] = json_encode(array());
        }
        if(array_key_exists('plasma',$input)){
            $input['plasma'] = json_encode($input['plasma']);
        }else{
            $input['plasma'] = json_encode(array());
        }
        if(array_key_exists('medicines',$input)){
            if(count($input['medicines'])==2){
                $input['medicines'] = array_merge($input['medicines'][0],$input['medicines'][1]);
            }
            $input['medicines'] = json_encode($input['medicines']);
        }else{
            $input['medicines'] = json_encode(array());
        }
        if(array_key_exists('bed',$input)){
            $input['bed'] = json_encode($input['bed']);
        }else{
            $input['bed'] = json_encode(array());
        }
        if(!array_key_exists('other',$input)){
            $input['other'] = "";
        }else if(!$input['other']){
            $input['other'] = "";
        }
        if(!array_key_exists('ward',$input)){
            $input['ward'] = "NA";
        }
        $input['marked_by_user']=false;
        $input['comment']="";
        // dd($input);
        $post = $this->postRepository->create($input);
        
        return $this->sendResponse(new PostResource($post), 'Post saved successfully');
    }

    /**
     * Display the specified Post.
     * GET|HEAD /posts/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Post $post */
        $post = $this->postRepository->find($id);

        if (empty($post)) {
            return $this->sendError('Post not found');
        }

        return $this->sendResponse(new PostResource($post), 'Post retrieved successfully');
    }

    /**
     * Update the specified Post in storage.
     * PUT/PATCH /posts/{id}
     *
     * @param int $id
     * @param UpdatePostAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePostAPIRequest $request)
    {
        $input = $request->all();

        /** @var Post $post */
        $post = $this->postRepository->find($id);

        if (empty($post)) {
            return $this->sendError('Post not found');
        }

        $post = $this->postRepository->update($input, $id);

        return $this->sendResponse(new PostResource($post), 'Post updated successfully');
    }

    /**
     * Remove the specified Post from storage.
     * DELETE /posts/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Post $post */
        $post = $this->postRepository->find($id);

        if (empty($post)) {
            return $this->sendError('Post not found');
        }

        $post->delete();

        return $this->sendSuccess('Post deleted successfully');
    }

    public function userPosts($userid)
    {
        /** @var Post $post */
        $user = $this->covidCareUserRepository->findBy(['id'=>$userid])->with(['posts'])->first();

        if (empty($user)) {
            return $this->sendError('User not found');
        }
        if(!empty($user)){
            $user = $user->toArray();
            foreach ($user['posts'] as $key => $post) {
                $params = array('oxygen','plasma','medicines','bed');
                foreach ($params as $k => $param) {
                    $user['posts'][$key][$param] = json_decode($post[$param],true);
                }
                $user['posts'][$key]['requirement']=explode(',',$post['requirement']);
            }
        }
        return $this->sendResponse($user, 'User Post retrieved successfully');
    }

    public function updateUserPost($userid, UpdatePostStatusAPIRequest $request)
    {
        /** @var Post $post */
        $post = $this->postRepository->findBy(['id'=>$request['post_id'],'user_id'=>$userid])->first();

        if (empty($post)) {
            return $this->sendError('Post not found');
        }
        $post->closed_at = Carbon::now();
        $post->marked_by_user = true;
        if($request['comment']){
            $post->comment = $request['comment'];
        }
        $post->save();
        return $this->sendSuccess('Post updateded successfully');
    }

    public function requirementStats(Request $request)
    {   $counter = array('oxygen'=>0,'bed'=>0,'plasma'=>0,'medicines'=>0);
        foreach ($counter as $key => $value) {
            $counter[$key] = $this->postRepository->findBy(
                ['closed_at'=>NULL]
                // $request->except(['skip', 'limit']),
                // $request->get('skip'),
                // $request->get('limit')
            )->where('requirement','like','%'.$key.'%')->count();
        }

        return $this->sendResponse($counter, 'Stats retrieved successfully');
    }
}
