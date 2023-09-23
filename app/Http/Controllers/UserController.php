<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Helpers\DatatableHelper;
use App\Helpers\FileHelper;
use App\Helpers\MailHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\StringHelper;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|numeric|min_digits:10|unique:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json(ResponseHelper::warning( validations: $validator->errors(), code: 422), 422);
        }

        DB::beginTransaction();
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
            DB::commit();
            return response()->json(ResponseHelper::success(), 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }


    public function find($id)
    {
        $user = User::whereId($id)->first();
        if (!$user) {
            return response()->json(ResponseHelper::warning( message: 'user not found', code: 404), 404);
        }
        return response()->json(ResponseHelper::success(data: $user), 200);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'required|numeric|min_digits:10|unique:users,phone,'.$id,
        ]);

        if ($validator->fails()) {
            return response()->json(ResponseHelper::warning( validations: $validator->errors(), code: 422), 422);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(ResponseHelper::warning( message: 'user not found', code: 404), 404);
        }


        try {
            $params = $validator->validated();
            $user->update($params);
            return response()->json(ResponseHelper::success(), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }

    public function approve($id, Request $request)
    {

        $user = User::find($id);
        if (!$user) {
            return response()->json(ResponseHelper::warning( message: 'user not found', code: 404), 404);
        }


        try {
            $user->update([
                'status' => StatusEnum::ACTIVE->value
            ]);
            return response()->json(ResponseHelper::success(), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }

    public function delete($id)
    {
        $user = User::whereId($id)->first();
        if (!$user) {
            return response()->json(ResponseHelper::warning( message: 'user not found', code: 404), 404);
        }

        try {
            $user->delete();
            return response()->json(ResponseHelper::success(), 200);
        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }

    public function datatables(Request $request)
    {
        $columns = ['id', 'name', 'phone', 'email', 'status', 'created_at'];
        $start = $request->get("start");
		$length = $request->get("length");
		$order = $request->get("order");
		$search = $request->get("search");

        $cmd = User::query();

        try {
            $data = DatatableHelper::make($cmd, $columns, $start, $length, $order, $search);
            return response()->json(ResponseHelper::success(data: $data), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);

        }
    }
}
