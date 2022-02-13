<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\TrainingNewRequest;
use App\Models\TrainingNew;
use App\Models\TrainingNewImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PressReleaseController extends Controller
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
            $columnSorts = [DB::raw("seqnum"), "training_news.created_at", "training_news.updated_at"];
            $typeQuery = request()->input("type");
            $whereCondition = function ($query) use ($typeQuery) {
                if ($typeQuery !== "3") {
                    $query->where("approved", $typeQuery)->where("status", 1);
                } else {
                    $query->where("status", 0);
                }
            };
            $totalData = TrainingNew::where($whereCondition)->count();

            $buildQuery = TrainingNew::selectRaw("row_number() over (order by training_news.training_news_id desc) as seqnum,training_news.*,u_create.fullname as u_create_name,u_update.fullname as u_update_name
                ,training_new_images.file_name,training_new_images.file_origin_name,training_new_images.file_size,training_new_images.file_path")
                ->join("users as u_create", "training_news.user_create_id", "=", "u_create.id")
                ->leftjoin("users as u_update", "training_news.user_update_id", "=", "u_update.id")
                ->leftjoin("training_new_images", "training_news.training_news_id", "=", "training_new_images.training_news_id")
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
                    $i["action"]["view"] = route("press-release.view", ["training_new" => $i["training_news_id"]]);
                    $i["action"]["edit"] = route("press-release.edit", ["training_new" => $i["training_news_id"]]);
                    $i["action"]["delete"] = route("press-release.destroy", ["training_new" => $i["training_news_id"]]);
                    $i["action"]["showhomepage"] = route("press-release.show_homepage", ["training_new" => $i["training_news_id"]]);
                    $i["action"]["priority"] = route("press-release.priority", ["training_new" => $i["training_news_id"]]);
                    $i["action"]["restore"] = route("press-release.restore", ["training_new" => $i["training_news_id"]]);
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
        return view("press-releases.index", [
            "badge" => TrainingNew::where([["approved", "=", 0], ["status", "=", 1]])->count(),
            "records" => TrainingNew::where([["approved", "=", 1], ["status", "=", 1]])->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("press-releases.form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TrainingNewRequest $request)
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

                $model = new TrainingNew();
                $model->title = $request->title;
                $model->introduction = $request->introduction;
                $model->detail = $request->detail;
                $model->start_date = $request->start_date;
                $model->end_date = $request->end_date;
                $model->user_create_id = auth()->id();
                $model->save();
                if ($upload) {
                    $file = $request->file("files");
                    $extension = $file->getClientOriginalExtension();
                    $directory = "training_news" . DIRECTORY_SEPARATOR . $model->training_news_id;
                    $filename = uniqid() . "." . $extension;
                    $path = $file->storeAs($directory, $filename, "public");
                    if ($path) {
                        $model->image()->save(new TrainingNewImage([
                            "file_name" => $filename,
                            "file_origin_name" => $file->getClientOriginalName(),
                            "file_type" => $file->getClientMimeType(),
                            "file_size" => $file->getSize(),
                            "file_path" => $path,
                        ]));
                    }
                }
            });
            return redirect()->route("press-release.index")->with("success", "บันทึกข้อมูลข่าวสารประชาสัมพันธ์เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TrainingNew  $trainingNew
     * @return \Illuminate\Http\Response
     */
    public function show(TrainingNew $training_new)
    {
        return view("press-releases.form", ["data" => $training_new]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TrainingNew  $trainingNew
     * @return \Illuminate\Http\Response
     */
    public function edit(TrainingNew $training_new)
    {
        return view("press-releases.form", ["data" => $training_new]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TrainingNew  $trainingNew
     * @return \Illuminate\Http\Response
     */
    public function update(TrainingNewRequest $request, TrainingNew $training_new)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request, $training_new) {
                $upload = false;
                if ($request->hasFile("files")) {
                    if (!in_array($request->file("files")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $upload = true;
                }
                $request->merge(["user_update_id" => auth()->id()]);
                $training_new->update($request->except(["files"]));
                if ($upload) {
                    $file = $request->file("files");
                    $extension = $file->getClientOriginalExtension();
                    $directory = "training_news" . DIRECTORY_SEPARATOR . $training_new->training_news_id;
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
                        if ($training_new->image) {
                            $training_new->image()->update($attrs);
                        } else {
                            $training_new->image()->save(new TrainingNewImage($attrs));
                        }
                    }
                }
            });
            return redirect()->route("press-release.index")->with("success", "บันทึกข้อมูลข่าวสารประชาสัมพันธ์เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TrainingNew  $trainingNew
     * @return \Illuminate\Http\Response
     */
    public function destroy(TrainingNew $training_new)
    {
        $training_new->update(["status" => 0, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลข่าวสารประชาสัมพันธ์เรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\TrainingNew  $trainingNew
     * @return \Illuminate\Http\Response
     */
    public function showHomePage(TrainingNew $training_new)
    {
        $training_new->update(["home_page" => $training_new->home_page == 1 ? 0 : 1]);
        return response()->json(["status" => true]);
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\TrainingNew  $trainingNew
     * @return \Illuminate\Http\Response
     */
    public function priority(Request $request, TrainingNew $training_new)
    {
        $training_new->update(["priority" => $request->priority]);
        return response()->json(["status" => true]);
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\TrainingNew  $trainingNew
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, TrainingNew $training_new)
    {
        try {
            DB::transaction(function () use ($request, $training_new) {
                $training_new->update([
                    "approved" => $request->action,
                    "send_mail_type" => $request->input("rad"),
                    "note" => $request->input("note"),
                    "status" => $request->action == "2"
                        ? 0
                        : $training_new->status,
                    "user_approve_id" => auth()->id(),
                    "approved_at" => DB::raw("CURRENT_DATE"),
                ]);
            });
            return redirect()->back()->with("success", "บันทึกข้อมูลข่าวสารประชาสัมพันธ์เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\TrainingNew  $trainingNew
     * @return \Illuminate\Http\Response
     */
    public function restore(TrainingNew $training_new)
    {
        $training_new->update(["status" => 1, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลข่าวสารประชาสัมพันธ์เรียบร้อย");
    }
}
