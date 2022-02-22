<?php

namespace App\Http\Controllers;

use App\Http\Requests\CareerNewsRequest;
use App\Models\CareerNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;
use App\Models\CareerNewsFile;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class CareerNewsController extends Controller
{
    public function __construct() {
        $this->middleware("can:view-career-news");
        $this->middleware("can:create-career-news")->only(["create","store"]);
        $this->middleware("can:update-career-news")->only("update");
        $this->middleware("can:del-career-news")->only("destroy");
        $this->middleware("can:approve-career-news")->only("approve");
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
            $columnSorts = [DB::raw("seqnum"), "career_news.created_at", "career_news.updated_at"];
            $typeQuery = request()->input("type");
            $whereCondition = function ($query) use ($typeQuery) {
                if ($typeQuery != "3") {
                    $query->where("approved", $typeQuery)->where("status", 1);
                } else {
                    $query->where("status", 0);
                }
            };
            $totalData = CareerNews::where($whereCondition)->count();

            $buildQuery = CareerNews::selectRaw("row_number() over (order by career_news.career_news_id desc) as seqnum,career_news.*,u_create.fullname as u_create_name,u_update.fullname as u_update_name
                    ,career_news_files.file_name,career_news_files.file_origin_name,career_news_files.file_size,career_news_files.file_path")
                ->join("users as u_create", "career_news.user_create_id", "=", "u_create.id")
                ->leftjoin("users as u_update", "career_news.user_update_id", "=", "u_update.id")
                ->leftjoin("career_news_files", "career_news.career_news_id", "=", "career_news_files.career_news_id")
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
                    $i["start_date"] = Helper::convertToDateTh($i["start_date"]);
                    $i["end_date"] = Helper::convertToDateTh($i["end_date"]);
                    $i["action"]["view"] = route("career-news.view", ["news" => $i["career_news_id"]]);
                    $i["action"]["edit"] = route("career-news.edit", ["news" => $i["career_news_id"]]);
                    $i["action"]["delete"] = route("career-news.destroy", ["news" => $i["career_news_id"]]);
                    $i["action"]["approve"] = route("career-news.approve", ["news" => $i["career_news_id"]]);
                    $i["action"]["restore"] = route("career-news.restore", ["news" => $i["career_news_id"]]);
                    $i["action"]["priority"] = route("career-news.priority", ["news" => $i["career_news_id"]]);
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
        return view("career-news.index", [
            "badge" => CareerNews::where([["approved", "=", 0], ["status", "=", 1]])->count(),
            "records" => CareerNews::where([["approved", "=", 1], ["status", "=", 1]])->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("career-news.form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CareerNewsRequest $request)
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

                $model = CareerNews::create($request->merge(["user_create_id" => auth()->id()])->except(["files"]));
                if ($model) {
                    if ($upload) {
                        $file = $request->file("files");
                        $extension = $file->getClientOriginalExtension();
                        $directory = "career-news" . DIRECTORY_SEPARATOR . $model->career_news_id;
                        $filename = uniqid() . "." . $extension;
                        $path = $file->storeAs($directory, $filename, "public");
                        if ($path) {
                            $model->files()->save(new CareerNewsFile([
                                "file_name" => $filename,
                                "file_origin_name" => $file->getClientOriginalName(),
                                "file_type" => $file->getClientMimeType(),
                                "file_size" => $file->getSize(),
                                "file_path" => $path,
                            ]));

                            // create thumbnails
                            $imageResize = Image::make(Storage::path($path))
                                ->resize(340, 180, function ($constraint) {
                                    $constraint->aspectRatio();
                                })
                                ->encode($extension);
                            Storage::put($directory . "/thumbnails/" . $filename, $imageResize);
                        }
                    }
                }
            });
            return redirect()->route("career-news.index")->with("success", "บันทึกข้อมูลข่าวอบรมหลักสูตรอาชีพเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CareerNews  $news
     * @return \Illuminate\Http\Response
     */
    public function show(CareerNews $news)
    {
        return view("career-news.form", ["data" => $news]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CareerNews  $news
     * @return \Illuminate\Http\Response
     */
    public function edit(CareerNews $news)
    {
        return view("career-news.form", ["data" => $news]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CareerNews  $news
     * @return \Illuminate\Http\Response
     */
    public function update(CareerNewsRequest $request, CareerNews $news)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request, $news) {
                $upload = false;
                if ($request->hasFile("files")) {
                    if (!in_array($request->file("files")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $upload = true;
                }

                $news->update($request->merge(["user_update_id" => auth()->id()])->except(["files"]));
                if ($upload) {
                    $file = $request->file("files");
                    $extension = $file->getClientOriginalExtension();
                    $directory = "career-news" . DIRECTORY_SEPARATOR . $news->career_news_id;
                    $filename = uniqid() . "." . $extension;
                    $path = $file->storeAs($directory, $filename, "public");
                    if ($path) {
                        CareerNewsFile::updateOrCreate(
                            ['career_news_id' => $news->career_news_id],
                            [
                                "file_name" => $filename,
                                "file_origin_name" => $file->getClientOriginalName(),
                                "file_type" => $file->getClientMimeType(),
                                "file_size" => $file->getSize(),
                                "file_path" => $path,
                            ]
                        );

                        // create thumbnails
                        $imageResize = Image::make(Storage::path($path))
                            ->resize(340, 180, function ($constraint) {
                                $constraint->aspectRatio();
                            })
                            ->encode($extension);
                        Storage::put($directory . "/thumbnails/" . $filename, $imageResize);
                    }
                }
            });
            return redirect()->route("career-news.index")->with("success", "บันทึกข้อมูลข่าวอบรมหลักสูตรอาชีพเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CareerNews  $news
     * @return \Illuminate\Http\Response
     */
    public function destroy(CareerNews $news)
    {
        $news->update(["status" => 0, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลข่าวอบรมหลักสูตรอาชีพเรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\CareerNews  $news
     * @return \Illuminate\Http\Response
     */
    public function priority(Request $request, CareerNews $news)
    {
        $news->update(["priority" => $request->priority]);
        return response()->json(["status" => true]);
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\CareerNews  $news
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, CareerNews $news)
    {
        try {
            DB::transaction(function () use ($request, $news) {
                $news->update([
                    "approved" => $request->action,
                    "send_mail_type" => $request->input("rad"),
                    "note" => $request->input("note"),
                    "status" => $request->action == "2"
                        ? 0
                        : $news->status,
                    "user_approve_id" => auth()->id(),
                    "approved_at" => DB::raw("CURRENT_DATE"),
                ]);
            });
            return redirect()->back()->with("success", "บันทึกข้อมูลข่าวอบรมหลักสูตรอาชีพเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\CareerNews  $book
     * @return \Illuminate\Http\Response
     */
    public function restore(CareerNews $news)
    {
        $news->update(["status" => 1, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลข่าวอบรมหลักสูตรอาชีพเรียบร้อย");
    }
}
