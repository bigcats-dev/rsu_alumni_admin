<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlbumRequest;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;
use App\Models\Gallery;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AlbumController extends Controller
{
    public function __construct() {
        $this->middleware("can:view-album");
        $this->middleware("can:create-album")->only(["create","store"]);
        $this->middleware("can:update-album")->only("update");
        $this->middleware("can:del-album")->only("destroy");
        $this->middleware("can:approve-album")->only("approve");
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
            $columnSorts = [DB::raw("seqnum"), "albums.created_at", "albums.updated_at"];
            $typeQuery = request()->input("type");
            $whereCondition = function ($query) use ($typeQuery) {
                if ($typeQuery != "3") {
                    $query->where("approved", $typeQuery)->where("status", 1);
                } else {
                    $query->where("status", 0);
                }
            };
            $totalData = Album::where($whereCondition)->count();

            $buildQuery = Album::selectRaw("row_number() over (order by albums.album_id desc) as seqnum,albums.*,u_create.fullname as u_create_name,u_update.fullname as u_update_name
                ,galleries.gallery_id,galleries.file_name,galleries.file_origin_name,galleries.file_size,galleries.file_path")
                ->join("users as u_create", "albums.user_create_id", "=", "u_create.id")
                ->leftjoin("users as u_update", "albums.user_update_id", "=", "u_update.id")
                ->leftjoin("galleries", function ($join) {
                    $join->on("albums.album_id", "=", "galleries.album_id")->where("galleries.cover_page", "Y");
                })
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
                    if (is_null($i["gallery_id"])) {
                        $gallery = Gallery::where("album_id", $i["album_id"])
                            ->orderBy("gallery_id")
                            ->limit(1)
                            ->first();
                        if ($gallery) {
                            $i["file"] = [
                                "name" => $gallery->file_origin_name,
                                "path" => asset("storage/" . $gallery->file_path),
                                "size" => $gallery->file_size,
                            ];
                        }
                    } else {
                        $i["file"] = [
                            "name" => $i["file_origin_name"],
                            "path" => asset("storage/" . $i["file_path"]),
                            "size" => $i["file_size"],
                        ];
                    }

                    $i["created_at"] = Helper::convertToDateTimeYTh($i["created_at"]);
                    $i["updated_at"] = Helper::convertToDateTimeYTh($i["updated_at"]);
                    $i["action"]["view"] = route("album.view", ["album" => $i["album_id"]]);
                    $i["action"]["edit"] = route("album.edit", ["album" => $i["album_id"]]);
                    $i["action"]["delete"] = route("album.destroy", ["album" => $i["album_id"]]);
                    $i["action"]["approve"] = route("album.approve", ["album" => $i["album_id"]]);
                    $i["action"]["active"] = route("album.active", ["album" => $i["album_id"]]);
                    $i["action"]["restore"] = route("album.restore", ["album" => $i["album_id"]]);
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
        return view("albums.index", [
            "badge" => Album::where([["approved", "=", 0], ["status", "=", 1]])->count(),
            "records" => Album::where([["approved", "=", 1], ["status", "=", 1]])->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("albums.form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AlbumRequest $request)
    {
        $request->validated();
        try {
            $album = DB::transaction(function () use ($request) {
                return Album::create($request->merge(["user_create_id" => auth()->id()])->all());
            });
            return redirect()->route("album.view", ["album" => $album->album_id])->with("success", "บันทึกข้อมูลอัลบั้มแกลเลอรีเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function show(Album $album)
    {
        return view("albums.form", ["data" => $album]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function edit(Album $album)
    {
        return view("albums.form", ["data" => $album]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function update(AlbumRequest $request, Album $album)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request, $album) {
                $album->update($request->merge(["user_update_id" => auth()->id()])->all());
            });
            return redirect()->route("album.index",)->with("success", "บันทึกข้อมูลอัลบั้มแกลเลอรีเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function destroy(Album $album)
    {
        $album->update(["status" => 0, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลอัลบั้มแกลเลอรีเรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\Album  $coin
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, Album $album)
    {
        try {
            DB::transaction(function () use ($request, $album) {
                $album->update([
                    "approved" => $request->action,
                    "note" => $request->input("note"),
                    "status" => $request->action == "2"
                        ? 0
                        : $album->status,
                    "user_approve_id" => auth()->id(),
                    "approved_at" => DB::raw("CURRENT_DATE"),
                ]);
            });
            return redirect()->back()->with("success", "บันทึกข้อมูลอัลบั้มแกลเลอรีเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function restore(Album $album)
    {
        $album->update(["status" => 1, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลอัลบั้มแกลเลอรีเรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\Album  $coin
     * @return \Illuminate\Http\Response
     */
    public function active(Album $album)
    {
        $album->update(["active" => $album->active == 1 ? 0 : 1]);
        return response()->json(["status" => true]);
    }

    /**
     * upload the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function galleryUpload(Request $request, Album $album)
    {
        $request->validate(["file" => "required|file"]);

        try {
            $file = DB::transaction(function () use ($request, $album) {
                if (!in_array($request->file("file")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                    throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                }
                $file = $request->file("file");
                $extension = $file->getClientOriginalExtension();
                $directory = "albums" . DIRECTORY_SEPARATOR . $album->album_id;
                $filename = uniqid() . "." . $extension;
                $path = $file->storeAs($directory, $filename, "public");
                if ($path) {
                    return Gallery::create([
                        "file_name" => $filename,
                        "file_origin_name" => $file->getClientOriginalName(),
                        "file_type" => $file->getClientMimeType(),
                        "file_size" => $file->getSize(),
                        "file_path" => $path,
                        "album_id" => $album->album_id,
                    ]);
                }

                return null;
            });

            return response()->json(["status" => true, "file" => $file]);
        } catch (Exception $e) {
            return response()->json(["status" => false, "msg" => $e->getMessage()]);
        }
    }

    /**
     * remove the specified resource from storage.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function galleryDestroy(Gallery $gallery)
    {
        try {
            Storage::delete($gallery->file_path);
            $gallery->delete();
            return response()->json(["status" => true]);
        } catch (Exception $e) {
            return response()->json(["status" => false, "msg" => $e->getMessage()]);
        }
    }

    /**
     * update the specified resource from storage.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function galleryCoverPage(Gallery $gallery)
    {
        try {
            DB::transaction(function () use ($gallery) {
                Gallery::where("album_id", $gallery->album->album_id)->update(["cover_page" => null]);
                $gallery->update(['cover_page' => 'Y']);
            });
            return response()->json(["status" => true]);
        } catch (Exception $e) {
            return response()->json(["status" => false, "msg" => $e->getMessage()]);
        }
    }
}
