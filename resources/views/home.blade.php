@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="d-grid gap-2">
                <a href="{{route('patient_index')}}" class="btn btn-primary btn-lg">Patient Lists</a>
                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#nvm">New Vaccination</button>
                <button type="button" class="btn btn-primary btn-lg">Report</button>
                <hr>
                <a href="" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#adminpanel">Admin Panel</a>
                <hr>
                <a href="" class="btn btn-primary btn-lg">Account Options</a>
            </div>
        </div>
    </div>
</div>

<form action="" method="GET">
    <div class="modal fade" id="nvm" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="">New Vaccination</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              ...
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
          </div>
        </div>
    </div>
</form>

<div class="modal fade" id="adminpanel" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="">Admin Panel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="d-grid gap-2">
                <a href="" class="btn btn-primary">Vaccination Sites</a>
                <a href="" class="btn btn-primary">Site Settings</a>
            </div>
        </div>
      </div>
    </div>
</div>
@endsection