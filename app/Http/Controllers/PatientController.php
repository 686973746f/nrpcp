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

        
    }

    public function edit($id) {

    }
    
    public function update($id, Request $request) {

    }
}
