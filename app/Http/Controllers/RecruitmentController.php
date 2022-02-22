<?php

namespace App\Http\Controllers;

use App\Models\Recruitment;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Http\Requests\RecruitmentRequest;
use App\Models\RecruitmentImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RecruitmentController extends Controller
{
    public function __construct() {
        $this->middleware("can:view-recruitment");
        $this->middleware("can:create-recruitment")->only(["create","store"]);
        $this->middleware("can:update-recruitment")->only("update");
        $this->middleware("can:del-recruitment")->only("destroy");
        $this->middleware("can:approve-recruitment")->only("approve");
    }
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
            $columnSorts = [DB::raw("seqnum"), "recruitments.created_at", "recruitments.updated_at"];
            $typeQuery = request()->input("type");
            $whereCondition = function ($query) use ($typeQuery) {
                if ($typeQuery != "3") {
                    $query->where("approved", $typeQuery)->where("status", 1);
                } else {
                    $query->where("status", 0);
                }
            };
            $totalData = Recruitment::where($whereCondition)->count();

            $buildQuery = Recruitment::selectRaw("row_number() over (order by recruitments.recruitment_id desc) as seqnum,recruitments.*,u_create.fullname as u_create_name,u_update.fullname as u_update_name
                ,recruitment_images.file_name,recruitment_images.file_origin_name,recruitment_images.file_size,recruitment_images.file_path")
                ->join("users as u_create", "recruitments.user_create_id", "=", "u_create.id")
                ->leftjoin("users as u_update", "recruitments.user_update_id", "=", "u_update.id")
                ->leftjoin("recruitment_images", "recruitments.recruitment_id", "=", "recruitment_images.recruitment_id")
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
                    $i["action"]["view"] = route("recruitment.view", ["recruitment" => $i["recruitment_id"]]);
                    $i["action"]["edit"] = route("recruitment.edit", ["recruitment" => $i["recruitment_id"]]);
                    $i["action"]["delete"] = route("recruitment.destroy", ["recruitment" => $i["recruitment_id"]]);
                    $i["action"]["active"] = route("recruitment.active", ["recruitment" => $i["recruitment_id"]]);
                    $i["action"]["restore"] = route("recruitment.restore", ["recruitment" => $i["recruitment_id"]]);
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
        return view("recruitments.index", [
            "badge" => Recruitment::where([["approved", "=", 0], ["status", "=", 1]])->count(),
            "records" => Recruitment::where([["approved", "=", 1], ["status", "=", 1]])->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("recruitments.form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RecruitmentRequest $request)
    {
        $request->validated();
        try {
            DB::transaction(function() use ($request){
                $upload = false;
                if ($request->hasFile("files")) {
                    if (!in_array($request->file("files")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $upload = true;
                }
                $model = Recruitment::create($request->merge(["user_create_id" => auth()->id()])->except(["files"]));
                if ($upload) {
                    $file = $request->file("files");
                    $extension = $file->getClientOriginalExtension();
                    $directory = "recruitments" . DIRECTORY_SEPARATOR . $model->recruitment_id;
                    $filename = uniqid() . "." . $extension;
                    $path = $file->storeAs($directory, $filename, "public");
                    if ($path) {
                        $model->image()->save(new RecruitmentImage([
                            "file_name" => $filename,
                            "file_origin_name" => $file->getClientOriginalName(),
                            "file_type" => $file->getClientMimeType(),
                            "file_size" => $file->getSize(),
                            "file_path" => $path,
                        ]));
                    }
                }
            });
            return redirect()->route("recruitment.index")->with("success", "บันทึกข้อมูลการรับสมัครงานเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Recruitment  $recruitment
     * @return \Illuminate\Http\Response
     */
    public function show(Recruitment $recruitment)
    {
        return view("recruitments.form",["data" => $recruitment]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Recruitment  $recruitment
     * @return \Illuminate\Http\Response
     */
    public function edit(Recruitment $recruitment)
    {
        return view("recruitments.form",["data" => $recruitment]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Recruitment  $recruitment
     * @return \Illuminate\Http\Response
     */
    public function update(RecruitmentRequest $request, Recruitment $recruitment)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request, $recruitment) {
                $upload = false;
                if ($request->hasFile("files")) {
                    if (!in_array($request->file("files")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $upload = true;
                }
                $recruitment->update($request->merge(["user_update_id" => auth()->id()])->except(["files"]));
                
                if ($upload) {
                    $file = $request->file("files");
                    $extension = $file->getClientOriginalExtension();
                    $directory = "recruitments" . DIRECTORY_SEPARATOR . $recruitment->recruitment_id;
                    $filename = uniqid() . "." . $extension;
                    $path = $file->storeAs($directory, $filename, "public");
                    if ($path) {
                        RecruitmentImage::updateOrCreate(
                            ["recruitment_id" => $recruitment->recruitment_id],
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
            return redirect()->route("recruitment.index")->with("success", "บันทึกข้อมูลการรับสมัครงานเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Recruitment  $recruitment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recruitment $recruitment)
    {
        $recruitment->update(["status" => 0 , "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลข่าวสารกิจกรรมเรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\Recruitment  $recruitment
     * @return \Illuminate\Http\Response
     */
    public function active(Recruitment $recruitment)
    {
        $recruitment->update(["active" => $recruitment->active == 1 ? 0 : 1]);
        return response()->json(["status" => true]);
    }


    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\Recruitment  $recruitment
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, Recruitment $recruitment)
    {
        try {
            DB::transaction(function () use ($request, $recruitment) {
                $recruitment->update([
                    "approved" => $request->action,
                    "send_mail_type" => $request->input("rad"),
                    "note" => $request->input("note"),
                    "status" => $request->action == "2"
                        ? 0
                        : $recruitment->status,
                    "user_approve_id" => auth()->id(),
                    "approved_at" => DB::raw("CURRENT_DATE"),
                ]);
            });
            return redirect()->back()->with("success", "บันทึกข้อมูลการรับสมัครงานเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\Recruitment  $recruitment
     * @return \Illuminate\Http\Response
     */
    public function restore(Recruitment $recruitment)
    {
        $recruitment->update(["status" => 1, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลการรับสมัครงานเรียบร้อย");
    }
}
