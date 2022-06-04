@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success">
                <div class="card-header text-center bg-success text-white"><strong><i class="fa-solid fa-circle-check me-2"></i>Success!</strong></div>
                <div class="card-body text-center">
                    <p>You have finished your 1st Dose of Anti-Rabies Vaccine.</p>
                    <p>Please see the details below for your next schedule:</p>
                    <hr>
                    {!! QrCode::size(150)->generate($f->patient->qr) !!}
                    <p><strong>Registration #:</strong> <u>{{$f->case_id}}</u></p>
                    <p><strong>Name:</strong> <u>{{$f->patient->getName()}}</u></p>
                    <p><strong>Age/Gender:</strong> <u>{{$f->patient->getAge()}} / {{$f->patient->sg()}}</u></p>
                    <p><strong>Address:</strong> <u>{{$f->patient->getAddressMini()}}</u></p>
                    <p><strong>Date of Bite:</strong> <u>{{date('m/d/Y (l)', strtotime($f->bite_date))}}</u> • <strong>Body Part:</strong> <u>{{$f->body_site}}</u></p>
                    <p><strong>Category:</strong> <u>{{$f->category_level}}</u></p>
                    <p><strong>Health Facility:</strong> <u>{{$f->vaccinationsite->site_name}}</u></p>

                    <table class="table table-bordered table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th colspan="2">Vaccine Brand: {{$f->brand_name}}</th>
                            </tr>
                            <tr class="text-center">
                                <th>Dose Schedule</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <tr>
                                <td><strong>3rd Day</strong></td>
                                <td>{{date('m/d/Y (l)', strtotime($f->d3_date))}}</td>
                            </tr>
                            <tr>
                                <td><strong>7th Day</strong></td>
                                <td>{{date('m/d/Y (l)', strtotime($f->d7_date))}}</td>
                            </tr>
                            <tr>
                                <td><strong>14th Day</strong></td>
                                <td>{{date('m/d/Y (l)', strtotime($f->d14_date))}}</td>
                            </tr>
                            <tr>
                                <td><strong>28th Day</strong></td>
                                <td>{{date('m/d/Y (l)', strtotime($f->d28_date))}}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                </div>
                <div class="card-footer text-center">
                    <a href="{{route('home')}}"><i class="fa-solid fa-house me-2"></i>Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection