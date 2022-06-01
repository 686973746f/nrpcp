@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="d-grid gap-2">
                <a href="{{route('patient_index')}}" class="btn btn-primary btn-lg">Patient Lists</a>
                <button type="button" class="btn btn-primary btn-lg">New Vaccination</button>
                <hr>
                <a href="" class="btn btn-primary btn-lg">Admin Panel</a>
                <hr>
                <a href="" class="btn btn-primary btn-lg">Account Options</a>
            </div>
        </div>
    </div>
</div>
@endsection