<?php

namespace App\Http\Controllers;

use App\Helpers\FileHelper;
use App\Helpers\ResponseHelper;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    public function upload(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:png,jpg',
        ]);
        if ($validator->fails()) {
            return response()->json(ResponseHelper::warning( validations: $validator->errors(), code: 422), 422);
        }

        try {

            $file = $request->file('file');
            $filecode = FileHelper::upload($file);
            $data = [
                'filecode' => $filecode
            ];
            return response()->json(ResponseHelper::success(data: $data), 200);
        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }

    public function images(Request $request)
    {
        try {
            $images = File::query()->get()->pluck('fileurl');
            return response()->json(ResponseHelper::success(data: $images), 200);
        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }
}
