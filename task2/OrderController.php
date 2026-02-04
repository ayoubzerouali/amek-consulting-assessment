<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
/* I refactored the method by replacing repeated queries inside loops with eager loading 
 * and database-level aggregations. I used with, withCount, and withSum to eliminate N+1 
 * queries and moved sorting and calculations to the database where possible. */
    public function index()
    {
        $orders = Order::with([
                'customer:id,name',
                'items:id,order_id,price,quantity',
                'cartItems' => fn ($q) => $q->latest(),
            ])
        ->withCount('items as total_items_count')
        ->withSum('items as total_amount', DB::raw('price * quantity'))
        ->latest('completed_at')
        ->get();
        $orderData = [];

        foreach ($orders as $order) {
            //Used already-loaded collections instead of additional database calls           
            
            /* $customer = $order->customer; */
            /* $items = $order->items; */
            /* $totalAmount = 0; */
            /* $itemsCount = 0; */

            //=================================
            // replaced with sql aggres using withCount and withSum

            /* foreach ($items as $item) { */
            /*     $product = $item->product; */
            /*     $totalAmount += $item->price * $item->quantity; */
            /*     $itemsCount++; */
            /* } */
            //=================================

            //=================================
            //Avoided querying inside loops 
            
            ///* $lastAddedToCart = CartItem::where('order_id', $order->id) */
            /*     ->orderByDesc('created_at') */
            /*     ->first() */
            /*     ->created_at ?? null; */
            $lastAddedToCart = $order->cartItems->first()?->created_at; 
            /* $completedOrderExists = Order::where('id', $order->id) */
            /*     ->where('status', 'completed') */
            /*     ->exists(); */

            $orderData[] = [
                'order_id' => $order->id,
                'customer_name' => $order->customer->name,
                'total_amount' => $order->total_amount ?? 0,
                'items_count' => $order->total_items_count ?? 0,
                'last_added_to_cart' => $lastAddedToCart,
                'completed_order_exists' => $order->status === 'completed',
                'created_at' => $order->created_at,
            ];
        }

        
        /* usort($orderData, function($a, $b) { */
        /*     $aCompletedAt = Order::where('id', $a['order_id']) */
        /*         ->where('status', 'completed') */
        /*         ->orderByDesc('completed_at') */
        /*         ->first() */
        /*         ->completed_at ?? null; */
        /**/
        /*     $bCompletedAt = Order::where('id', $b['order_id']) */
        /*         ->where('status', 'completed') */
        /*         ->orderByDesc('completed_at') */
        /*         ->first() */
        /*         ->completed_at ?? null; */
        /**/
        /*     return strtotime($bCompletedAt) - strtotime($aCompletedAt); */
        /* }); */

        return view('orders.index', ['orders' => $orderData]);
    }
}
