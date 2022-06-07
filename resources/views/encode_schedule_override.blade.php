@extends('layouts.app')

@section('content')
<form action="{{route('override_schedule_process', ['br_id' => $d->id])}}" method="POST">
    @csrf
    <div class="container">
        <div class="card">
            <div class="card-header">Schedule Override</div>
            <div class="card-body">
                <div class="alert alert-primary" role="alert">
                    <b class="text-danger">Note:</b> Only the pending schedule can be manually changed.
                </div>
                <table class="table table-bordered text-center">
                    <thead class="bg-light">
                        <tr>
                            <th>Schedule</th>
                            <th>Date</th>
                            <th>Change to</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td scope="row"><b>Day 0</b></td>
                            <td>{{date('m/d/Y (l)', strtotime($d->d0_date))}}</td>
                            <td>
                                <input type="date" class="form-control" name="d0_date" id="d0_date" value="{{old('d0_date', $d->d0_date)}}" {{($d->d0_done == 1) ? 'disabled' : 'required'}}>
                            </td>
                        </tr>
                        <tr>
                            <td scope="row"><b>Day 3</b></td>
                            <td>{{date('m/d/Y (l)', strtotime($d->d3_date))}}</td>
                            <td>
                                <input type="date" class="form-control" name="d3_date" id="d3_date" value="{{old('d0_date', $d->d3_date)}}" min="{{date('Y-m-d', strtotime($d->d0_date))}}" {{($d->d3_done == 1) ? 'disabled' : 'required'}}>
                            </td>
                        </tr>
                        @if($d->is_booster == 0)
                        <tr>
                            <td scope="row"><b>Day 7</b></td>
                            <td>{{date('m/d/Y (l)', strtotime($d->d7_date))}}</td>
                            <td>
                                <input type="date" class="form-control" name="d7_date" id="d7_date" value="{{old('d0_date', $d->d7_date)}}" min="{{date('Y-m-d', strtotime($d->d3_date))}}" {{($d->d7_done == 1) ? 'disabled' : 'required'}}>
                            </td>
                        </tr>
                        @if($d->pep_route != 'ID')
                        <tr>
                            <td scope="row"><b>Day 14</b></td>
                            <td>{{date('m/d/Y (l)', strtotime($d->d14_date))}}</td>
                            <td>
                                <input type="date" class="form-control" name="d14_date" id="d14_date" value="{{old('d0_date', $d->d14_date)}}" min="{{date('Y-m-d', strtotime($d->d7_date))}}" {{($d->d14_done == 1) ? 'disabled' : 'required'}}>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td scope="row"><b>Day 28</b></td>
                            <td>{{date('m/d/Y (l)', strtotime($d->d28_date))}}</td>
                            <td>
                                <input type="date" class="form-control" name="d28_date" id="d28_date" value="{{old('d0_date', $d->d28_date)}}" min="{{date('Y-m-d', strtotime($d->d14_date))}}" {{($d->d28_done == 1) ? 'disabled' : 'required'}}>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</form>
@endsection