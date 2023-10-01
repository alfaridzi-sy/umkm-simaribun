<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;

class ReportController extends Controller
{
    public function index(){
        $today          = Carbon::now()->toDateString();
        $startOfWeek    = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek      = Carbon::now()->endOfWeek()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $role = auth()->user()->role_id;
        $user = auth()->user()->user_id;

        if($role == 1){
            $ordersToday    = Order::whereDate('order_date', $today)->where('order_status', '!=', 1)->count();
            $ordersThisWeek = Order::whereBetween('order_date', [$startOfWeek, $endOfWeek])->where('order_status', '!=', 1)->count();
            $ordersThisMonth = Order::whereBetween('order_date', [$startOfMonth, $endOfMonth])->where('order_status', '!=', 1)->count();

            $salesTodayData    = Order::whereDate('order_date', $today)->where('order_status', '!=', 1)->sum('price_amount');
            $salesThisWeekData = Order::whereBetween('order_date', [$startOfWeek, $endOfWeek])->where('order_status', '!=', 1)->sum('price_amount');
            $salesThisMonthData= Order::whereBetween('order_date', [$startOfMonth, $endOfMonth])->where('order_status', '!=', 1)->sum('price_amount');

            $ordersTodayData    = Order::whereDate('order_date', $today)->where('order_status', '!=', 1)->get();
            $ordersThisWeekData = Order::whereBetween('order_date', [$startOfWeek, $endOfWeek])->where('order_status', '!=', 1)->get();
            $ordersThisMonthData= Order::whereBetween('order_date', [$startOfMonth, $endOfMonth])->where('order_status', '!=', 1)->get();
        }else if($role == 2){
            $ordersToday    = Order::whereDate('order_date', $today)->where('order_status', '!=', 1)->where('user_id', $user)->count();
            $ordersThisWeek = Order::whereBetween('order_date', [$startOfWeek, $endOfWeek])->where('order_status', '!=', 1)->where('user_id', $user)->count();
            // dd($ordersThisWeek);
            $ordersThisMonth = Order::whereBetween('order_date', [$startOfMonth, $endOfMonth])->where('order_status', '!=', 1)->where('user_id', $user)->count();

            $salesTodayData    = Order::whereDate('order_date', $today)->where('order_status', '!=', 1)->where('user_id', $user)->sum('price_amount');
            $salesThisWeekData = Order::whereBetween('order_date', [$startOfWeek, $endOfWeek])->where('order_status', '!=', 1)->where('user_id', $user)->sum('price_amount');
            $salesThisMonthData= Order::whereBetween('order_date', [$startOfMonth, $endOfMonth])->where('order_status', '!=', 1)->where('user_id', $user)->sum('price_amount');

            $ordersTodayData    = Order::whereDate('order_date', $today)->where('order_status', '!=', 1)->where('user_id', $user)->get();
            $ordersThisWeekData = Order::whereBetween('order_date', [$startOfWeek, $endOfWeek])->where('order_status', '!=', 1)->where('user_id', $user)->get();
            $ordersThisMonthData= Order::whereBetween('order_date', [$startOfMonth, $endOfMonth])->where('order_status', '!=', 1)->where('user_id', $user)->get();
        }

        $salesToday = number_format($salesTodayData, 0, ',', '.');
        $salesThisWeek = number_format($salesThisWeekData, 0, ',', '.');
        $salesThisMonth = number_format($salesThisMonthData, 0, ',', '.');

        return view('admin.report.index',[
            "ordersToday"           => $ordersToday,
            "ordersThisWeek"        => $ordersThisWeek,
            "ordersThisMonth"       => $ordersThisMonth,
            "salesToday"            => $salesToday,
            "salesThisWeek"         => $salesThisWeek,
            "salesThisMonth"        => $salesThisMonth,
            "ordersTodayData"       => $ordersTodayData,
            "ordersThisWeekData"    => $ordersThisWeekData,
            "ordersThisMonthData"   => $ordersThisMonthData
        ]);
    }
}
