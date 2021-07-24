<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use App\ShoppingCart;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page != null ? $request->page : 1;
        $code = $request->code != null ? $request->code : "";
        $sort = $request->sort;
        $orders = ShoppingCart::getOrders($code);
        $total = count($orders);
        $paginator = new LengthAwarePaginator(array_slice($orders, ($page - 1), 15), $total, 15, $page, ['path' => 'dashboard.orders.index']);

        return view('dashboard.orders.index', compact('orders', 'total', 'paginator', 'sort', 'code'));
    }
}
