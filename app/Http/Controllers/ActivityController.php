<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;
use App\Http\Requests\ActivityRequest;
use App\Models\ActivityImage;
use App\Models\ActivityRoom;
use App\Models\ActivityRoomSchedule;
use App\Models\ActivitySchedule;
use App\Models\Room;
use App\Models\RoomGroup;
use App\Models\RoomSubGroup;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    public function __construct() {
        $this->middleware(["can:view-event-news"]);
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
            $columnSorts = [DB::raw("seqnum"), "activities.created_at", "activities.updated_at"];
            $typeQuery = request()->input("type");
            $whereCondition = function ($query) use ($typeQuery) {
                if ($typeQuery != "3") {
                    $query->where("approved", $typeQuery)->where("status", 1);
                } else {
                    $query->where("status", 0);
                }
            };
            $totalData = Activity::where($whereCondition)->count();

            $buildQuery = Activity::selectRaw("row_number() over (order by activities.activity_id desc) as seqnum,activities.*,u_create.fullname as u_create_name,u_update.fullname as u_update_name
                ,activity_images.file_name,activity_images.file_origin_name,activity_images.file_size,activity_images.file_path")
                ->join("users as u_create", "activities.user_create_id", "=", "u_create.id")
                ->leftjoin("users as u_update", "activities.user_update_id", "=", "u_update.id")
                ->leftjoin("activity_images", "activities.activity_id", "=", "activity_images.activity_id")
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
                    $i["action"]["view"] = route("event-news.view", ["activity" => $i["activity_id"]]);
                    $i["action"]["edit"] = route("event-news.edit", ["activity" => $i["activity_id"]]);
                    $i["action"]["delete"] = route("event-news.destroy", ["activity" => $i["activity_id"]]);
                    $i["action"]["showhomepage"] = route("event-news.show_homepage", ["activity" => $i["activity_id"]]);
                    $i["action"]["priority"] = route("event-news.priority", ["activity" => $i["activity_id"]]);
                    $i["action"]["restore"] = route("event-news.restore", ["activity" => $i["activity_id"]]);
                    $i["action"]["book"] = route("event-news.book-room", ["activity" => $i["activity_id"]]);
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
        return view("activitys.index", [
            "badge" => Activity::where([["approved", "=", 0], ["status", "=", 1]])->count(),
            "records" => Activity::where([["approved", "=", 1], ["status", "=", 1]])->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("activitys.form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ActivityRequest $request)
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

                $model = Activity::create($request->merge(["user_create_id" => auth()->id()])->except(["schedule", "files"]));
                if ($model) {
                    foreach ($request->input("schedule") as $schedule) {
                        $pcSchedule = $model->activity_schedules()->create([
                            "schedule_date" => $schedule["date"]
                        ]);
                        if (is_array($schedule["time"])) {
                            foreach ($schedule["time"] as $time) {
                                $pcSchedule->activity_schedule_details()->create($time);
                            }
                        }
                    }

                    if ($upload) {
                        $file = $request->file("files");
                        $extension = $file->getClientOriginalExtension();
                        $directory = "activities" . DIRECTORY_SEPARATOR . $model->activity_id;
                        $filename = uniqid() . "." . $extension;
                        $path = $file->storeAs($directory, $filename, "public");
                        if ($path) {
                            $model->image()->save(new ActivityImage([
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
            return redirect()->route("event-news.index")->with("success", "บันทึกข้อมูลข่าวสารกิจกรรมเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
        return view("activitys.form", ["data" => $activity]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $activity)
    {
        return view("activitys.form", ["data" => $activity]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(ActivityRequest $request, Activity $activity)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request, $activity) {
                $upload = false;
                if ($request->hasFile("files")) {
                    if (!in_array($request->file("files")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $upload = true;
                }
                $activity->update($request->merge(["user_update_id" => auth()->id()])->except(["schedule", "files"]));
                if (sizeof($activity->activity_schedules) > 0) $activity->activity_schedules()->delete();
                foreach ($request->input("schedule") as $schedule) {
                    $pcSchedule = $activity->activity_schedules()->create([
                        "schedule_date" => $schedule["date"]
                    ]);
                    if (is_array($schedule["time"])) {
                        foreach ($schedule["time"] as $time) {
                            $pcSchedule->activity_schedule_details()->create($time);
                        }
                    }
                }
                if ($upload) {
                    $file = $request->file("files");
                    $extension = $file->getClientOriginalExtension();
                    $directory = "activities" . DIRECTORY_SEPARATOR . $activity->activity_id;
                    $filename = uniqid() . "." . $extension;
                    $path = $file->storeAs($directory, $filename, "public");
                    if ($path) {
                        $attrs = [
                            "file_name" => $filename,
                            "file_origin_name" => $file->getClientOriginalName(),
                            "file_type" => $file->getClientMimeType(),
                            "file_size" => $file->getSize(),
                            "file_path" => $path,
                        ];
                        if ($activity->image) {
                            $activity->image()->update($attrs);
                        } else {
                            $activity->image()->save(new ActivityImage($attrs));
                        }
                    }
                }
            });
            return redirect()->route("event-news.index")->with("success", "บันทึกข้อมูลข่าวสารกิจกรรมเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        $activity->update(["status" => 0, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลข่าวสารกิจกรรมเรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\Activity  $Activity
     * @return \Illuminate\Http\Response
     */
    public function showHomePage(Activity $activity)
    {
        $activity->update(["home_page" => $activity->home_page == 1 ? 0 : 1]);
        return response()->json(["status" => true]);
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\Activity  $Activity
     * @return \Illuminate\Http\Response
     */
    public function priority(Request $request, Activity $activity)
    {
        $activity->update(["priority" => $request->priority]);
        return response()->json(["status" => true]);
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\Activity  $Activity
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, Activity $activity)
    {
        try {
            DB::transaction(function () use ($request, $activity) {
                $activity->update([
                    "approved" => $request->action,
                    "send_mail_type" => $request->input("rad"),
                    "note" => $request->input("note"),
                    "status" => $request->action == "2"
                        ? 0
                        : $activity->status,
                    "user_approve_id" => auth()->id(),
                    "approved_at" => DB::raw("CURRENT_DATE"),
                ]);
            });
            return redirect()->back()->with("success", "บันทึกข้อมูลข่าวสารกิจกรรมเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\Activity  $Activity
     * @return \Illuminate\Http\Response
     */
    public function restore(Activity $activity)
    {
        $activity->update(["status" => 1, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลข่าวสารกิจกรรมเรียบร้อย");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function book(Activity $activity)
    {
        $params = [];
        $params["ms_room_group"] = RoomGroup::selectRaw("convert(ROOM_GROUP_UID,'utf8','us7ascii') AS ROOM_GROUP_ID,ROOM_GROUP_NAME_TH")->get();
        if ($activity->activity_room) {
            if ($activity->activity_room->type == "1") {
                $params["ms_room_subgroup"] = RoomSubGroup::selectRaw("convert(ROOM_SUB_GROUP_UID,'utf8','us7ascii') AS ROOM_SUB_GROUP_ID,ROOM_SUB_GROUP_NAME_TH")->get();
                $params["ms_room"] = Room::selectRaw("convert(ROOM_UID,'utf8','us7ascii') AS ROOM_ID,ROOM_NO,ROOM_NAME_TH,TOTAL_SEAT,EXAM_SEAT,AREA")->get();
            }
        }
        $params["activity"] = $activity;
        return view("activitys.book-room",$params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function booking(Request $request, Activity $activity)
    {
        try {
            DB::transaction(function() use ($request,$activity){
                ActivityRoom::updateOrCreate(["activity_id" => $activity->activity_id],$request->except(["room"]));
                if ($request->input("type") == "1") {
                    if (sizeof($request->input("room")) > 0) {
                        foreach ($request->input("room") as $room) {
                            if (isset($room["del"])) {
                                $activity->activity_room_schedule()->where("activity_room_schedules_id",$room["del"])->delete();
                            } else{
                                if (isset($room["id"])) {
                                    ActivityRoomSchedule::find($room["id"])->update($room);
                                } else {
                                    $activity->activity_room_schedule()->create($room);
                                }
                            }
                        }
                    }
                }
            });
            return redirect()->route("event-news.index")->with("success", "บันทึกข้อมูลสถานที่จัดกิจกรรมเรียบร้อย");
        }catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]); 
        }
    }
}
