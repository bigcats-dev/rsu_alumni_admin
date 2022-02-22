<?php

namespace App\Http\Controllers;

use App\Models\SpiritCoinActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;
use App\Http\Requests\SpiritCoinActivityRequest;
use App\Models\SpiritCoinActivityImage;
use Illuminate\Support\Str;

class SpiritCoinActivityController extends Controller
{
    public function __construct() {
        $this->middleware("can:view-spirit-coin-activity");
        $this->middleware("can:create-spirit-coin-activity")->only(["create","store"]);
        $this->middleware("can:update-spirit-coin-activity")->only("update");
        $this->middleware("can:del-spirit-coin-activity")->only("destroy");
        $this->middleware("can:approve-spirit-coin-activity")->only("approve");
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
            $columnSorts = [DB::raw("seqnum"), "spirit_coin_activities.created_at", "spirit_coin_activities.updated_at"];
            $typeQuery = request()->input("type");
            $whereCondition = function ($query) use ($typeQuery) {
                if ($typeQuery != "3") {
                    $query->where("approved", $typeQuery)->where("status", 1);
                } else {
                    $query->where("status", 0);
                }
            };
            $totalData = SpiritCoinActivity::where($whereCondition)->count();

            $buildQuery = SpiritCoinActivity::selectRaw("row_number() over (order by spirit_coin_activities.spirit_coin_activity_id desc) as seqnum,spirit_coin_activities.*,u_create.fullname as u_create_name,u_update.fullname as u_update_name
                ,spirit_coin_activity_images.file_name,spirit_coin_activity_images.file_origin_name,spirit_coin_activity_images.file_size,spirit_coin_activity_images.file_path")
                ->join("users as u_create", "spirit_coin_activities.user_create_id", "=", "u_create.id")
                ->leftjoin("users as u_update", "spirit_coin_activities.user_update_id", "=", "u_update.id")
                ->leftjoin("spirit_coin_activity_images", "spirit_coin_activities.spirit_coin_activity_id", "=", "spirit_coin_activity_images.spirit_coin_activity_id")
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
                    $i["action"]["view"] = route("spirit-coin-activity.view", ["coin" => $i["spirit_coin_activity_id"]]);
                    $i["action"]["edit"] = route("spirit-coin-activity.edit", ["coin" => $i["spirit_coin_activity_id"]]);
                    $i["action"]["delete"] = route("spirit-coin-activity.destroy", ["coin" => $i["spirit_coin_activity_id"]]);
                    $i["action"]["approve"] = route("spirit-coin-activity.approve", ["coin" => $i["spirit_coin_activity_id"]]);
                    $i["action"]["restore"] = route("spirit-coin-activity.restore", ["coin" => $i["spirit_coin_activity_id"]]);
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
        return view("spirit-coin-activities.index", [
            "badge" => SpiritCoinActivity::where([["approved", "=", 0], ["status", "=", 1]])->count(),
            "records" => SpiritCoinActivity::where([["approved", "=", 1], ["status", "=", 1]])->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("spirit-coin-activities.form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SpiritCoinActivityRequest $request)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request) {
                $upload = false;
                if ($request->hasFile("files")) {
                    if (!in_array($request->file("files")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $upload = true;
                }

                $model = SpiritCoinActivity::create($request->merge(["user_create_id" => auth()->id()])->except(["files"]));
                if ($model) {

                    if ($upload) {
                        $file = $request->file("files");
                        $extension = $file->getClientOriginalExtension();
                        $directory = "spirit-con-activities" . DIRECTORY_SEPARATOR . $model->spirit_coin_activity_id;
                        $filename = uniqid() . "." . $extension;
                        $path = $file->storeAs($directory, $filename, "public");
                        if ($path) {
                            $model->image()->save(new SpiritCoinActivityImage([
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
            return redirect()->route("spirit-coin-activity.index")->with("success", "บันทึกข้อมูลกิจกรรมสปิริตคอยน์เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SpiritCoinActivity  $coin
     * @return \Illuminate\Http\Response
     */
    public function show(SpiritCoinActivity $coin)
    {
        return view("spirit-coin-activities.form", ["data" => $coin]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SpiritCoinActivity  $coin
     * @return \Illuminate\Http\Response
     */
    public function edit(SpiritCoinActivity $coin)
    {
        return view("spirit-coin-activities.form", ["data" => $coin]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SpiritCoinActivity  $coin
     * @return \Illuminate\Http\Response
     */
    public function update(SpiritCoinActivityRequest $request, SpiritCoinActivity $coin)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request, $coin) {
                $upload = false;
                if ($request->hasFile("files")) {
                    if (!in_array($request->file("files")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $upload = true;
                }

                $coin->update($request->merge(["user_update_id" => auth()->id()])->except(["files"]));
                if ($upload) {
                    $file = $request->file("files");
                    $extension = $file->getClientOriginalExtension();
                    $directory = "spirit-con-activities" . DIRECTORY_SEPARATOR . $coin->spirit_coin_activity_id;
                    $filename = uniqid() . "." . $extension;
                    $path = $file->storeAs($directory, $filename, "public");
                    if ($path) {
                        SpiritCoinActivityImage::updateOrCreate(
                            ["spirit_coin_activity_id" => $coin->spirit_coin_activity_id],
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
            return redirect()->route("spirit-coin-activity.index")->with("success", "บันทึกข้อมูลกิจกรรมสปิริตคอยน์เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SpiritCoinActivity  $coin
     * @return \Illuminate\Http\Response
     */
    public function destroy(SpiritCoinActivity $coin)
    {
        $coin->update(["status" => 0 , "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลกิจกรรมสปิริตคอยน์เรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\SpiritCoinActivity  $coin
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, SpiritCoinActivity $coin)
    {
        try {
            DB::transaction(function () use ($request, $coin) {
                $coin->update([
                    "approved" => $request->action,
                    "note" => $request->input("note"),
                    "status" => $request->action == "2"
                        ? 0
                        : $coin->status,
                    "user_approve_id" => auth()->id(),
                    "approved_at" => DB::raw("CURRENT_DATE"),
                ]);
            });
            return redirect()->back()->with("success", "บันทึกข้อมูลกิจกรรมสปิริตคอยน์เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\SpiritCoinActivity  $coin
     * @return \Illuminate\Http\Response
     */
    public function restore(SpiritCoinActivity $coin)
    {
        $coin->update(["status" => 1 , "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลกิจกรรมสปิริตคอยน์เรียบร้อย");
    }
}
