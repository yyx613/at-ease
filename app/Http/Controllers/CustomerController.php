<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{
    public function index($id) {
        $user = User::find($id);
        if ($user === null) { // If user id is not found
            abort(404);
        }
        session(['customer_id' => $user->id]);
        
        return view('pages.customer.info', [
            'nextStep' => route('select-product'),
            'user' => $user,
            'orderPendingId' => Order::ORDER_STATUS_PENDING
        ]);
    }
    
    public function selectProduct() {
        // If no user has selected and direct url
        if (session('customer_id') == null) {
            return redirect(route('customer-list'));
        }
        $user = User::find(session('customer_id'));
        $products = Product::where('qty', '>', 0)->get();
        // Replace product price with user special price if there is any
        foreach ($user->products as $product) {
            foreach ($products as $prod) {
                if ($prod->id == $product->pivot->product_id) {
                    $prod->price = $product->pivot->special_price;
                }
            }
        }

        return view('pages.product.list', [
            'nextStep' => route('cart'),
            'products' => $products,
        ]);
    }

    public function selectProductSubmit(Request $request) {
        try {
            $cart = json_decode($request->get('cart'));
    
            if (count($cart) <= 0) {
                return redirect()->back()->with('info', 'Please select at least 1 product');
            }
            
            session(['cart' => $request->get('cart')]);
            
            return redirect(route('cart'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong... Please try again later');
        }
    }
}
