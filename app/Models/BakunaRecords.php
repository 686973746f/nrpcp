<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BakunaRecords extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'vaccination_site_id',
        'case_id',
        'is_booster',
        'case_date',
        'case_location',
        'animal_type',
        'animal_type_others',
        'bite_date',
        'bite_type',
        'body_site',
        'category_level',
        'washing_of_bite',
        'rig_date_given',
        'pep_route',
        'brand_name',
        'd0_date',
        'd0_done',
        'd3_date',
        'd3_done',
        'd7_date',
        'd7_done',
        'd14_date',
        'd14_done',
        'd28_date',
        'd28_done',
        'outcome',
        'biting_animal_status',
        'remarks',
    ];

    public function patient() {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function vaccinationsite() {
        return $this->belongsTo(VaccinationSite::class, 'vaccination_site_id');
    }

    public function ifOldCase() {
        //fetch latest and compare to id
        $latest = BakunaRecords::where('patient_id', $this->patient->id)->orderBy('created_at', 'DESC')->first();

        if($latest->id == $this->id) {
            return false;
        }
        else {
            return true;
        }
    }
}
