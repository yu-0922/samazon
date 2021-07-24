<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\User;

class ShoppingCart extends Model
{
    public static function getDailyBillings()
    {
        $count = DB::table('shoppingcart')->count();
        if ($count == 0) {
            return [];
        }

        $recent_date = DB::table('shoppingcart')->latest('created_at')->first()->created_at;
        $recent_date = new Carbon($recent_date);
        $recent_date->addDays(1);

        $latest_date = DB::table('shoppingcart')->first()->created_at;
        $latest_date = new Carbon($latest_date);

        $billings = [];

        while ($recent_date->format('Y-m-d') != $latest_date->format('Y-m-d')) {
            $date = $latest_date->format('Y-m-d');
            $query = DB::table('shoppingcart')->whereDate('created_at', '=', $date);

            $billings[] = [
                'created_at' => $date,
                'total' => $query->sum('price_total'),
                'count' => $query->count(),
                'avg' => $query->avg('price_total')
            ];
            $latest_date->addDays(1);
        }

        return $billings;
    }

    public static function getMonthlyBillings()
    {
        $recent_date = DB::table('shoppingcart')->latest('created_at')->first()->created_at;
        $recent_date = new Carbon($recent_date);
        $recent_date->addMonths(1);

        $latest_date = DB::table('shoppingcart')->first()->created_at;
        $latest_date = new Carbon($latest_date);

        $billings = [];

        while ($recent_date->format('Y-m') != $latest_date->format('Y-m')) {
            $date = $latest_date->format('Y-m');
            $query = DB::table('shoppingcart')->whereYear('created_at', '=', $latest_date->year)->whereMonth('created_at', '=', $latest_date->month);

            $billings[] = [
                'created_at' => $date,
                'total' => $query->sum('price_total'),
                'count' => $query->count(),
                'avg' => $query->avg('price_total')
            ];
            $latest_date->addMonths(1);
        }

        return $billings;
    }

    public static function getOrders($code)
    {
        $shoppingcarts = DB::table('shoppingcart')->where("code", 'like', "%{$code}%")->get();

        $orders = [];

        foreach($shoppingcarts as $order) {
            $orders[] = [
                'created_at' => $order->created_at,
                'total' => $order->price_total,
                'user_name' => User::find($order->instance)->name,
                'code' => $order->code
            ];
        }

        return $orders;
    }
}
