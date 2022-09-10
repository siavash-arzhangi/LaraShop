<?php

namespace App\Http\Controllers\Api;

use Trez\RayganSms\Facades\RayganSms;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{

    public function register(Request $request) {
        $request = $request->all();
        $validator = Validator::make($request,[
            'name' => 'required|max:50',
            'username' => 'required|unique:users',
            'password' => 'required'
        ]);

        if ($validator->fails())
            return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

        $user = User::create([
            'user_id' => uuid('user'),
            'name' => $request['name'],
            'username' => $request['username'],
            'password' => Hash::make($request['password']),
            'information' => '{}',
            'role' => config("app.auth.defaults.role"),
            'level' => config("app.auth.defaults.level")
        ]);
        return response()->json($user, 200);
    }

    public function login(Request $request) {
        $request = $request->all();
        $validator = Validator::make($request,[
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails())
            return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

        $auth = [
            'username' => $request['username'],
            'password' => $request['password']
        ];

        if (!auth()->attempt($auth))
            return response()->json(['status' => responseCode(401)]);

        // TODO: remember_token

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response(['user' => auth()->user(), 'access_token' => $accessToken], 200);
    }

    public function index(Request $request) {
        $request = $request->header();
        $auth = Auth::user();

        if (isAdmin($auth)) {
            $data = User::paginate((integer) pagination($request, 'users'));
            return response()->json($data, 200);
        }else {
            return response()->json(['status' => responseCode(401)]);
        }
    }

    public function create(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        if (isAdmin($auth)) {
            $validator = Validator::make($request,[
                'name' => 'required|max:50',
                'username' => 'required|unique:users',
                'password' => 'required',
                'role' => Rule::in(array_keys(config("app.auth.roles")))
            ]);

            if ($validator->fails())
                return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

            $user = User::create([
                'user_id' => uuid('user'),
                'name' => $request['name'],
                'username' => $request['username'],
                'password' => Hash::make($request['password']),
                'information' => '{}',
                'role' => $request['role'] !== null ? $request['role'] : config("app.auth.defaults.role"),
                'level' => $request['level'] !== null ? $request['level'] : config("app.auth.defaults.level"),
            ]);
            return response()->json($user, 200);
        }else {
            return response()->json(['status' => responseCode(401)]);
        }
    }

    public function read(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        if (isAdmin($auth)) {

            $user = User::where('user_id', $request['userid']);
            if ($user->exists()) {
                $data = $user->get();
                return response()->json($data, 200);
            }else {
                return response()->json(['status' => responseCode(404)]);
            }

        }else {
            return response()->json($auth, 200);
        }
    }

    public function update(Request $request) {
        // actions: change pass, level toggle
    }

    public function delete(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        if (isAdmin($auth)) {

            $user = User::where('user_id', $request['userid']);
            if ($user->exists()) {
                
                try {
                    $user->delete();
                    return response()->json(['status' => responseCode(200)]);
                } catch (\Throwable $error) {
                    return response()->json(['status' => responseCode(500), 'errors' => $error]);
                }

            }else {
                return response()->json(['status' => responseCode(404)]);
            }

        }else {
            return response()->json(['status' => responseCode(401)]);
        }
    }

    public function logout(Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json(['status' => responseCode(200)]);
    }
}