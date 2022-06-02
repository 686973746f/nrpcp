<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'lname',
        'fname',
        'mname',
        'suffix',
        'bdate',
        'gender',
        'contact_number',
        'address_region_code',
        'address_region_text',
        'address_province_code',
        'address_province_text',
        'address_muncity_code',
        'address_muncity_text',
        'address_brgy_code',
        'address_brgy_text',
        'address_street',
        'address_houseno',
        'remarks',

        'created_by',
        'updated_by',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getName() {
        return $this->lname.", ".$this->fname.' '.$this->suffix." ".$this->mname;
    }

    public function getNameFormal() {
        return $this->fname." ".$this->mname.' '.$this->lname." ".$this->suffix;
    }

    public function getAddress() {
        return $this->address_houseno.', '.$this->address_street.', BRGY. '.$this->address_brgy_text.', '.$this->address_muncity_text.', '.$this->address_province_text;
    }

    public function sg() {
        return substr($this->sex,0,1);
    }

    public function getAge() {
        if(Carbon::parse($this->attributes['bdate'])->age > 0) {
            return Carbon::parse($this->attributes['bdate'])->age;
        }
        else {
            if (Carbon::parse($this->attributes['bdate'])->diff(\Carbon\Carbon::now())->format('%m') == 0) {
                return Carbon::parse($this->attributes['bdate'])->diff(\Carbon\Carbon::now())->format('%d DAYS');
            }
            else {
                return Carbon::parse($this->attributes['bdate'])->diff(\Carbon\Carbon::now())->format('%m MOS');
            }
        }
    }

    public function getAgeInt() {
        return Carbon::parse($this->attributes['bdate'])->age;
    }
}