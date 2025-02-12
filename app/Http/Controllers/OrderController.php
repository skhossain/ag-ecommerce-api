<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;

class OrderController extends Controller
{
    // Fetch all orders (admin only)
    public function index()
    {
        return Order::with('items','user')->orderBy('id','desc')->paginate(10);
    }

    // Place a new order (user only)
    public function store(Request $request)
    {
        $user_id = auth()->id();

        DB::beginTransaction();

        try {
            // Get cart items for the user
            $items = Cart::where('user_id', $user_id)->get();
            $address_info = $request->address;

            $address = New Address();
            $address->user_id = $user_id;
            $address->name = $address_info['name'];
            $address->email = $address_info['email'];
            $address->phone = $address_info['phone'];
            $address->address = $address_info['address'];
            $address->city = $address_info['city'];
            $address->postalCode = $address_info['postalCode'];
            $address->country = $address_info['country'];
            $address->save();

            $total_amount = 0;
            // Create a new order
            $order = Order::create([
                'user_id' => $user_id,
                'address_id' => $address->id,
                'total_amount' => $total_amount,
            ]);

            // Loop through the items in the cart
            foreach ($items as $item) {
                // Find the product details
                $product = Product::find($item->product_id);
                $item->price = $product->price; 

                // Calculate the total amount
                $total_amount += $item->price * $item->quantity; 

                // Create order items
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }

            // Update the total amount of the order
            $order->update(['total_amount' => $total_amount]);

            // Delete the items from the cart
            $items->each(function ($item) {
                $item->delete();
            });

            // Commit the transaction if everything is successful
            DB::commit();

            return $order;

        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Optionally, you can log the error or return a custom message
            // Log::error($e->getMessage());
            return response()->json(['error' => 'Something went wrong','message' => $e->getMessage()], 500);
        }
    }


    // Fetch a single order
    public function show(Order $order)
    {
        $user = auth()->user();

        // Allow admin to view any order
        if ($user->role === 'admin') {
            return $order->load('items');
        }

        // Allow regular users to view only their own orders
        if ($order->user_id === $user->id) {
            return $order->load('items');
        }

        // If user is neither admin nor the order owner, return unauthorized response
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    public function userOrders()
    {
        return Order::where('user_id', auth()->id())->with('items')->paginate(10);
    }

}
