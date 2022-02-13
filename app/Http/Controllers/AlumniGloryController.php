<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlumniGloryRequest;
use App\Models\AlumniGlory;
use App\Models\AwardType;
use App\Models\EducationLevel;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\Models\AlumniGloryImage;

class AlumniGloryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $datas = [];
            $totalData = 0;
            $totalFiltered = 0;
            $columnSorts = [DB::raw("seqnum"), "alumni_glories.created_at", "alumni_glories.updated_at"];
            $typeQuery = request()->input("type");
            $whereCondition = function ($query) use ($typeQuery) {
                if ($typeQuery != "3") {
                    $query->where("approved", $typeQuery)->where("alumni_glories.status", 1);
                } else {
                    $query->where("status", 0);
                }
            };
            $totalData = AlumniGlory::where($whereCondition)->count();

            $buildQuery = AlumniGlory::selectRaw("row_number() over (order by alumni_glories.alumni_glory_id desc) as seqnum,alumni_glories.*,u_create.fullname as u_create_name,u_update.fullname as u_update_name
                ,facultys.faculty_name_th,majors.major_name_th,alumni_glory_images.file_name,alumni_glory_images.file_origin_name,alumni_glory_images.file_size,alumni_glory_images.file_path")
                ->join("users as u_create", "alumni_glories.user_create_id", "=", "u_create.id")
                ->leftjoin("facultys", "alumni_glories.faculty_id", "=", "facultys.faculty_id")
                ->leftjoin("majors", "alumni_glories.major_id", "=", "majors.major_id")
                ->leftjoin("users as u_update", "alumni_glories.user_update_id", "=", "u_update.id")
                ->leftjoin("alumni_glory_images","alumni_glories.alumni_glory_id","=","alumni_glory_images.alumni_glory_id")
                ->where($whereCondition);

            if (Str::of(request()->input("search.value"))->trim()->isNotEmpty()) {
                $target = request()->input("search.value");
                $buildQuery->where("title", "LIKE", "%{$target}%");
            }
            $totalFiltered = $buildQuery->count();
            $buildQuery->offset(request()->input("start", 1))
                ->limit(request()->input("length", 10));

            if (request()->has("order.0.column")) {
                $buildQuery->orderBy($columnSorts[request()->input("order.0.column")], request()->input("order.0.dir"));
            }

            $datas = collect($buildQuery->get()->toArray())
                ->transform(function ($i) {
                    $i["file"] = [
                        "name" => $i["file_origin_name"],
                        "path" => asset("storage/" . $i["file_path"]),
                        "size" => $i["file_size"],
                    ];
                    $i["created_at"] = Helper::convertToDateTimeYTh($i["created_at"]);
                    $i["updated_at"] = Helper::convertToDateTimeYTh($i["updated_at"]);
                    $i["action"]["view"] = route("alumni-glory.view", ["alumni" => $i["alumni_glory_id"]]);
                    $i["action"]["edit"] = route("alumni-glory.edit", ["alumni" => $i["alumni_glory_id"]]);
                    $i["action"]["delete"] = route("alumni-glory.destroy", ["alumni" => $i["alumni_glory_id"]]);
                    $i["action"]["approve"] = route("alumni-glory.approve", ["alumni" => $i["alumni_glory_id"]]);
                    $i["action"]["active"] = route("alumni-glory.active", ["alumni" => $i["alumni_glory_id"]]);
                    $i["action"]["restore"] = route("alumni-glory.restore", ["alumni" => $i["alumni_glory_id"]]);
                    return $i;
                });

            $json_data = [
                "draw" => intval(request()->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $datas,
            ];
            return response()->json($json_data);
        }
        return view("alumni-glories.index", [
            "badge" => AlumniGlory::where([["approved", "=", 0], ["status", "=", 1]])->count(),
            "records" => AlumniGlory::where([["approved", "=", 1], ["status", "=", 1]])->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("alumni-glories.form",[
            "ms_faculty" => Faculty::all(),
            "ms_major" => Major::all(),
            "ms_education" => EducationLevel::all(),
            "ms_award_type" => AwardType::noneSubType()->active()->get(),
            "ms_award_sub_type" => AwardType::SubType()->active()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AlumniGloryRequest $request)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request) {
                $upload = false;
                if ($request->hasFile("images")) {
                    if (!in_array($request->file("images")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $upload = true;
                }

                $model = AlumniGlory::create($request->merge(["user_create_id" => auth()->id()])->except(["images"]));
                if ($model) {
                    if ($upload) {
                        $file = $request->file("images");
                        $extension = $file->getClientOriginalExtension();
                        $directory = "alumni-glories" . DIRECTORY_SEPARATOR . $model->alumni_glory_id;
                        $filename = uniqid() . "." . $extension;
                        $path = $file->storeAs($directory, $filename, "public");
                        if ($path) {
                            $model->image()->save(new AlumniGloryImage([
                                "file_name" => $filename,
                                "file_origin_name" => $file->getClientOriginalName(),
                                "file_type" => $file->getClientMimeType(),
                                "file_size" => $file->getSize(),
                                "file_path" => $path,
                            ]));
                        }
                    }
                }
            });
            return redirect()->route("alumni-glory.index")->with("success", "บันทึกข้อมูลศิษย์เก่าดีเด่นเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AlumniGlory  $alumniGlory
     * @return \Illuminate\Http\Response
     */
    public function show(AlumniGlory $alumni)
    {
        return view("alumni-glories.form",[
            "data" => $alumni,
            "ms_faculty" => Faculty::all(),
            "ms_major" => Major::all(),
            "ms_education" => EducationLevel::all(),
            "ms_award_type" => AwardType::noneSubType()->active()->get(),
            "ms_award_sub_type" => AwardType::SubType()->active()->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AlumniGlory  $alumniGlory
     * @return \Illuminate\Http\Response
     */
    public function edit(AlumniGlory $alumni)
    {
        return view("alumni-glories.form",[
            "data" => $alumni,
            "ms_faculty" => Faculty::all(),
            "ms_major" => Major::all(),
            "ms_education" => EducationLevel::all(),
            "ms_award_type" => AwardType::noneSubType()->active()->get(),
            "ms_award_sub_type" => AwardType::SubType()->active()->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AlumniGlory  $alumniGlory
     * @return \Illuminate\Http\Response
     */
    public function update(AlumniGloryRequest $request, AlumniGlory $alumni)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request,$alumni) {
                $upload = false;
                if ($request->hasFile("images")) {
                    if (!in_array($request->file("images")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $upload = true;
                }

                $alumni->update($request->merge(["user_update_id" => auth()->id()])->except(["images"]));
                if ($upload) {
                    $file = $request->file("images");
                    $extension = $file->getClientOriginalExtension();
                    $directory = "alumni-glories" . DIRECTORY_SEPARATOR . $alumni->alumni_glory_id;
                    $filename = uniqid() . "." . $extension;
                    $path = $file->storeAs($directory, $filename, "public");
                    if ($path) {
                        AlumniGloryImage::updateOrCreate(
                            ["alumni_glory_id" => $alumni->alumni_glory_id],
                            [
                                "file_name" => $filename,
                                "file_origin_name" => $file->getClientOriginalName(),
                                "file_type" => $file->getClientMimeType(),
                                "file_size" => $file->getSize(),
                                "file_path" => $path,
                            ]
                        );
                    }
                }
            });
            return redirect()->route("alumni-glory.index")->with("success", "บันทึกข้อมูลศิษย์เก่าดีเด่นเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AlumniGlory  $alumniGlory
     * @return \Illuminate\Http\Response
     */
    public function destroy(AlumniGlory $alumni)
    {
        $alumni->update(["status" => 0 , "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลศิษย์เก่าดีเด่นเรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\AlumniGlory  $alumni
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, AlumniGlory $alumni)
    {
        try {
            DB::transaction(function () use ($request, $alumni) {
                $alumni->update([
                    "approved" => $request->action,
                    "note" => $request->input("note"),
                    "status" => $request->action == "2"
                        ? 0
                        : $alumni->status,
                    "user_approve_id" => auth()->id(),
                    "approved_at" => DB::raw("CURRENT_DATE"),
                ]);
            });
            return redirect()->back()->with("success", "บันทึกข้อมูลศิษย์เก่าดีเด่นเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\AlumniGlory  $alumni
     * @return \Illuminate\Http\Response
     */
    public function restore(AlumniGlory $alumni)
    {
        $alumni->update(["status" => 1 , "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลศิษย์เก่าดีเด่นเรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\AlumniGlory  $alumni
     * @return \Illuminate\Http\Response
     */
    public function active(AlumniGlory $alumni)
    {
        $alumni->update(["active" => $alumni->active == 1 ? 0 : 1]);
        return response()->json(["status" => true]);
    }
}
