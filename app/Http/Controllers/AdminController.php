<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VaccinationSite;

class AdminController extends Controller
{
    public function vaccinationsite_index() {
        $list = VaccinationSite::orderBy('site_name', 'ASC')->paginate(10);

        return view('vaccinationsite_index', [
            'list' => $list,
        ]);
    }
    
    public function vaccinationsite_store(Request $request) {
        $request->validate([
            'site_name' => 'required',
        ]);

        VaccinationSite::create([
            'site_name' => $request->site_name,
        ]);

        return redirect()->route('vaccinationsite_index')
        ->with('msg', 'Vaccination Site was added successfully.')
        ->with('msgtype', 'success');
    }
}
