@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            @if(session('msg'))
            <div class="alert alert-{{session('msgtype')}}" role="alert">
                {{session('msg')}}
            </div>
            @endif
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

<form action="{{route('search_init')}}" method="POST">
  @csrf
  <div class="modal fade" id="nvm" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="">New Vaccination</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="patient_id" class="form-label">Select Patient to Encode</label>
              <select class="form-select" name="patient_id" id="patient_id" required>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-magnifying-glass me-2"></i>Search</button>
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
                <a href="{{route('vaccinationsite_index')}}" class="btn btn-primary">Vaccination Sites</a>
                <a href="{{route('vaccinebrand_index')}}" class="btn btn-primary">Vaccine Brands</a>
                <a href="" class="btn btn-primary">Site Settings</a>
            </div>
        </div>
      </div>
    </div>
</div>

<script>
  $(document).ready(function () {
    $('#patient_id').select2({
          dropdownParent: $("#nvm"),
          theme: "bootstrap",
          placeholder: 'Search by Name / Patient ID ...',
          ajax: {
              url: "{{route('patient_ajaxlist')}}",
              dataType: 'json',
              delay: 250,
              processResults: function (data) {
                  return {
                      results:  $.map(data, function (item) {
                          return {
                              text: item.text,
                              id: item.id,
                              class: item.class,
                          }
                      })
                  };
              },
              cache: true
          }
      });
  });
</script>
@endsection