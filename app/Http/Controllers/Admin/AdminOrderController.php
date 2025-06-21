<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminOrderController extends Controller
{
    public function index()
    {
        $viewData = [];
        $viewData['title'] = 'Admin Page - Orders - Online Store';
        $viewData['orders'] = Order::with(['user', 'items.product'])->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.order.index')->with('viewData', $viewData);
    }

    public function show($id)
    {
        $viewData = [];
        $viewData['title'] = 'Admin Page - Order Details - Online Store';
        $viewData['order'] = Order::with(['user', 'items.product'])->findOrFail($id);

        return view('admin.order.show')->with('viewData', $viewData);
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->setPaymentStatus($request->input('payment_status'));
        $order->save();

        return back()->with('success', 'Payment status updated successfully.');
    }
}
