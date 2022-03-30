<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AlumniController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $datas = [];
            $totalData = 0;
            $totalFiltered = 0;
            $totalData = Alumni::count();
            $buildQuery = Alumni::selectRaw("row_number() over (order by al_alumni_detail.alumni_code asc) as seqnum,al_alumni_detail.*,facultys.faculty_name_th,majors.major_name_th")
                ->leftjoin("facultys", "al_alumni_detail.fac_code", "=", "facultys.faculty_code")
                ->leftjoin("majors", "al_alumni_detail.major_code", "=", "majors.major_code");

            if (Str::of(request()->input("year"))->trim()->isNotEmpty()) {
                $buildQuery->where("al_alumni_detail.graduate_year", request()->input("year"));
            }

            if (Str::of(request()->input("faculty"))->trim()->isNotEmpty()) {
                $buildQuery->where("al_alumni_detail.fac_code", request()->input("faculty"));
            }

            if (Str::of(request()->input("major"))->trim()->isNotEmpty()) {
                $buildQuery->where("al_alumni_detail.major_code", request()->input("major"));
            }

            if (Str::of(request()->input("name"))->trim()->isNotEmpty()) {
                $target = request()->input("name");
                $buildQuery->where(function ($query) use ($target) {
                    $query->where("alumni_code", "LIKE", "%{$target}%")
                        ->orWhere("alumni_name_tha", "LIKE", "%{$target}%")
                        ->orWhere("alumni_name_eng", "LIKE", "%{$target}%")
                        ->orWhere("alumni_lastname_tha", "LIKE", "%{$target}%")
                        ->orWhere("alumni_lastname_eng", "LIKE", "%{$target}%");
                });
            }

            $totalFiltered = $buildQuery->count();
            $datas = $buildQuery->offset(request()->input("start", 1))
                ->limit(request()->input("length", 10))
                ->get();
            $json_data = [
                "draw" => intval(request()->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $datas,
            ];
            return response()->json($json_data);
        }

        return view("alumni.index", [
            "ms_faculty" => Faculty::all(),
            "ms_major" => Major::all(),
        ]);
    }
}
