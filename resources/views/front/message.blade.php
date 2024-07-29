@if (Session::has('error'))
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h4><i class="icon fa fa-check"></i> Error!</h4>{{ Session::get('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
</div>
@endif
@if (Session::has('success'))
<div class="col-md-12">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <h4><i class="icon fa fa-check"></i> Success!</h4>{{ Session::get('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
</div>
@endif
