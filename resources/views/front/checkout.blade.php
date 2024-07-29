@extends('front.layouts.app')
@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.shop') }}">Shop</a></li>
                    <li class="breadcrumb-item">Checkout</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-9 pt-4">
        <div class="container">
            <form id="orderForm" name="orderForm" action="" method="post">
            <div class="row">
                <div class="col-md-8">
                    <div class="sub-title">
                        <h2>Shipping Address</h2>
                    </div>
                    <div class="card shadow-lg border-0">
                        <div class="card-body checkout-form">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" value="{{ (!empty($customerAddress)) ? $customerAddress->first_name : '' }}">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name" value="{{ (!empty($customerAddress)) ? $customerAddress->last_name : '' }}">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{ (!empty($customerAddress)) ? $customerAddress->email : '' }}">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <select name="country" id="country" class="form-control">
                                            <option value="">Select a Country</option>
                                            @if ($countries->isNotEmpty())
                                            @foreach ($countries as $country)
                                            <option {{ (!empty($customerAddress) && $customerAddress->country_id == $country->id) ? 'Selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <textarea name="address" id="address" cols="30" rows="3" placeholder="Address" class="form-control" value="{{ (!empty($customerAddress)) ? $customerAddress->address : '' }}"></textarea>
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="apartment" id="apartment" class="form-control" placeholder="Apartment, suite, unit, etc. (optional)" value="{{ (!empty($customerAddress)) ? $customerAddress->apartment : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <input type="text" name="city" id="city" class="form-control" placeholder="City" value="{{ (!empty($customerAddress)) ? $customerAddress->city : '' }}">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <input type="text" name="state" id="state" class="form-control" placeholder="State" value="{{ (!empty($customerAddress)) ? $customerAddress->state : '' }}">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <input type="text" name="zip" id="zip" class="form-control" placeholder="Zip" value="{{ (!empty($customerAddress)) ? $customerAddress->zip : '' }}">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Mobile No." value="{{ (!empty($customerAddress)) ? $customerAddress->mobile : '' }}">
                                        <p>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <textarea name="notes" id="notes" cols="30" rows="2" placeholder="Order Notes (optional)" class="form-control" value="{{ (!empty($customerAddress)) ? $customerAddress->notes : '' }}"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sub-title">
                        <h2>Order Summery</h3>
                    </div>
                    <div class="card cart-summery">
                        @foreach (Cart::content() as $item)
                        <div class="card-body">
                            <div class="d-flex justify-content-between pb-2">
                                <div class="h6">{{ $item-> name}} x {{ $item->qty }}</div>
                                <div class="h6">Rs.{{ $item->price*$item->qty}}</div>
                            </div>

                        @endforeach
                            <div class="d-flex justify-content-between summery-end">
                                <div class="h6"><strong>Subtotal</strong></div>
                                <div class="h6"><strong>Rs.{{ Cart::subtotal() }}</strong></div>
                            </div>
                            <div class="d-flex justify-content-between summery-end">
                                <div class="h6"><strong>Discount</strong></div>
                                <div class="h6"><strong id="discount_value">Rs.{{ $discount }}</strong></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="h6"><strong>Shipping</strong></div>
                                <div class="h6"><strong id="shippingAmount">Rs.{{ $totalShippingCharge->amount }}</strong></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2 summery-end">
                                <div class="h5"><strong>Total</strong></div>
                                <div class="h5"><strong id="grandTotal">Rs.{{ Cart::subtotal() }}</strong></div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group apply-coupan mt-4">
                        <input type="text" name="discount_code" id="discount_code" placeholder="Coupon Code" class="form-control">
                        <button class="btn btn-dark" type="button" id="apply-discount">Apply Coupon</button>
                    </div>
                    <div id="discount-response-wrapper">
                    @if (Session::has('code'))
                    <div class=" apply-coupan mt-4" id="discount-response">
                    <strong>{{ Session()->get('code')->code }};</strong>
                    <button class="btn btn-sm btn-danger" id="remove-discount"><i class="fa fa-times"></i></button>
                </div>
                    @endif
                </div>
                    <div class="card payment-form ">
                        <h3 class="card-title h5 mb-3">Payment Method</h3>
                        <div>
                            <input type="radio" name="payment_method" value="cod" id="payment_method_one">
                            <label for="payment_method_one" class="form-check-label">Cash on Delivery</label>
                        </div>
                        <div>
                            <input type="radio" name="payment_method" value="online" id="payment_method_two">
                            <label for="payment_method_two" class="form-check-label">Online Payment</label>
                        </div>
                        <div class="card-body p-0 d-none mt-3" id="card-payment-form">
                            <div class="mb-3">
                                <label for="card_number" class="mb-2">Card Number</label>
                                <input type="text" name="card_number" id="card_number" placeholder="Valid Card Number" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="expiry_date" class="mb-2">Expiry Date</label>
                                    <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YYYY" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="expiry_date" class="mb-2">CVV Code</label>
                                    <input type="text" name="expiry_date" id="expiry_date" placeholder="123" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="pt-4">
                            <button type="submit" class="btn-dark btn btn-block w-100">Pay Now</button>
                        </div>
                    </div>


                    <!-- CREDIT CARD FORM ENDS HERE -->

                </div>
            </div>
        </form>
        </div>
    </section>
@endsection
@section('customJs')
<script>
    $("#payment_method_one").click(function () {
        if ($(this).is(":checked") == true) {
            $("#card-payment-form").addClass('d-none');
        }
    });
    $("#payment_method_two").click(function () {
        if ($(this).is(":checked") == true) {
            $("#card-payment-form").removeClass('d-none');
        }
    });
    $('#orderForm').submit(function(event){
    event.preventDefault();

    var element = $(this);
    $("button[type=submit]").prop('disabled',true);

    $.ajax({
        url:'{{ route("front.processCheckout") }}',
        type: 'post',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response){
            $("button[type=submit]").prop('disabled',false);
            if(response["status"] == true) {
                window.location.href="{{ url('/thanks/') }}/"+response.orderId;
                $("#first_name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#last_name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#country").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#address").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#state").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#city").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                $("#mobile").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
            } else {
                var errors = response['errors'];
            if(errors['first_name']){
                $("#first_name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['first_name']);
            }
            if(errors['last_name']){
                $("#last_name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['last_name']);
            }
            if(errors['email']){
                $("#email").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['email']);
            }
            if(errors['country']){
                $("#country").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['country']);
            }
            if(errors['address']){
                $("#address").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['address']);
            }
            if(errors['state']){
                $("#state").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['state']);
            }
            if(errors['city']){
                $("#city").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['city']);
            }
            if(errors['mobile']){
                $("#mobile").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['mobile']);
            }
            }

        }, error: function(jqXHR, exception){
            console.log("Something went wrong.");
        }
    });
});
$("#apply-discount").click(function(){
    $.ajax({
        url:'{{ route("front.applyDiscount") }}',
        type: 'post',
        data: {code: $("#discount_code").val(), country_id: $("#country").val()},
        dataType: 'json',
        success: function(response){
            if (response.status == true) {
                $("#shippingAmount").html('$'+response.shippingCharge);
                $("#grandTotal").html('$'+response.grandTotal);
                $("#discount_value").html('$'+response.discount);
            }
            }
    });
});
$('body').on('click',"#remove-discount",function(){
    $.ajax({
        url:'{{ route("front.removeDiscount") }}',
        type: 'post',
        data: {country_id: $("#country").val()},
        dataType: 'json',
        success: function(response){
            if (response.status == true) {
                $("#shippingAmount").html('$'+response.shippingCharge);
                $("#grandTotal").html('$'+response.grandTotal);
                $("#discount_value").html('$'+response.discount);
                $("discount-response").html('');
                $("discount-response-wrapper").html(response.discountSting);
                $("discount_code").val('');
            }
            }
    });
});
</script>
@endsection
