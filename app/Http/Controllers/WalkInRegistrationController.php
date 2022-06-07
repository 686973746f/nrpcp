<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\BakunaRecords;

class WalkInRegistrationController extends Controller
{
    public function walkin_part1() {
        return view('walkin_part1');
    }

    public function walkin_part2() {
        $lname = mb_strtoupper(request()->input('lname'));
        $fname = mb_strtoupper(request()->input('fname'));
        $mname = mb_strtoupper(request()->input('mname'));
        $suffix = mb_strtoupper(request()->input('suffix'));
        $bdate = request()->input('bdate');

        $b = Patient::where('lname', $lname)
        ->where('fname', $fname)
        ->where(function ($q) use ($mname) {
            $q->where('mname', $mname)
            ->orWhereNull('mname');
        })
        ->where(function ($q) use ($suffix) {
            $q->where('suffix', $suffix)
            ->orWhereNull('suffix');
        })
        ->whereDate('bdate', $bdate)
        ->first();

        if($b) {
            $br = BakunaRecords::where('patient_id', $b->id)->orderBy('created_at', 'DESC')->first();
            
            if($br) {
                if($br->outcome == 'C') {
                    if(date('Y-m-d') > Carbon::parse($br->d0_date)->addDays(90)) {
                        return view('walkin_part2');
                    }
                    else {
                        return redirect()->back()
                        ->with('msg', 'Unable to proceed. Hindi pa lagpas ng 90 na araw ang iyong huling bakuna.')
                        ->with('msgtype', 'danger');
                    }
                }
                else {
                    return redirect()->back()
                    ->with('msg', 'Hindi maaaring magpatuloy. Ikaw ay kasalukuyang may tinatapos pa na bakuna.')
                    ->with('msgtype', 'danger');
                }
            }
            else {
                return view('walkin_part2');
            }
        }
        else {
            return view('walkin_part2');
        }
    }

    public function walkin_part3(Request $request) {
        return view('walkin_part3');
    }
}
