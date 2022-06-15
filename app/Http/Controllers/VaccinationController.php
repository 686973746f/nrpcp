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

        return view('encode_existing', ['d' => $data]);
    }

    public function create_new($id) {
        $p = Patient::findOrFail($id);

        $data = BakunaRecords::where('patient_id', $p->id)->first();

        if(!$data) {
            $vblist = VaccineBrand::where('enabled', 1)->orderBy('brand_name', 'ASC')->get();
            $vslist = VaccinationSite::where('enabled', 1)->orderBy('id', 'ASC')->get();

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
            'vaccination_site_id' => 'required|numeric',
            'case_date' => 'required|date',
            'case_location' => 'nullable',
            'animal_type' => 'required',
            'animal_type_others' => ($request->animal_type == 'O') ? 'required' : 'nullable',
            'bite_date' => 'required|date|after_or_equal:2000-01-01|before_or_equal:today',
            'bite_type' => 'required',
            'body_site' => 'nullable',
            'category_level' => 'required',
            'washing_of_bite' => 'required',
            'rig_date_given' => 'nullable|date',
            'pep_route' => 'required',
            'brand_name' => 'required',
            'outcome' => 'required',
            'biting_animal_status' => 'required',
            'remarks' => 'nullable',
        ]);

        $check = BakunaRecords::where('patient_id', $id)
        ->where(function($q) use ($request) {
            $q->whereDate('created_at', date('Y-m-d'))
            ->orWhereDate('d0_date', $request->d0_date);
        })->first();

        if(!($check)) {
            //Check if Booster Dose (If May Case na dati)
            $booster_check = BakunaRecords::where('patient_id', $id)->where('outcome', 'C')->first();
            if($booster_check) {
                $is_booster = 1;
            }
            else {
                $is_booster = 0;
            }

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
                'is_booster' => $is_booster,
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
            ])
            ->with('msg', 'You have finished your 1st Dose of your Anti-Rabies Vaccine.')
            ->with('dose', 1);
        }
        else {
            return redirect()->route('home')
            ->with('msg', 'You are not allowed to do that')
            ->with('msgtype', 'warning');
        }
    }

    public function encode_edit($bakuna_id) {
        $p = BakunaRecords::findOrFail($bakuna_id);

        $vblist = VaccineBrand::orderBy('brand_name', 'ASC')->get();
        $vslist = VaccinationSite::orderBy('id', 'ASC')->get();

        return view('encode_edit', [
            'd' => $p,
            'vblist' => $vblist,
            'vslist' => $vslist,
        ]);
    }

    public function encode_update($bakuna_id, Request $request) {
        $request->validate([
            'vaccination_site_id' => 'required|numeric',
            'case_date' => 'required|date',
            'case_location' => 'nullable',
            'animal_type' => 'required',
            'animal_type_others' => ($request->animal_type == 'O') ? 'required' : 'nullable',
            'bite_date' => 'required|date|after_or_equal:2000-01-01|before_or_equal:today',
            'bite_type' => 'required',
            'body_site' => 'nullable',
            'category_level' => 'required',
            'washing_of_bite' => 'required',
            'rig_date_given' => 'nullable|date',
            'pep_route' => 'required',
            'brand_name' => 'required',
            'outcome' => 'required',
            'biting_animal_status' => 'required',
            'remarks' => 'nullable',
        ]);

        $b = BakunaRecords::findOrFail($bakuna_id);

        $b->vaccination_site_id = $request->vaccination_site_id;
        $b->case_date = $request->case_date;
        $b->case_location = ($request->filled('case_location')) ? mb_strtoupper($request->case_location) : NULL;
        $b->animal_type = $request->animal_type;
        $b->animal_type_others = ($request->animal_type == 'O') ? mb_strtoupper($request->animal_type_others) : NULL;
        $b->bite_date = $request->bite_date;
        $b->bite_type = $request->bite_type;
        $b->body_site = ($request->filled('body_site')) ? mb_strtoupper($request->body_site) : NULL;
        $b->category_level = $request->category_level;
        $b->washing_of_bite = ($request->washing_of_bite == 'Y') ? 1 : 0;
        $b->rig_date_given = $request->rig_date_given;

        $b->pep_route = $request->pep_route;
        $b->brand_name = $request->brand_name;

        $b->outcome = $request->outcome;
        $b->biting_animal_status = $request->biting_animal_status;
        $b->remarks = $request->remarks;

        //Checking of Outcome on Category 3 with Erig
        if($b->category_level == 3 && $b->d28_done == 1 && !is_null($b->rig_date_given)) {
            $b->outcome == 'C';
        }

        if($b->isDirty()) {
            $b->save();
        }

        return redirect()->back()
        ->with('msg', 'Patient Vaccination Information was updated successfully.')
        ->with('msgtype', 'success');
    }

    public function bakuna_again($patient_id) {
        $b = BakunaRecords::where('patient_id', $patient_id)->whereNotIn('outcome', ['C', 'INC'])->orderBy('created_at', 'DESC')->first();

        if($b) {
            $vblist = VaccineBrand::where('enabled', 1)->orderBy('brand_name', 'ASC')->get();
            $vslist = VaccinationSite::where('enabled', 1)->orderBy('id', 'ASC')->get();
            
            //Check duration 3 months
            $bcheck = BakunaRecords::whereDate('case_date', '=>', date('Y-m-d', strtotime('-3 Months')))->first();
            if($bcheck) {
                return redirect()->back()
                ->with('msg', 'Unable to process. Patient was vaccinated 90 Days (3 Months) ago. Booster is not required')
                ->with('msgtype', 'warning');
            }
            else {
                return view('encode_new', [
                    'd' => $b->patient,
                    'vblist' => $vblist,
                    'vslist' => $vslist,
                ]);
            }
        }
        else {
            return abort(401);
        }
    }

    public function encode_process($br_id, $dose) {
        $get_br = BakunaRecords::findOrFail($br_id);

        if(!is_null($get_br->brand_name)) {
            if($dose == 1) {
                if($get_br->ifAbleToProcessD0() == 'Y') {
                    $get_br->d0_done = 1;
                }
                else {
                    return abort(401);
                }

                if($get_br->patient->register_status == 'PENDING') {
                    $c = Patient::findOrFail($get_br->patient_id);
                    
                    $c->register_status = 'VERIFIED';
                    $c->save();
                }
    
                $msg = 'You have finished your 1st Dose of your Anti-Rabies Vaccine.';
            }
            else if($dose == 2) { //Day 3
                if($get_br->ifAbleToProcessD3() == 'Y') {
                    $get_br->d3_done = 1;
                }
                else {
                    return abort(401);
                }
    
                if($get_br->is_booster == 1) {
                    $get_br->outcome = 'C';
                    $msg = 'You have finished your Booster Dose of your Anti-Rabies Vaccine.';
                }
                else {
                    $msg = 'You have finished your 2nd Dose of your Anti-Rabies Vaccine.';
    
                    //Check if delay ang d3 bakuna then move next schedules
                    if($get_br->d3_date != date('Y-m-d')) {
                        $ad = Carbon::parse($get_br->d3_date);
                        $bd = Carbon::parse(date('Y-m-d'));
    
                        $date_diff = $ad->diffInDays($bd);
                        if($date_diff >= 3) {
                            $get_br->d3_date = date('Y-m-d');
                            $get_br->d7_date = Carbon::parse($get_br->d7_date)->addDays(4)->format('Y-m-d');
                            $get_br->d14_date = Carbon::parse($get_br->d14_date)->addDays(4)->format('Y-m-d');
                            $get_br->d28_date = Carbon::parse($get_br->d28_date)->addDays(4)->format('Y-m-d');
                        }
                    }
                }
            }
            else if($dose == 3) { //Day 7
                if($get_br->d7_date == date('Y-m-d') && $get_br->d0_done == 1 && $get_br->d3_done == 1 && $get_br->d7_done == 0) {
                    $get_br->d7_done = 1;
                }
                else {
                    return abort(401);
                }
    
                $msg = 'You have finished your 3rd Dose of your Anti-Rabies Vaccine.';
            }
            else if($dose == 4 && $get_br->pep_route == 'IM') { //Day 14
                if($get_br->d14_date == date('Y-m-d') && $get_br->d0_done == 1 && $get_br->d3_done == 1 && $get_br->d7_done == 1 && $get_br->d14_done == 0) {
                    $get_br->d14_done = 1;
                }
                else {
                    return abort(401);
                }
                
                $msg = 'You have finished your 4th Dose of your Anti-Rabies Vaccine.';
            }
            else if($dose == 5) { //Day 28
                if($get_br->pep_route == 'IM') {
                    if($get_br->d28_date == date('Y-m-d') && $get_br->d0_done == 1 && $get_br->d3_done == 1 && $get_br->d7_done == 1 && $get_br->d14_done == 1 && $get_br->d28_done == 0) {
                        $get_br->d28_done = 1;
                    }
                    else {
                        return abort(401);
                    }
                }
                else if($get_br->pep_route == 'ID') { //Skip 14 Day
                    if($get_br->d28_date == date('Y-m-d') && $get_br->d0_done == 1 && $get_br->d3_done == 1 && $get_br->d7_done == 1 && $get_br->d28_done == 0) {
                        $get_br->d28_done = 1;
                    }
                    else {
                        return abort(401);
                    }
                }
    
                if($get_br->category_level == 2) {
                    $get_br->outcome = 'C';
                }
                else if($get_br->category_level == 3 && !is_null($get_br->rig_date_given)) {
                    $get_br->outcome = 'C';
                }
    
                $msg = 'Congratulations. You have completed your doses of Anti-Rabies Vaccine!';
            }
    
            $get_br->save();
    
            return view('encode_finished', [
                'f' => $get_br,
            ])
            ->with('msg', $msg)
            ->with('dose' , $dose);
        }
        else {
            return redirect()->back()
            ->with('msg', 'Unable to proceed. Please put Vaccine Brand first, click [Save] then try again.')
            ->with('msgtype', 'warning');
        }
    }

    public function qr_quicksearch(Request $request) {
        $sqr = $request->qr;

        $search = Patient::where('qr', $sqr)->first();

        if($search) {
            //load latest bakuna record

            $b = BakunaRecords::where('patient_id', $search->id)->orderBy('created_at', 'DESC')->first();
            if($b) {
                return redirect()->route('encode_existing', ['id' => $search->id]);
            }
            else {
                return redirect()->back()
                ->with('msg', 'No Anti-Rabies Vaccination Record found for '.$search->getName())
                ->with('msgtype', 'warning');
            }
        }
        else {
            return redirect()->back()
            ->with('msg', 'User does not exist on the server.')
            ->with('msgtype', 'warning');
        }
    }

    public function override_schedule($id) {
        $d = BakunaRecords::findOrFail($id);

        return view('encode_schedule_override', [
            'd' => $d,
        ]);
    }

    public function override_schedule_process($id, Request $request) {
        $d = BakunaRecords::findOrFail($id);

        $request->validate([
            
        ]);

        if($d->d0_done == 0) {
            $d->d0_date = $request->d0_date;

            if($request->d0_ostatus == 'C') {
                $d->d0_done = 1;
            }
        }

        if($d->d3_done == 0) {
            $d->d3_date = $request->d3_date;

            if($request->d3_ostatus == 'C') {
                $d->d0_done = 1;
                $d->d3_done = 1;

                if($d->outcome == 'INC' && $d->is_booster == 1) {
                    $d->outcome = 'C';
                }
            }
        }

        if($d->is_booster == 0) {
            if($d->d7_done == 0) {
                $d->d7_date = $request->d7_date;

                if($request->d7_ostatus == 'C') {
                    $d->d0_done = 1;
                    $d->d3_done = 1;
                    $d->d7_done = 1;
                }
            }
    
            if($d->pep_route == 'IM') {
                if($d->d14_done == 0) {
                    $d->d14_date = $request->d14_date;

                    if($request->d14_ostatus == 'C') {
                        $d->d0_done = 1;
                        $d->d3_done = 1;
                        $d->d7_done = 1;
                        $d->d14_done = 1;
                    }
                }
            }
    
            if($d->d28_done == 0) {
                $d->d28_date = $request->d28_date;

                if($request->d28_ostatus == 'C') {
                    $d->d0_done = 1;
                    $d->d3_done = 1;
                    $d->d7_done = 1;
                    $d->d14_done = 1;
                    $d->d28_done = 1;

                    if($d->outcome == 'INC' && $d->is_booster == 0) {
                        $d->outcome = 'C';
                    }
                }
            }
        }

        if($d->isDirty()) {
            $d->save();
        }

        return redirect()->route('encode_edit', ['br_id' => $d->id])
        ->with('msg', 'Schedule has been manually changed successfully.')
        ->with('msgtype', 'success');
    }
}
