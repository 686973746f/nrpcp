@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div>
                <div>Patient List</div>
                <div><a href="{{route('patient_create')}}" class="btn btn-success">Add Patient</a></div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="bg-light text-center">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age/Gender</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th>Date Encoded / By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $d)
                    <tr>
                        <td scope="row"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
        </div>
    </div>
</div>
@endsection