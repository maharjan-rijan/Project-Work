<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class DiscountCodeController extends Controller
{
    public function index(){
        return view('admin.discountCoupon.list');
    }
    public function create(){
        return view('admin.discountCoupon.create');
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'status' => 'required',
            'discount_amount' => 'required|numeric',
        ]);
        if($validator->passes()) {
            if (!empty($request->starts_at) && !empty($request->expires_at) ) {
                $now = Carbon::now();
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                $expiresAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);
               if($startAt->lte($now) == true){
                return response()->json([
                    'status' => false,
                    'errors' => ['starts_at' => 'Starting date cannot be less than current date time']
                ]);
               }
            }

            if (!empty($request->starts_at) && !empty($request->expires_at) ) {
                $now = Carbon::now();
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                $expiresAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);
               if($expiresAt->gt($startAt) == false){
                return response()->json([
                    'status' => false,
                    'errors' => ['expires_at' => 'Expiry date cannot be less than starting date time']
                ]);
               }
            }
            $discountCode = new DiscountCoupon();
            $discountCode->code = $request->code;
            $discountCode->name = $request->name;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_user;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->min_amount = $request->min_amount;
            $discountCode->status = $request->status;
            $discountCode->starts_at = $request->starts_at;
            $discountCode->expires_at = $request->expires_at;
            $discountCode->description = $request->description;
            $discountCode->save();

            $message = 'Discount Coupon Added Successfully.';
            session()->flash('success',$message);
            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        }
        else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function edit(){

    }
    public function update(){

    }
    public function destroy(){

    }
}