<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Helpers\DatatableHelper;
use App\Helpers\FileHelper;
use App\Helpers\ResponseHelper;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function card(Request $request)
    {

        try {

            $query = User::query();

            $all_users = $query->count();
            $all_users_desc = "Jumlah User";
            $active_users = $query->where('status', StatusEnum::ACTIVE->value)->count();
            $active_users_desc = "Jumlah User Aktif";


            $query = Product::query();

            $all_products = $query->count();
            $all_products_desc = "Jumlah Produk";
            $active_products = $query->where('status', StatusEnum::ACTIVE->value)->count();
            $active_products_desc = "Jumlah Produk Aktif";

            $result = [
                [ 'desc' => $all_users_desc, 'total' => $all_users . " User", ],
                [ 'desc' => $active_users_desc, 'total' => $active_users . " User", ],
                [ 'desc' => $all_products_desc, 'total' => $all_products . " User", ],
                [ 'desc' => $active_products_desc, 'total' => $active_products . " User", ],
            ];
            return response()->json(ResponseHelper::success(data: $result), 200);
        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }

    
}
