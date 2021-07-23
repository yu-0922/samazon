<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\ShoppingCart;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page != null ? $request->page : 1;
        $sort = $request->sort;
        $billings = [];
        if ($request->sort == 'month') {
            $billings = ShoppingCart::getMonthlyBillings();
        } else {
            $billings = ShoppingCart::getDailyBillings();
        }
        $total = count($billings);
        $paginator = new LengthAwarePaginator(array_slice($billings, ($page - 1), 15), $total, 15, $page, ['path' => 'dashboard']);

        return view('dashboard.index', compact('billings', 'total', 'paginator', 'sort'));
    }
}
