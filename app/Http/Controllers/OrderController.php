<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function cart() {
        if (session('customer_id') == null || session('cart') == null) {
            return redirect(route('customer-list'));
        }

        $temp_cart = json_decode(session('cart'));
        $user = User::find(session('customer_id'));

        // Get products detail
        $prodIds = [];
        for ($i=0; $i < count($temp_cart); $i++) { 
            array_push($prodIds, $temp_cart[$i]->id);
        }
        $selectedProducts = Product::whereIn('id', $prodIds)->get();
        // Calculate cart price
        $cart = [];
        $cartTotalPrice = 0;
        for ($i=0; $i < count($temp_cart); $i++) { 
            // Get special price
            $specialPrice = null;
            foreach ($user->products as $product) {
                if ($product->pivot->product_id == $temp_cart[$i]->id) {
                    $specialPrice = $product->pivot->special_price;
                    break;
                }
            }
            // Assign product detail
            foreach ($selectedProducts as $prod) {
                if ($prod->id == $temp_cart[$i]->id) {
                    $price = $specialPrice ?? $prod->price;
                    $totalPrice = $price * $temp_cart[$i]->qty;
                    $cartTotalPrice += $totalPrice;
                    $cart[$i] = (object)[
                        'id' => $prod->id,
                        'name' => $prod->name,
                        'img_name' => $prod->img_name,
                        'price' => $price,
                        'qty' => $temp_cart[$i]->qty,
                        'total_price' => number_format((float)$totalPrice, 2, '.', ''),
                    ];
                }
            }
        }
        $cartTotalPrice = number_format((float)$cartTotalPrice, 2, '.', '');
        
        // dd( $cart, $temp_cart );
        session(['final_cart' => [
            'cart' => $cart,
            'total_price' => $cartTotalPrice
        ]]);

        return view('pages.order.cart', [
            'cart' => $cart,
            'user' => $user,
            'cartTotalPrice' => $cartTotalPrice
        ]);
    }

    public function cartSubmit(Request $request) {
        try {
            if (session('final_cart') != null && session('customer_id') != null) {
                $cart = session('final_cart')['cart'];
                $cartTotalPrice = session('final_cart')['total_price'];
                // dd($cart, $cartTotalPrice);

                $orders = [];
                $orderSku = Order::ORDER_SKU_PREFIX . date('YmdHis');
                for ($i=0; $i < count($cart); $i++) { 
                    $orders[] = [
                        'order_sku' => $orderSku,
                        'customer_id' => session('customer_id'),
                        'product_id' => $cart[$i]->id,
                        'qty' => $cart[$i]->qty,
                        'price' => $cart[$i]->price,
                        'remark' => $request->get('remark') ?? NULL,
                        'status_id' => Order::ORDER_STATUS_PENDING
                    ];
                    $prod = Product::find($cart[$i]->id); // Deduct product qty
                    $prod->decrement('qty', $cart[$i]->qty);
                    $prod->save();
                }
                Order::insert($orders); // Create orders
                $user = User::find(session('customer_id')); // Update customer credit
                $user->increment('credit', $cartTotalPrice);
                $user->save();

                return redirect(route('order-receipt'));
            }
            return redirect(route('customer-list'));
        } catch (\Throwable $th) {
            return redirect(route('customer-list'));
        }
    }

    public function orderReceipt() {
        return view('pages.order.receipt', [
            'nextStep' => route('customer-list')
        ]);
    }
}