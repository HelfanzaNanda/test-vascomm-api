<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Helpers\DatatableHelper;
use App\Helpers\FileHelper;
use App\Helpers\ResponseHelper;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric',
            'status' => 'required',
            'filecode' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(ResponseHelper::warning( validations: $validator->errors(), code: 422), 422);
        }

        try {
            $params = $validator->validated();
            $params['file_id'] = FileHelper::searchFilecode($params['filecode']);
            unset($params['filecode']);
            Product::create($params);
            return response()->json(ResponseHelper::success(), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }


    public function find($id)
    {
        $product = Product::whereId($id)->with('file')->first();
        if (!$product) {
            return response()->json(ResponseHelper::warning( message: 'product not found', code: 404), 404);
        }
        return response()->json(ResponseHelper::success(data: $product), 200);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric',
            'status' => 'required',
            'filecode' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(ResponseHelper::warning( validations: $validator->errors(), code: 422), 422);
        }

        $product = Product::find($id);
        if (!$product) {
            return response()->json(ResponseHelper::warning( message: 'product not found', code: 404), 404);
        }


        try {
            $params = $validator->validated();
            $params['file_id'] = FileHelper::searchFilecode($params['filecode']);
            unset($params['filecode']);
            $product->update($params);
            return response()->json(ResponseHelper::success(), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }

    public function delete($id)
    {
        $product = Product::whereId($id)->with('file')->first();
        if (!$product) {
            return response()->json(ResponseHelper::warning( message: 'product not found', code: 404), 404);
        }

        try {
            $product->file->delete();
            $product->delete();
            return response()->json(ResponseHelper::success(), 200);
        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }

    public function datatables(Request $request)
    {
        $columns = ['id', 'name', 'price', 'status', 'created_at'];
        $start = $request->get("start");
		$length = $request->get("length");
		$order = $request->get("order");
		$search = $request->get("search");

        $cmd = Product::query()->with('file');

        try {
            $data = DatatableHelper::make($cmd, $columns, $start, $length, $order, $search);
            return response()->json(ResponseHelper::success(data: $data), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);

        }
    }

    public function latest(Request $request)
    {
        $products = Product::query()
        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->with('file')
        ->take(10)->get();

        try {
            // $data = DatatableHelper::make($cmd, $columns, $start, $length, $order, $search);
            return response()->json(ResponseHelper::success(data: $products), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);

        }
    }

    public function active(Request $request)
    {
        $products = Product::query()
        ->where('status', StatusEnum::ACTIVE->value)
        ->with('file')
        ->take(10)->get();

        try {
            // $data = DatatableHelper::make($cmd, $columns, $start, $length, $order, $search);
            return response()->json(ResponseHelper::success(data: $products), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);

        }
    }
}
