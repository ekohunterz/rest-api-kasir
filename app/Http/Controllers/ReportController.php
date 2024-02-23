<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{

    public function index(Request $request)
    {

        return response()->json([
            'status' => 'success',
            'data' => [
                'daily' => $this->dailyReports(),
                'weekly' => $this->weeklyReports(),
                'monthly' => $this->monthlyReports($request->month ?? null),
                'yearly' => $this->yearlyReports($request->year ?? null),
                'bestSelling' => $this->bestSelling()
            ]
        ]);
    }
    public function dailyReports()
    {
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        $order = Order::whereDate('created_at', $today)->get();
        $orderYesterday = Order::whereDate('created_at', $yesterday)->get();

        $productSold = $order->sum(function ($order) {
            return $order->order_detail->sum('quantity');
        });

        $productSoldYesterday = $orderYesterday->sum(function ($order) {
            return $order->order_detail->sum('quantity');
        });


        $income = $order->sum('total_pay');
        $incomeYesterday = $orderYesterday->sum('total_pay');

        $percentageSold = ($productSold - $productSoldYesterday) / $productSoldYesterday * 100;
        $percentageIncome = ($income - $incomeYesterday) / $incomeYesterday * 100;
        return collect([
            'today' => ['productSold' => $productSold, 'income' => $income, 'date' => $today],
            'yesterday' => ['productSold' => $productSoldYesterday, 'income' => $incomeYesterday, 'date' => $yesterday],
            'percentage' => ['productSold' => $percentageSold, 'income' => $percentageIncome]
        ]);
    }

    public function weeklyReports()
    {
        $order = Order::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->get();

        $orderLastWeek = Order::whereBetween('created_at', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        ])->get();

        $productSold = $order->sum(function ($order) {
            return $order->order_detail->sum('quantity');
        });

        $productSoldLastWeek = $orderLastWeek->sum(function ($order) {
            return $order->order_detail->sum('quantity');
        });


        $income = $order->sum('total_pay');
        $incomeLastWeek = $orderLastWeek->sum('total_pay');

        $percentageIncome = 0;
        $percentageSold = 0;

        if ($incomeLastWeek) {
            $percentageIncome = ($income - $incomeLastWeek) / $incomeLastWeek * 100;
            $percentageSold = ($productSold - $productSoldLastWeek) / $productSoldLastWeek * 100;
        }

        return collect([
            'thisWeek' => ['productSold' => $productSold, 'income' => $income],
            'lastWeek' => ['productSold' => $productSoldLastWeek, 'income' => $incomeLastWeek],
            'percentage' => ['productSold' => $percentageSold, 'income' => $percentageIncome]
        ]);
    }

    public function monthlyReports($month)
    {
        // Parsing tanggal yang diterima dari permintaan
        $parsedMonth = $month ? Carbon::createFromFormat('Y/m', $month) : Carbon::now();

        // Mengambil tahun dari tanggal yang diterima dari permintaan
        $year = $parsedMonth->year;
        // Mengambil bulan dari tanggal yang diterima dari permintaan
        $month = $parsedMonth->month;

        $monthlyData = [];

        // Mengambil tanggal awal dan akhir dari bulan yang dipilih
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Mengambil data pesanan untuk setiap hari dalam bulan yang dipilih
        for ($day = 1; $day <= $endDate->daysInMonth; $day++) {
            $startOfDay = Carbon::create($year, $month, $day)->startOfDay();
            $endOfDay = Carbon::create($year, $month, $day)->endOfDay();
            $orders = Order::whereBetween('created_at', [$startOfDay, $endOfDay])->get();

            $productSold = $orders->sum(function ($order) {
                return $order->order_detail->sum('quantity');
            });

            $income = $orders->sum('total_pay');

            // Menyimpan data penjualan untuk setiap hari dalam bulan
            $monthlyData[$day] = ['productSold' => $productSold, 'income' => $income, 'date' => Carbon::create($year, $month, $day)->format('d F, Y')];
        }

        // Menghitung total penjualan dan pendapatan untuk bulan tersebut
        $totalProductSold = collect($monthlyData)->sum('productSold');
        $totalIncome = collect($monthlyData)->sum('income');

        return collect([
            'productSold' => $totalProductSold,
            'income' => $totalIncome,
            'data' => $monthlyData
        ]);
    }



    public function yearlyReports($year)
    {
        $year =  $year ?? Carbon::now()->year;
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
                'date' => $startOfMonth->format('M Y')
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

    public function exportYearlyReportToExcel(Request $request)
    {
        $year = $request->year ?? Carbon::now()->year;

        // Panggil fungsi untuk mendapatkan data laporan tahunan
        $yearlyReportData = $this->yearlyReports($year);

        // Buat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Isi data laporan tahunan ke dalam file Excel
        $sheet->setCellValue('A1', 'Laporan Tahunan ' . $year);
        $sheet->setCellValue('A2', 'Tanggal');
        $sheet->setCellValue('B2', 'Produk Terjual');
        $sheet->setCellValue('C2', 'Pendapatan');

        $row = 3;
        foreach ($yearlyReportData['monthly'] as $monthlyReport) {
            $sheet->setCellValue('A' . $row, $monthlyReport['date']);
            $sheet->setCellValue('B' . $row, $monthlyReport['productSold']);
            $sheet->setCellValue('C' . $row, $monthlyReport['income']);
            $row++;
        }

        // Simpan file Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_tahunan_' . $year . '.xlsx';
        $path = storage_path('exports/' . $filename);
        $writer->save($path);

        // Kirim file Excel sebagai respons
        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportMonthlyReportToExcel(Request $request)
    {

        $month = $request->month ?? Carbon::now()->month;

        // Panggil fungsi untuk mendapatkan data laporan bulanan
        $monthlyReportData = $this->monthlyReports($month);

        // Buat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Isi data laporan bulanan ke dalam file Excel
        $sheet->setCellValue('A1', 'Laporan Bulanan ' . '- ' . $month);
        $sheet->setCellValue('A2', 'Tanggal');
        $sheet->setCellValue('B2', 'Produk Terjual');
        $sheet->setCellValue('C2', 'Pendapatan');

        $row = 3;
        foreach ($monthlyReportData['data'] as $monthlyReport) {
            $sheet->setCellValue('A' . $row, $monthlyReport['date']);
            $sheet->setCellValue('B' . $row, $monthlyReport['productSold']);
            $sheet->setCellValue('C' . $row, $monthlyReport['income']);
            $row++;
        }

        $sheet->setCellValue('A' . $row + 1, 'Total');
        $sheet->setCellValue('B' . $row + 1, $monthlyReportData['productSold']);
        $sheet->setCellValue('C' . $row + 1, $monthlyReportData['income']);



        // Simpan file Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_bulanan_'  . Carbon::createFromFormat('Y/m', $month)->format('F Y') . '.xlsx';
        $path = storage_path('exports/' . $filename);
        $writer->save($path);

        // Kirim file Excel sebagai respons
        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }
}
