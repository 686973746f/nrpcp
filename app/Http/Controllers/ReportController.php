<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BakunaRecords;
use App\Models\VaccinationSite;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function linelist_index() {
        if(request()->input('fyear')) {
            $get = BakunaRecords::whereYear('case_date', request()->input('fyear'))->get();
        }
        else {
            $get = BakunaRecords::whereYear('case_date', date('Y'))->get();
        }
        
        return view('report_linelist', [
            'list' => $get,
        ]);
    }

    public function export1(Request $request) {
        $sd = $request->start_date;
        $ed = $request->end_date;
        
        if($request->submit == 'AR') {
            $spreadsheet = IOFactory::load(storage_path('AR_TEMPLATE.xlsx'));
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A6', date('Y').' Quarter Accomplishment Reports from '.date('M d, Y', strtotime($request->start_date)).' to '.date('M d, Y', strtotime($request->end_date)));
            
            $vslist = VaccinationSite::get();

            foreach($vslist as $i => $v) {
                $i = $i + 11; //Row 11 Start ng pag-fill ng Values

                $male_count = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('gender', 'MALE')
                    ->where('register_status', 'VERIFIED');
                })
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $female_count = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('gender', 'FEMALE')
                    ->where('register_status', 'VERIFIED');
                })
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $less15 = BakunaRecords::whereHas('patient', function($q) {
                    $q->where(function ($r) {
                        $r->whereRaw('TIMESTAMPDIFF(YEAR, bdate, CURDATE()) < 15')
                        ->orWhere('age', '<', 15);
                    })
                    ->where('register_status', 'VERIFIED');
                })
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $great15 = BakunaRecords::whereHas('patient', function($q) {
                    $q->where(function ($r) {
                        $r->whereRaw('TIMESTAMPDIFF(YEAR, bdate, CURDATE()) >= 15')
                        ->orWhere('age', '>=', 15);
                    })
                    ->where('register_status', 'VERIFIED');
                })
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $cat1_count = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 1)
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $cat2_count = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 2)
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $cat3_count = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 3)
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $dog_count = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->whereIn('animal_type', ['PD', 'SD'])
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $cat_count = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('animal_type', 'C')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $others_count = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('animal_type', 'O')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $tcv_count = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('outcome', 'C')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $hrig = 0;

                $erig = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('outcome', 'C')
                ->where('category_level', 3)
                ->whereNotNull('rig_date_given')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $booster_count = BakunaRecords::where('vaccination_site_id', $v->id)
                ->where('is_booster', 1)
                ->where('outcome', 'C')
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $ir1 = 0;
                $ir2 = 0;
                $ir3 = 0;

                $sheet->setCellValue('A'.$i, $v->site_name);

                $sheet->setCellValue('C'.$i, $male_count);
                $sheet->setCellValue('D'.$i, $female_count);

                $sheet->setCellValue('F'.$i, $less15);
                $sheet->setCellValue('G'.$i, $great15);

                $sheet->setCellValue('I'.$i, $cat1_count);
                $sheet->setCellValue('J'.$i, $cat2_count);
                $sheet->setCellValue('K'.$i, $cat3_count);

                $sheet->setCellValue('P'.$i, $ir1);
                $sheet->setCellValue('Q'.$i, $ir2);
                $sheet->setCellValue('R'.$i, $ir3);

                $sheet->setCellValue('S'.$i, $tcv_count);
                $sheet->setCellValue('T'.$i, $hrig);
                $sheet->setCellValue('T'.$i, $erig); //ERIG

                $sheet->setCellValue('V'.$i, $dog_count);
                $sheet->setCellValue('W'.$i, $cat_count);
                $sheet->setCellValue('X'.$i, $others_count);

                $sheet->setCellValue('Z'.$i, $booster_count);
                $sheet->setCellValue('AA'.$i, 0); //Pre-exposure Count
            }

            $i = $i+1;

            $fileName = 'RPP_AR_'.date('m_d_Y').'.xlsx';
            ob_clean();
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
            $writer->save('php://output');
            //$writer->save(public_path('TEST.xlsx'));
        }
        else if($request->submit == 'RO4A') {
            $spreadsheet = IOFactory::load(storage_path('RO4A_TEMPLATE.xlsx'));
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'CY: '.date('M d, Y', strtotime($sd)).' - '.date('M d, Y', strtotime($ed)));
            
            $vslist = VaccinationSite::get();

            foreach($vslist as $i => $v) {
                $i = $i + 6; //Row 6 Start ng pag-fill ng Values

                $cat2_total = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 2)
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $cat2_rig = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 2)
                ->whereNotNull('rig_date_given')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $cat2_complete = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 2)
                ->where('outcome', 'C')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $cat2_incomplete = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 2)
                ->where('outcome', 'INC')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();
                
                $cat2_none = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 2)
                ->where('outcome', 'N')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $cat2_died = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 2)
                ->where('outcome', 'D')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $cat3_total = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 3)
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $cat3_rig = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 3)
                ->whereNotNull('rig_date_given')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $cat3_complete = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 3)
                ->where('outcome', 'C')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $cat3_incomplete = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 3)
                ->where('outcome', 'INC')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();
                
                $cat3_none = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 3)
                ->where('outcome', 'N')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $cat3_died = BakunaRecords::whereHas('patient', function($q) {
                    $q->where('register_status', 'VERIFIED');
                })
                ->where('category_level', 3)
                ->where('outcome', 'D')
                ->where('vaccination_site_id', $v->id)
                ->whereBetween('case_date', [$sd, $ed])
                ->count();

                $sheet->setCellValue('A'.$i, $v->site_name);

                $sheet->setCellValue('B'.$i, $cat2_total);
                $sheet->setCellValue('C'.$i, $cat2_rig);
                $sheet->setCellValue('D'.$i, $cat2_complete);
                $sheet->setCellValue('E'.$i, $cat2_incomplete);
                $sheet->setCellValue('F'.$i, $cat2_none);
                $sheet->setCellValue('G'.$i, $cat2_died);

                $sheet->setCellValue('H'.$i, $cat3_total);
                $sheet->setCellValue('I'.$i, $cat3_rig);
                $sheet->setCellValue('J'.$i, $cat3_complete);
                $sheet->setCellValue('K'.$i, $cat3_incomplete);
                $sheet->setCellValue('L'.$i, $cat3_none);
                $sheet->setCellValue('M'.$i, $cat3_died);
            }

            $fileName = 'RPP_RO4A.xlsx';
            ob_clean();
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
            $writer->save('php://output');
            //$writer->save(public_path('TEST.xlsx'));
        }
        else {
            return abort(401);
        }
    }
}