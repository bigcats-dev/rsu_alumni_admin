<?php

namespace App\Http\Controllers;

use App\Models\AlumniAffairs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;
use App\Http\Requests\AffairRequest;
use App\Models\AlumniAffairsImage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class AlumniAffairsController extends Controller
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
            $columnSorts = [DB::raw("seqnum"), "alumni_affairs.created_at", "alumni_affairs.updated_at"];
            $typeQuery = request()->input("type");
            $whereCondition = function ($query) use ($typeQuery) {
                if ($typeQuery != "3") {
                    $query->where("approved", $typeQuery)->where("status", 1);
                } else {
                    $query->where("status", 0);
                }
            };
            $totalData = AlumniAffairs::where($whereCondition)->count();

            $buildQuery = AlumniAffairs::selectRaw("row_number() over (order by alumni_affairs.alumni_affairs_id desc) as seqnum,alumni_affairs.*,u_create.fullname as u_create_name,u_update.fullname as u_update_name
                ,alumni_affairs_images.file_name,alumni_affairs_images.file_origin_name,alumni_affairs_images.file_size,alumni_affairs_images.file_path")
                ->join("users as u_create", "alumni_affairs.user_create_id", "=", "u_create.id")
                ->leftjoin("users as u_update", "alumni_affairs.user_update_id", "=", "u_update.id")
                ->leftjoin("alumni_affairs_images", "alumni_affairs.alumni_affairs_id", "=", "alumni_affairs_images.alumni_affairs_id")
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
                    $i["action"]["view"] = route("alumni-affairs.view", ["affairs" => $i["alumni_affairs_id"]]);
                    $i["action"]["edit"] = route("alumni-affairs.edit", ["affairs" => $i["alumni_affairs_id"]]);
                    $i["action"]["approve"] = route("alumni-affairs.approve", ["affairs" => $i["alumni_affairs_id"]]);
                    $i["action"]["delete"] = route("alumni-affairs.destroy", ["affairs" => $i["alumni_affairs_id"]]);
                    $i["action"]["restore"] = route("alumni-affairs.restore", ["affairs" => $i["alumni_affairs_id"]]);
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
        return view("affairs.index", [
            "badge" => AlumniAffairs::where([["approved", "=", 0], ["status", "=", 1]])->count(),
            "records" => AlumniAffairs::where([["approved", "=", 1], ["status", "=", 1]])->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("affairs.form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AffairRequest $request)
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
                $model = AlumniAffairs::create($request->merge(["user_create_id" => auth()->id()])->except(["files"]));
                if ($upload) {
                    $file = $request->file("files");
                    $extension = $file->getClientOriginalExtension();
                    $directory = "alumni-affairs" . DIRECTORY_SEPARATOR . $model->alumni_affairs_id;
                    $filename = uniqid() . "." . $extension;
                    $path = $file->storeAs($directory, $filename, "public");
                    if ($path) {
                        $model->image()->save(new AlumniAffairsImage([
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
            });
            return redirect()->route("alumni-affairs.index")->with("success", "บันทึกข้อมูลกิจการศิษย์เก่าเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AlumniAffairs  $affair
     * @return \Illuminate\Http\Response
     */
    public function show(AlumniAffairs $affairs)
    {
        return view("affairs.form", ["data" => $affairs]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AlumniAffairs  $affair
     * @return \Illuminate\Http\Response
     */
    public function edit(AlumniAffairs $affairs)
    {
        return view("affairs.form", ["data" => $affairs]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AlumniAffairs  $affair
     * @return \Illuminate\Http\Response
     */
    public function update(AffairRequest $request, AlumniAffairs $affairs)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request, $affairs) {
                $upload = false;
                if ($request->hasFile("files")) {
                    if (!in_array($request->file("files")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $upload = true;
                }
                $affairs->update($request->merge(["user_update_id" => auth()->id()])->except(["files"]));
                if ($upload) {
                    $file = $request->file("files");
                    $extension = $file->getClientOriginalExtension();
                    $directory = "alumni-affairs" . DIRECTORY_SEPARATOR . $affairs->alumni_affairs_id;
                    $filename = uniqid() . "." . $extension;
                    $path = $file->storeAs($directory, $filename, "public");
                    if ($path) {
                        AlumniAffairsImage::updateOrCreate(
                            ["alumni_affairs_id" => $affairs->alumni_affairs_id],
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
            return redirect()->route("alumni-affairs.index")->with("success", "บันทึกข้อมูลกิจการศิษย์เก่าเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AlumniAffairs  $affair
     * @return \Illuminate\Http\Response
     */
    public function destroy(AlumniAffairs $affairs)
    {
        $affairs->update(["status" => 0, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลกิจการศิษย์เก่าเรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\AlumniAffairs  $affairs
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, AlumniAffairs $affairs)
    {
        try {
            DB::transaction(function () use ($request, $affairs) {
                $affairs->update([
                    "approved" => $request->action,
                    "note" => $request->input("note"),
                    "status" => $request->action == "2"
                        ? 0
                        : $affairs->status,
                    "user_approve_id" => auth()->id(),
                    "approved_at" => DB::raw("CURRENT_DATE"),
                ]);
            });
            return redirect()->back()->with("success", "บันทึกข้อมูลกิจการศิษย์เก่าเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\AlumniAffairs  $affairs
     * @return \Illuminate\Http\Response
     */
    public function restore(AlumniAffairs $affairs)
    {
        $affairs->update(["status" => 1, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลกิจการศิษย์เก่าเรียบร้อย");
    }
}
