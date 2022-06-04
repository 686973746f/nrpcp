<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Patient;
use App\Models\VaccineBrand;
use Illuminate\Http\Request;
use App\Models\BakunaRecords;
use App\Models\VaccinationSite;

class VaccinationController extends Controller
{
    public function search_init(Request $request) {
        $request->validate([
            'patient_id' => 'required',
        ]);

        $id = $request->patient_id;

        $p = Patient::findOrFail($id);

        $data = BakunaRecords::where('patient_id', $p->id)->orderBy('created_at', 'DESC')->first();

        if($data) {
            return redirect()->route('encode_existing', ['id' => $data->patient->id]);
        }
        else {
            return redirect()->route('encode_create_new', [
                'id' => $p->id,
            ])
            ->with('msg', 'No Existing Vaccination Records found. You may continue encoding.')
            ->with('msgtype', 'success');
        }
    }

    public function encode_existing($id) {
        $p = Patient::findOrFail($id);

        $data = BakunaRecords::where('patient_id', $p->id)->orderBy('created_at', 'DESC')->first();

        return view('encode_existing', ['data' => $data]);
    }

    public function create_new($id) {
        $p = Patient::findOrFail($id);

        $data = BakunaRecords::where('patient_id', $p->id)->first();

        if(!$data) {
            $vblist = VaccineBrand::orderBy('brand_name', 'ASC')->get();
            $vslist = VaccinationSite::orderBy('id', 'ASC')->get();

            return view('encode_new', [
                'd' => $p,
                'vblist' => $vblist,
                'vslist' => $vslist,
            ]);
        }
        else {
            return abort(401);
        }
    }

    public function create_store($id, Request $request) {
        $request->validate([

        ]);

        $case_id = date('Y').'-'.(BakunaRecords::whereYear('created_at', date('Y'))->count() + 1);

        //Days Calculation (Skip and Wednesdays, Saturdays and Sundays due to Government Office Hours)
        $base_date = $request->d0_date;

        $set_d3_date = Carbon::parse($request->d0_date)->addDays(3);

        if($set_d3_date->dayOfWeek == Carbon::WEDNESDAY) {
            $set_d3_date = Carbon::parse($set_d3_date)->addDays(1);
        }
        else if($set_d3_date->dayOfWeek == Carbon::SATURDAY) {
            $set_d3_date = Carbon::parse($set_d3_date)->addDays(2);
        }
        else if($set_d3_date->dayOfWeek == Carbon::SUNDAY) {
            $set_d3_date = Carbon::parse($set_d3_date)->addDays(1);
        }

        $set_d7_date = Carbon::parse($request->d0_date)->addDays(7);

        if($set_d7_date->dayOfWeek == Carbon::WEDNESDAY) {
            $set_d7_date = Carbon::parse($set_d7_date)->addDays(1);
        }
        else if($set_d7_date->dayOfWeek == Carbon::SATURDAY) {
            $set_d7_date = Carbon::parse($set_d7_date)->addDays(2);
        }
        else if($set_d7_date->dayOfWeek == Carbon::SUNDAY) {
            $set_d7_date = Carbon::parse($set_d7_date)->addDays(1);
        }

        $set_d14_date = Carbon::parse($request->d0_date)->addDays(14);

        if($set_d14_date->dayOfWeek == Carbon::WEDNESDAY) {
            $set_d14_date = Carbon::parse($set_d14_date)->addDays(1);
        }
        else if($set_d14_date->dayOfWeek == Carbon::SATURDAY) {
            $set_d14_date = Carbon::parse($set_d14_date)->addDays(2);
        }
        else if($set_d14_date->dayOfWeek == Carbon::SUNDAY) {
            $set_d14_date = Carbon::parse($set_d14_date)->addDays(1);
        }

        $set_d28_date = Carbon::parse($request->d0_date)->addDays(28);

        if($set_d28_date->dayOfWeek == Carbon::WEDNESDAY) {
            $set_d28_date = Carbon::parse($set_d28_date)->addDays(1);
        }
        else if($set_d28_date->dayOfWeek == Carbon::SATURDAY) {
            $set_d28_date = Carbon::parse($set_d28_date)->addDays(2);
        }
        else if($set_d28_date->dayOfWeek == Carbon::SUNDAY) {
            $set_d28_date = Carbon::parse($set_d28_date)->addDays(1);
        }

        $f = $request->user()->bakunarecord()->create([
            'patient_id' => $id,
            'vaccination_site_id' => $request->vaccination_site_id,
            'case_id' => $case_id,
            'case_date' => $request->case_date,
            'case_location' => ($request->filled('case_location')) ? mb_strtoupper($request->case_location) : NULL,
            'animal_type' => $request->animal_type,
            'animal_type_others' => ($request->animal_type == 'O') ? mb_strtoupper($request->animal_type_others) : NULL,
            'bite_date' => $request->bite_date,
            'bite_type' => $request->bite_type,
            'body_site' => ($request->filled('body_site')) ? mb_strtoupper($request->body_site) : NULL,
            'category_level' => $request->category_level,
            'washing_of_bite' => ($request->washing_of_bite == 'Y') ? 1 : 0,
            'rig_date_given' => $request->rig_date_given,

            'pep_route' => $request->pep_route,
            'brand_name' => $request->brand_name,
            'd0_date' => $request->d0_date,
            'd0_done' => 1,
            'd3_date' => $set_d3_date->format('Y-m-d'),
            'd7_date' => $set_d7_date->format('Y-m-d'),
            'd14_date' => $set_d14_date->format('Y-m-d'),
            'd28_date' => $set_d28_date->format('Y-m-d'),

            'outcome' => $request->outcome,
            'biting_animal_status' => $request->biting_animal_status,
            'remarks' => $request->remarks,
        ]);

        return view('encode_finished', [
            'f' => $f,
        ]);
    }

    public function edit_vaccination($bakuna_id) {

    }

    public function bakuna_again($patient_id) {

    }
}
