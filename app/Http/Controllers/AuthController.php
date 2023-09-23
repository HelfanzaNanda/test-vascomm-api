<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Helpers\MailHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\StringHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|numeric|min_digits:10|unique:users,phone',
        ]);
        if ($validator->fails()) {
            // return response()->json($validator->errors(), 422);
            return response()->json(ResponseHelper::warning( validations: $validator->errors(), code: 422), 422);
        }

        try {
            $password = StringHelper::getRandomString(8);
            $params = $validator->validated();
            $params['password'] = Hash::make($password);
            $params['status'] = StatusEnum::NOT_ACTIVE->value;
            $user = User::create($params);

            $user->assignRole('user');

            $mail = $user->email;

            $title = "welcome to join " . env('APP_NAME');
            $content = "here is your login account, username = $mail, password = $password";
            MailHelper::send($mail, $title, $content);

            return response()->json(ResponseHelper::success(), 200);
        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }

    }

    public function login(Request $request)
    {

        $email = $request->email;
        $user = User::query()
        ->where('status', StatusEnum::ACTIVE->value)
        ->where(function($q) use($email) {
            $q->orWhereRaw('lower(email) = lower(?)', [$email])
                ->orWhereRaw('lower(phone) = lower(?)', [$email]);
        })
        ->first();

        if (!$user) {
            return response()->json(ResponseHelper::warning( message: 'Unauthorized', code: 401), 401);
        }


        $credentials = [
            'email' => $user->email,
            'password' => $request->password,
        ];

        // $password = $request->password;
        if(!Auth::attempt($credentials)){
        // if(!$user || !Hash::check($password, $user->password)){
            return response()->json(ResponseHelper::warning( message: 'Unauthorized', code: 401), 401);
        }
        $user = Auth::user();
        $role = $user->roles()->first();
        $user['role'] = $role;
        $accessToken = $user->createToken('nApp')->accessToken;
        $data = [
            'user' => $user,
            'jwt' => [
                'access_token' => $accessToken,
                'token_type' => 'bearer',
                'expires_in' => 60 * 60
            ]
        ];
        return response()->json(ResponseHelper::success(data: $data), 200);
    }
}
