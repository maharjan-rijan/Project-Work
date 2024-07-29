<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
   public function addToCart(Request $request){
    $product = Product::with('product_images')->find($request->id);
    //Cart::add('23445','Product 1',1,4453);
    if($product == null){
        return response()->json([
            'status' => false,
            'message' => 'Record not found'
        ]);
    }
    if(Cart::count() > 0){
        $cartContent = Cart::content();
        $productAlreadyExist = false;

        foreach ($cartContent as $item) {
            if($item->id == $product->id){
                $productAlreadyExist = true;
            }
        }
        if($productAlreadyExist == false){
            Cart::add($product->id, $product->title,1,$product->price,['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
            $status = true;
            $message = '<strong>'.$product->title.'</strong> added in your cart successfully.';
            session()->flash('success',$message);
        } else {
            $status = false;
            $message = $product->title.' already added to cart';
        }
    } else {
        Cart::add($product->id, $product->title,1,$product->price,['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
        $status = true;
        $message ='<strong>'.$product->title.'</strong> added in your cart successfully.';
        session()->flash('success',$message);
    }
    return response()->json([
        'status' => $status,
        'message' => $message
    ]);

   }
   public function cart(){
    $cartContent = Cart::content();
    $date['cartContent'] = $cartContent;
    return view('front.cart', $date);
   }
public function updateCart(Request $request){
    $rowId = $request->rowId;
    $qty = $request->qty;
    $itemInfo = Cart::get($rowId);
    $product = Product::find($itemInfo->id);
    if($product->track_qty == 'Yes'){
        if ($qty < $product->qty ) {
            $updateCart = Cart::update($rowId,$qty);
            $message = 'Cart updated successfully.';
            $status = true;
            session()->flash('success',$message);
        } else {
            $message = 'Requested qty('.$qty.') not avaliable in stock.';
            $status = false;
            session()->flash('error',$message);
        }
    } else {
        $updateCart = Cart::update($rowId,$qty);
        $message = 'Cart updated successfully.';
        $status = true;
        session()->flash('success',$message);
    }
    return response()->json([
        'status' => $status,
        'message' => $message
    ]);
}
public function deleteItem(Request $request){
    $rowId = $request->rowId;
    $itemInfo = Cart::get($rowId);
    if ($itemInfo == null) {
        $errorMessage = 'Item not found in cart';
        session()->flash('error',$errorMessage);
        return response()->json([
            'status' => false,
            'message' => $errorMessage
        ]);
    }
    Cart::remove($request->rowId);
    $successMessage = 'Item removed from cart successfully.';
    session()->flash('success',$successMessage);
    return response()->json([
        'status' => true,
        'message' => $successMessage
    ]);
}
public function checkOut(){
    //If cart is empty redirect to cart page
    if (Cart::count() == 0) {
        return redirect()->route('front.cart');
    }
    //if user is not login then redirect to login page
    if (Auth::check() == false) {
        if (!session()->has('url.intended')) {
           session(['url.intended'=> url()->current()]);
        }
       return redirect()->route('account.login');
    }
    $customerAddress = CustomerAddress::where('user_id',Auth::user()->id)->first();
    session()->forget('url.intended');
    $countries = Country::orderBy('name','ASC')->get();
    return view('front.checkout',[
        'countries' => $countries,
        'customerAddress' => $customerAddress,
    ]);
}
public function processCheckout(Request $request){
//Apply Validator 1
$validator = Validator::make($request->all(),[
    'first_name' => 'required|max:10',
    'last_name' => 'required|max:10',
    'email' => 'required|email',
    'country' => 'required',
    'address' => 'required|max:30',
    'city' => 'required',
    'state' => 'required',
    'mobile' => 'required',
]);
if ($validator->fails()) {
    return response()->json([
        'status' => false,
        'message' => 'Please fix the errors',
        'errors' => $validator->errors()
    ]);
}
//Step 2 Save user Address
$user = Auth::user();
$customerAddress = CustomerAddress::updateOrCreate(
    ['user_id' => $user->id],
    [
        'user_id' => $user->id,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'mobile' => $request->mobile,
        'country_id' => $request->country,
        'address' => $request->address,
        'apartment' => $request->apartment,
        'city' => $request->city,
        'state' => $request->state,
        'zip' => $request->zip,
    ]
);
//Store Data in Order Table
if ($request->payment_method == 'cod') {
    $shipping = 0;
    $discount = 0;
    $subtotal = Cart::subtotal(2,'.','');
    $grandtotal = $subtotal+$shipping;

    $order = new Order;
    $order->subtotal =$subtotal;
    $order->shipping = $shipping;
    $order->grand_total = $grandtotal;
    $order->user_id = $user->id;

    $order->first_name = $request->first_name;
    $order->last_name = $request->last_name;
    $order->email = $request->email;
    $order->mobile = $request->mobile;
    $order->address = $request->address;
    $order->apartment = $request->apartment;
    $order->state = $request->state;
    $order->city = $request->city;
    $order->country_id = $request->country;
    $order->zip = $request->zip;
    $order->notes = $request->notes;
    $order->save();

    //Store Order Items in Order Items table
   foreach (Cart::content() as $item) {
    $orderItem = new OrderItem;
    $orderItem->product_id = $item->id;
    $orderItem->order_id = $order->id;
    $orderItem->product_id = $item->id;
    $orderItem->name = $item->name;
    $orderItem->qty = $item->qty;
    $orderItem->price = $item->price;
    $orderItem->total = $item->price*$item->qty;
    $orderItem->save();
   }
   session()->flash('success','You have successfully placed your order.');
   Cart::destroy();
   return response()->json([
    'status' => true,
    'orderId' => $order->id,
    'message' => 'Order Saved successfully.',
]);
}
}
public function thankyou($id){
    return view('front.thanks',[
        'id' => $id,
    ]);
}
}
