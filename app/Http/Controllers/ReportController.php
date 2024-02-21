<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function index(Request $request)
    {
        $date = $request->date ?? now();

        return response()->json([
            'status' => 'success',
            'data' => [
                'daily' => $this->dailyReports($date),
                'weekly' => $this->weeklyReports(),
                'monthly' => $this->monthlyReports(),
                'yearly' => $this->yearlyReports(),
                'bestSelling' => $this->bestSelling()
            ]
        ]);
    }
    public function dailyReports($date)
    {
        $order = Order::whereDate('created_at', $date)->get();

        $productSold = $order->sum(function ($order) {
            return $order->order_detail->sum('quantity');
        });

        $income = $order->sum('total_pay');

        return collect([
            'productSold' => $productSold,
            'income' => $income,
            'date' => $date
        ]);
    }

    public function weeklyReports()
    {
        $order = Order::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->get();

        $productSold = $order->sum(function ($order) {
            return $order->order_detail->sum('quantity');
        });

        $income = $order->sum('total_pay');

        return collect([
            'productSold' => $productSold,
            'income' => $income,
            'date' => Carbon::now()->startOfWeek() . ' - ' . Carbon::now()->endOfWeek()
        ]);
    }

    public function monthlyReports()
    {

        $order = Order::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->get();

        $productSold = $order->sum(function ($order) {
            return $order->order_detail->sum('quantity');
        });

        $income = $order->sum('total_pay');

        return collect([
            'productSold' => $productSold,
            'income' => $income,
            'date' => Carbon::now()->startOfMonth() . ' - ' . Carbon::now()->endOfMonth()
        ]);
    }

    public function yearlyReports()
    {
        $year = Carbon::now()->year;
        $monthlyData = [];
        $totalProductSold = 0;
        $totalIncome = 0;

        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
            $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

            $order = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();
            $productSold = $order->sum(function ($order) {
                return $order->order_detail->sum('quantity');
            });
            $income = $order->sum('total_pay');

            $totalProductSold += $productSold;
            $totalIncome += $income;

            $monthlyData[] = [
                'productSold' => $productSold,
                'income' => $income,
                'date' => $startOfMonth->format('Y-m-d') . ' - ' . $endOfMonth->format('Y-m-d')
            ];
        }

        return [
            'yearly' => [
                'productSold' => $totalProductSold,
                'income' => $totalIncome,
                'date' => Carbon::now()->startOfYear() . ' - ' . Carbon::now()->endOfYear()
            ],
            'monthly' => $monthlyData
        ];
    }


    public function bestSelling()
    {
        $bestsellingProducts = OrderDetail::with('product:id,name')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return $bestsellingProducts;
    }
}
