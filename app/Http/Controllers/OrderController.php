<?php

namespace App\Http\Controllers;

use App\Models\FreeOfCharge;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $cartTotalPrice = $cartTotalPriceAfterFoc = 0;
        $hasFoc = false;
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
                    // Product single price (use special price there is any, else use default price)
                    $price = $specialPrice ?? $prod->price;

                    // Product total price (product single price * qty)
                    $totalPrice = $price * $temp_cart[$i]->qty;
                    $totalPrice = number_format((float)$totalPrice, 2, '.', '');
                    $cartTotalPrice += $totalPrice; // Add to cart total price
                    
                    // Product total price FOC (based on FOC type)
                    $totalPriceAfterFoc = null;
                    if (isset($user->foc->type)) {
                        if ($user->foc->type == FreeOfCharge::FOC_TYPE_1) {
                            $totalPriceAfterFoc = 0;
                        } else if ($user->foc->type == FreeOfCharge::FOC_TYPE_2 && $user->foc->product_id == $prod->id) {
                            if ($user->foc->foc_2_val >= $temp_cart[$i]->qty) {
                                $totalPriceAfterFoc = 0;
                            } else {
                                $totalPriceAfterFoc = $price * abs($user->foc->foc_2_val - $temp_cart[$i]->qty);
                            }
                        } else if ($user->foc->type == FreeOfCharge::FOC_TYPE_3 && $user->foc->product_id == $prod->id) {
                            $freeN = explode('-', $user->foc->foc_3_val)[0];
                            $onEveryM = explode('-', $user->foc->foc_3_val)[1];
                            // Formula
                            $batch = (int)floor($temp_cart[$i]->qty / $onEveryM);
                            $totalPriceAfterFoc = $totalPrice - ($batch * $freeN * $price);
                        }
                    }
                    // Add product ttl-foc into cart ttl-foc (if there is no ttl-foc for the product, add ttl)
                    if (isset($user->foc->type) && $totalPriceAfterFoc !== null) {
                        $totalPriceAfterFoc = number_format((float)$totalPriceAfterFoc, 2, '.', '');
                        $cartTotalPriceAfterFoc += $totalPriceAfterFoc; // Add to cart total price foc
                        $hasFoc = true;
                    } else {
                        $totalPriceAfterFoc = $totalPrice;
                        $cartTotalPriceAfterFoc += $totalPrice;
                    }
                    // Assign into array of object
                    $cart[$i] = (object)[
                        'id' => $prod->id,
                        'name' => $prod->name,
                        'img_name' => $prod->img_name,
                        'price' => $price,
                        'qty' => $temp_cart[$i]->qty,
                        'total_price' => $totalPrice,
                        'total_price_after_foc' => $totalPriceAfterFoc,
                    ];
                }
            }
        }
        $cartTotalPrice = number_format((float)$cartTotalPrice, 2, '.', '');
        $cartTotalPriceAfterFoc = number_format((float)$cartTotalPriceAfterFoc, 2, '.', '');
        
        session(['final_cart' => [
            'cart' => $cart,
            'total_price' => $cartTotalPrice,
            'total_price_after_foc' => $cartTotalPriceAfterFoc
        ]]);
        
        return view('pages.order.cart', [
            'cart' => $cart,
            'user' => $user,
            'cartTotalPrice' => $cartTotalPrice,
            'cartTotalPriceFoc' => $cartTotalPriceAfterFoc,
            'hasFoc' => $hasFoc
        ]);
    }

    public function cartSubmit(Request $request) {
        try {
            if (session('final_cart') != null && session('customer_id') != null) {
                $cart = session('final_cart')['cart'];
                $cartTotalPriceAfterFoc = session('final_cart')['total_price_after_foc'];
                $user = User::find(session('customer_id'));
                
                $orders = [];
                $orderSku = Order::ORDER_SKU_PREFIX . date('YmdHis');
                for ($i=0; $i < count($cart); $i++) { 
                    $orders[] = [
                        'order_sku' => $orderSku,
                        'customer_id' => session('customer_id'),
                        'product_id' => $cart[$i]->id,
                        'qty' => $cart[$i]->qty,
                        'price' => $cart[$i]->price,
                        'total_price' => $cart[$i]->total_price,
                        'total_price_after_foc' => $cart[$i]->total_price_after_foc,
                        'status_id' => Order::ORDER_STATUS_PENDING,
                        'remark' => $request->get('remark') ?? NULL,
                    ];
                    $prod = Product::find($cart[$i]->id); // Deduct product qty
                    $prod->decrement('qty', $cart[$i]->qty);
                    $prod->save();
                }
                Order::insert($orders); // Create orders
                session(['order_sku' => $orderSku]);
                // Update customer credit
                $user->increment('credit', $cartTotalPriceAfterFoc);
                $user->save();

                return redirect(route('order-receipt'));
            }
            return redirect(route('customer-list'));
        } catch (\Throwable $th) {
            return redirect(route('customer-list'));
        }
    }

    public function orderReceipt() {
        $user = User::find(session('customer_id'));
        $cart = session('final_cart')['cart'];
        $order = Order::where('order_sku', session('order_sku'))->first();
        
        $pdf = PDF::loadView('pages.pdf.order-receipt', [
            'orderId' => session('order_sku'),
            'invoiceDate' => $order->created_at,
            'cart' => $cart,
            'updatedCredit' => $user->credit,
        ]);
        $invoiceName = 'invoice-' . session('order_sku') . '.pdf';
        Storage::put('public/pdf/' . $invoiceName, $pdf->output());

        return view('pages.order.receipt', [
            'nextStep' => route('customer-list'),
            'invoice_name' => $invoiceName
        ]);
    }
}