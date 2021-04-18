<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\AppBaseController;

class AuthController extends AppBaseController
{
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = Validator::make($request->all(), [
                'first_name' => 'required|max:55',
                'last_name' => 'required|max:55',
                'phone' => 'required|min:10|max:10|unique:users',
                'email' => 'email',
                'birthdate' => 'required|date',
                'password' => 'required|confirmed'
            ]);
    
            if ($validatedData->fails()) {
                DB::rollback();
                return $this->sendError($validatedData->messages()->first());
                // return response([ 'success' => false, 'message' => ]);
            }
            $data = $request->all();
            $data['password'] = bcrypt($request->password);
            $user = User::create($data);

        }catch (\Exception $e) {
            DB::rollback();
            // return response([ 'success' => false, 'message' => ]);
            return $this->sendError($e->getMessage());

        }
        DB::commit();
        $accessToken = $user->createToken(env('TOKEN_KEY'))->accessToken;

        return $this->sendResponse(['user' => $user, 'access_token' => $accessToken], 'Registration Success');
        // return response([ 'success' => true, 'message' => 'Registration Success' ]);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return $this->sendError('Invalid Credentials');
            // return response(['message' => ]);
        }

        $accessToken = auth()->user()->createToken(env('TOKEN_KEY'))->accessToken;

        // return response();
        return $this->sendResponse(['user' => auth()->user(), 'access_token' => $accessToken], 'Login Success');
    }
}
