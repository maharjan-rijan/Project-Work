@extends('front.layouts.app')

@section('content')
<section class="container">
    <div class="col-md-12 text-align-center py-5">
        @if (Session::has('success'))
        <div class="col-md-12 text-align-center py-5">
        <div class="alert alert-success">
        {{ Session::get('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
        </div>
@endif
        <h1>Thank You!</h1>
        <p>Your request Id is : {{ $id }}</p>
    </div>
</section>
@endsection
