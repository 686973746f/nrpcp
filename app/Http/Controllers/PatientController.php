<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index() {
        $list = Patient::orderBy('lname', 'ASC')->paginate(10);

        return view('patientlist_index', [
            'list' => $list,
        ]);
    }

    public function create() {
        return view('patientlist_create');
    }

    public function store(Request $request) {
        $request->validate([

        ]);

        $create = $request->user()->patient()->create([
            'lname' => $request->lname,
            'fname' => $request->fname,
            'mname' => ($request->filled('mname')) ? $request->mname : NULL,
            'suffix' => ($request->filled('suffix')) ? $request->suffix : NULL,
            'bdate' => $request->bdate,
            'gender' => $request->gender,
            'contact_number' => $request->contact_number,
            'address_region_code' => $request->address_region_code,
            'address_region_text' => $request->address_region_text,
            'address_province_code' => $request->address_province_code,
            'address_province_text' => $request->address_province_text,
            'address_muncity_code' => $request->address_muncity_code,
            'address_muncity_text' => $request->address_muncity_text,
            'address_brgy_code' => $request->address_brgy_text,
            'address_brgy_text' => $request->address_brgy_text,
            'address_street' => $request->address_street,
            'address_houseno' => $request->address_houseno,

            'remarks' => ($request->filled('remarks')) ? $request->remarks : NULL,
        ]);

        return redirect()->route('patient_index')
        ->with('msg', 'Patient was added successfully.')
        ->with('msgtype', 'success');
    }

    public function edit($id) {

    }
    
    public function update($id, Request $request) {

    }
}
