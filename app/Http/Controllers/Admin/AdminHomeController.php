<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminHomeController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $totalRevenue = Order::sum('total');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        
        // Bénéfices (approximation basée sur le prix de vente - coût estimé)
        // Pour simplifier, on considère un bénéfice de 30% sur chaque vente
        $totalProfit = $totalRevenue * 0.3;
        
        // Statistiques par période
        $todayRevenue = Order::whereDate('created_at', Carbon::today())->sum('total');
        $todayProfit = $todayRevenue * 0.3;
        
        $thisMonthRevenue = Order::whereMonth('created_at', Carbon::now()->month)->sum('total');
        $thisMonthProfit = $thisMonthRevenue * 0.3;
        
        // Chiffre d'affaires par produit
        $revenueByProduct = DB::table('items')
            ->join('products', 'items.product_id', '=', 'products.id')
            ->selectRaw('products.name, SUM(items.price * items.quantity) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();
        
        // Chiffre d'affaires par catégorie
        $revenueByCategory = DB::table('items')
            ->join('products', 'items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, SUM(items.price * items.quantity) as revenue')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();
        
        // Commandes récentes
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.home.index', compact(
            'totalRevenue',
            'totalProfit',
            'totalOrders',
            'totalProducts',
            'todayRevenue',
            'todayProfit',
            'thisMonthRevenue',
            'thisMonthProfit',
            'revenueByProduct',
            'revenueByCategory',
            'recentOrders'
        ));
    }

    public function downloadPdf()
    {
        // Récupérer toutes les statistiques
        $totalRevenue = Order::sum('total');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalProfit = $totalRevenue * 0.3;
        
        // Statistiques par période
        $todayRevenue = Order::whereDate('created_at', Carbon::today())->sum('total');
        $todayProfit = $todayRevenue * 0.3;
        
        $thisMonthRevenue = Order::whereMonth('created_at', Carbon::now()->month)->sum('total');
        $thisMonthProfit = $thisMonthRevenue * 0.3;
        
        $thisYearRevenue = Order::whereYear('created_at', Carbon::now()->year)->sum('total');
        $thisYearProfit = $thisYearRevenue * 0.3;
        
        // Statistiques par jour (7 derniers jours)
        $revenueByDay = Order::selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->whereBetween('created_at', [Carbon::now()->subDays(6), Carbon::now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Statistiques par mois (12 derniers mois)
        $revenueByMonth = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as revenue')
            ->whereBetween('created_at', [Carbon::now()->subMonths(11), Carbon::now()])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Chiffre d'affaires par produit
        $revenueByProduct = DB::table('items')
            ->join('products', 'items.product_id', '=', 'products.id')
            ->selectRaw('products.name, SUM(items.price * items.quantity) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->get();
        
        // Bénéfices par produit
        $profitByProduct = DB::table('items')
            ->join('products', 'items.product_id', '=', 'products.id')
            ->selectRaw('products.name, SUM(items.price * items.quantity) * 0.3 as profit')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('profit')
            ->get();
        
        // Chiffre d'affaires par catégorie
        $revenueByCategory = DB::table('items')
            ->join('products', 'items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, SUM(items.price * items.quantity) as revenue')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();
        
        // Bénéfices par catégorie
        $profitByCategory = DB::table('items')
            ->join('products', 'items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, SUM(items.price * items.quantity) * 0.3 as profit')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('profit')
            ->get();
        
        // Commandes récentes
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        $data = [
            'totalRevenue' => $totalRevenue,
            'totalProfit' => $totalProfit,
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'todayRevenue' => $todayRevenue,
            'todayProfit' => $todayProfit,
            'thisMonthRevenue' => $thisMonthRevenue,
            'thisMonthProfit' => $thisMonthProfit,
            'thisYearRevenue' => $thisYearRevenue,
            'thisYearProfit' => $thisYearProfit,
            'revenueByDay' => $revenueByDay,
            'revenueByMonth' => $revenueByMonth,
            'revenueByProduct' => $revenueByProduct,
            'profitByProduct' => $profitByProduct,
            'revenueByCategory' => $revenueByCategory,
            'profitByCategory' => $profitByCategory,
            'recentOrders' => $recentOrders,
            'generatedAt' => Carbon::now()->format('d/m/Y H:i:s')
        ];
        
        $pdf = PDF::loadView('admin.home.pdf', $data);
        
        return $pdf->download('rapport-statistiques-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
}
