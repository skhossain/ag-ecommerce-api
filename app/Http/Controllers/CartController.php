<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    // Fetch the user's cart
    public function index()
    {
        return Cart::where('user_id', auth()->id())->with('product')->get();
    }

    // Add a product to the cart
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        // Check if the product is already in the cart
        if (Cart::where([['user_id', auth()->id()], ['product_id', $request->product_id]])->exists()) {
            $cart = Cart::where([['user_id', auth()->id()], ['product_id', $request->product_id]])->first();
            $cart->quantity += $request->quantity;
            $cart->save();
        }else{
            $cart = Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }
        return Cart::where('id', $cart->id)->with('product')->first();
    }

    // Remove a product from the cart
    public function destroy(Cart $cart)
    {
        $cart->delete();
        return response()->json(['message' => 'Product removed from cart'], 204);
    }

    // Clear the user's cart
    public function clearCart()
    {
        Cart::where('user_id', auth()->id())->delete();
        return response()->json(['message' => 'Cart cleared successfully'], 204);
    }

    // Update the quantity of a product in the cart
    public function update(Cart $cart, Request $request)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1',
        ]);
        $cart->quantity = $request->quantity;
        $cart->save();
        return Cart::where('id', $cart->id)->with('product')->first();
    }
}
