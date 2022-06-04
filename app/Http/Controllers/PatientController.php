<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $foundunique = false;

        while(!$foundunique) {
            $for_qr = Str::random(20);
            
            $search = Patient::where('qr', $for_qr)->first();
            if(!$search) {
                $foundunique = true;
            }
        }

        if(Patient::ifDuplicateFound($request->lname, $request->fname, $request->mname, $request->suffix, $request->bdate)) {
            return back()->with('msg', 'Unable to register new patient. Patient details already exists on the server')
            ->with('msgtype', 'danger');
        }
        else {
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
    
                'qr' => $for_qr,
                'remarks' => ($request->filled('remarks')) ? $request->remarks : NULL,
            ]);
    
            return redirect()->route('patient_index')
            ->with('msg', 'Patient was added successfully.')
            ->with('msgtype', 'success');
        }
    }

    public function edit($id) {

    }
    
    public function update($id, Request $request) {

    }

    public function ajaxList(Request $request) {
        $list = [];
        if($request->has('q') && strlen($request->input('q')) > 1) {
            $search = mb_strtoupper($request->q);

            $data = Patient::where(function ($query) use ($search) {
                $query->where(DB::raw('CONCAT(lname," ",fname," ", mname)'), 'LIKE', "%".str_replace(',','', $search)."%")
                ->orWhere(DB::raw('CONCAT(lname," ",fname)'), 'LIKE', "%".str_replace(',','', $search)."%")
                ->orWhere('id', $search);
            })->get();

            foreach($data as $item) {
                array_push($list, [
                    'id' => $item->id,
                    'text' => $item->getName().' | '.$item->getAge().'/'.substr($item->gender,0,1).' | '.date('m/d/Y', strtotime($item->bdate)),
                ]);
            }
        }

        return response()->json($list);
    }
}
