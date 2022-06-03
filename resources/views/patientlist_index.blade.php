@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div>Patient List</div>
                <div><a href="{{route('patient_create')}}" class="btn btn-success">Add Patient</a></div>
            </div>
        </div>
        <div class="card-body">
            @if(session('msg'))
            <div class="alert alert-{{session('msgtype')}}" role="alert">
                {{session('msg')}}
            </div>
            @endif
            
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
                        <td scope="row">{{$d->id}}</td>
                        <td><a href="{{route('patient_edit', ['id' => $d->id])}}">{{$d->getName()}}</a></td>
                        <td>{{$d->getAge()}} / {{$d->sg()}}</td>
                        <td>{{$d->contact_number}}</td>
                        <td><small>{{$d->getAddress()}}</small></td>
                        <td>{{date('m/d/Y H:i A', strtotime($d->created_at))}} / {{$d->user->name}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination justify-content-center mt-3">
                {{$list->appends(request()->input())->links()}}
            </div>
        </div>
    </div>
</div>
@endsection