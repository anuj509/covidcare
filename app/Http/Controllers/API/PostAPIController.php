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
// use App\Jobs\SaveToSheetJob;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
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
        if($request->get('category') == "beds"){
            $category = "bed";
        }else{
            $category = $request->get('category');
        }
        $posts = $this->postRepository->findBy(
            ['closed_at'=>NULL],
            // $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        )->where('requirement','like','%' . $category . '%')->where($searchfields[0],'like','%'.$request->get('keyword').'%')->orderBy('id', 'DESC')->get();
        $i = 1;
        while(count($posts)==0 && $i < count($searchfields)){
            $posts = $this->postRepository->findBy(
                ['closed_at'=>NULL],
                // $request->except(['skip', 'limit']),
                $request->get('skip'),
                $request->get('limit')
            )->where('requirement','like','%' . $category . '%')->where($searchfields[$i],'like','%'.$request->get('keyword').'%')->orderBy('id', 'DESC')->get();
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
            $input['oxygen'] = json_encode(array(array("number"=>null)));
        }
        if(array_key_exists('plasma',$input)){
            $input['plasma'] = json_encode($input['plasma']);
        }else{
            $input['plasma'] = json_encode(array(array("units"=>null)));
        }
        if(array_key_exists('medicines',$input)){
            if(count($input['medicines'])==2){
                $input['medicines'] = [array_merge($input['medicines'][0],$input['medicines'][1])];
            }
            $input['medicines'] = json_encode($input['medicines']);
        }else{
            $input['medicines'] = json_encode(array(array("name"=>null,"count"=>null)));
        }
        if(array_key_exists('bed',$input)){
            $input['bed'] = json_encode($input['bed']);
        }else{
            $input['bed'] = json_encode(array(array("count"=>null)));
        }
        if(!array_key_exists('other',$input)){
            $input['other'] = "";
        }else if(!$input['other']){
            $input['other'] = "";
        }
        if(!array_key_exists('ward',$input)){
            $input['ward'] = "NA";
        }else if(array_key_exists('ward',$input) && ($input['ward']==null || $input['ward']=="")){
            $input['ward'] = "NA";
        }
        $input['marked_by_user']=false;
        $input['comment']="";
        // dd($input);
        $post = $this->postRepository->create($input);

        SaveToSheetJob::dispatch($this->postRepository,$post->id);
        $client = $this->getClient();
        $this->service = new Google_Service_Sheets($client);
        $values = [
            [
                $post['name'],
                $post['dob'],
                $post['gender'],
                $post['blood_group'],
                $post['oxygen_level'],
                $post['poc_name'],
                $post['poc_phone'],
                $post['patient_currently_admitted_at'],
                $post['ward'],
                $post['requirement'],
                $post['oxygen'],
                $post['plasma'],
                $post['medicines'],
                $post['bed'],
                $post['other'],
                $post['created_at']
            ],
        ];
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $valueInputOption = "RAW";
        $params = [
            'valueInputOption' => $valueInputOption
        ];
        $result = $this->service->spreadsheets_values->append(env('GOOGLE_SHEET_ID'), 'Sheet1', $body, $params);
        
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
        $user['posts'] = array_reverse($user['posts']);
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

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    public function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('CovidCare Data Ingestor');
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
        $client->setAuthConfig(storage_path('credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('none');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = storage_path('token.json');
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }
}
