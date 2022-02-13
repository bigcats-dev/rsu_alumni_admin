<?php

namespace App\Http\Controllers;

use App\Models\YearBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;
use App\Http\Requests\YearBookRequest;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\YearBookFile;
use Illuminate\Support\Str;

class YearBookController extends Controller
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
            $columnSorts = [DB::raw("seqnum"), "yearbooks.created_at", "yearbooks.updated_at"];
            $typeQuery = request()->input("type");
            $whereCondition = function ($query) use ($typeQuery) {
                if ($typeQuery != "3") {
                    $query->where("approved", $typeQuery)->where("status", 1);
                } else {
                    $query->where("status", 0);
                }
            };
            $totalData = YearBook::where($whereCondition)->count();

            $buildQuery = YearBook::selectRaw("row_number() over (order by yearbooks.yearbook_id desc) as seqnum,yearbooks.*,u_create.fullname as u_create_name,u_update.fullname as u_update_name")
                ->join("users as u_create", "yearbooks.user_create_id", "=", "u_create.id")
                ->leftjoin("users as u_update", "yearbooks.user_update_id", "=", "u_update.id")
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
                    $i["files"] = YearBookFile::where("yearbook_id",$i["yearbook_id"])
                        ->where("file_type","application/pdf")
                        ->get()
                        ->transform(function($f){
                            return [
                                "name" => $f->file_origin_name,
                                "url" => asset("storage/".$f->file_path),
                                "size" => Helper::filesize_formatted($f->file_size),
                            ];
                        });
                    $i["images"] = YearBookFile::where("yearbook_id",$i["yearbook_id"])
                        ->whereRaw("file_type LIKE 'image%'")
                        ->get()
                        ->transform(function($f){
                            return [
                                "name" => $f->file_origin_name,
                                "url" => asset("storage/".$f->file_path),
                                "size" => Helper::filesize_formatted($f->file_size),
                            ];
                        });

                    $i["created_at"] = Helper::convertToDateTimeYTh($i["created_at"]);
                    $i["updated_at"] = Helper::convertToDateTimeYTh($i["updated_at"]);
                    $i["action"]["view"] = route("year-book.view", ["book" => $i["yearbook_id"]]);
                    $i["action"]["edit"] = route("year-book.edit", ["book" => $i["yearbook_id"]]);
                    $i["action"]["delete"] = route("year-book.destroy", ["book" => $i["yearbook_id"]]);
                    $i["action"]["approve"] = route("year-book.approve", ["book" => $i["yearbook_id"]]);
                    $i["action"]["restore"] = route("year-book.restore", ["book" => $i["yearbook_id"]]);
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
        return view("year-books.index", [
            "badge" => YearBook::where([["approved", "=", 0], ["status", "=", 1]])->count(),
            "records" => YearBook::where([["approved", "=", 1], ["status", "=", 1]])->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("year-books.form", [
            "ms_faculty" => Faculty::all(),
            "ms_major" => Major::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(YearBookRequest $request)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request) {
                $uploadPdf = false;
                $uploadImg = false;
                if ($request->hasFile("files")) {
                    if (!in_array($request->file("files")->getClientOriginalExtension(), ["pdf"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .pdf)");
                    }
                    $uploadPdf = true;
                }
                if ($request->hasFile("images")) {
                    if (!in_array($request->file("images")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $uploadImg = true;
                }

                $model = YearBook::create($request->merge(["user_create_id" => auth()->id()])->except(["files", "images"]));
                if ($model) {
                    $upload = function ($file, $model) {
                        $extension = $file->getClientOriginalExtension();
                        $directory = "yearbooks" . DIRECTORY_SEPARATOR . $model->yearbook_id;
                        $filename = uniqid() . "." . $extension;
                        $path = $file->storeAs($directory, $filename, "public");
                        if ($path) {
                            $model->files()->save(new YearBookFile([
                                "file_name" => $filename,
                                "file_origin_name" => $file->getClientOriginalName(),
                                "file_type" => $file->getClientMimeType(),
                                "file_size" => $file->getSize(),
                                "file_path" => $path,
                            ]));
                        }
                    };
                    if ($uploadPdf) $upload($request->file("files"), $model);
                    if ($uploadImg) $upload($request->file("images"), $model);
                }
            });
            return redirect()->route("year-book.index")->with("success", "บันทึกข้อมูลหนังสือรุ่นเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\YearBook  $book
     * @return \Illuminate\Http\Response
     */
    public function show(YearBook $book)
    {
        return view("year-books.form", [
            "data" => $book,
            "ms_faculty" => Faculty::all(),
            "ms_major" => Major::all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\YearBook  $yearBook
     * @return \Illuminate\Http\Response
     */
    public function edit(YearBook $book)
    {
        return view("year-books.form", [
            "data" => $book,
            "ms_faculty" => Faculty::all(),
            "ms_major" => Major::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\YearBook  $book
     * @return \Illuminate\Http\Response
     */
    public function update(YearBookRequest $request, YearBook $book)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request, $book) {
                $uploadPdf = false;
                $uploadImg = false;
                if ($request->hasFile("files")) {
                    if (!in_array($request->file("files")->getClientOriginalExtension(), ["pdf"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .pdf)");
                    }
                    $uploadPdf = true;
                }
                if ($request->hasFile("images")) {
                    if (!in_array($request->file("images")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $uploadImg = true;
                }

                $book->update($request->merge(["user_update_id" => auth()->id()])->except(["files", "images"]));
                $upload = function ($file, $search) use ($book) {
                    $extension = $file->getClientOriginalExtension();
                    $directory = "yearbooks" . DIRECTORY_SEPARATOR . $book->yearbook_id;
                    $filename = uniqid() . "." . $extension;
                    $path = $file->storeAs($directory, $filename, "public");
                    if ($path) {
                        YearBookFile::updateOrCreate(
                            $search,
                            [
                                "file_name" => $filename,
                                "file_origin_name" => $file->getClientOriginalName(),
                                "file_type" => $file->getClientMimeType(),
                                "file_size" => $file->getSize(),
                                "file_path" => $path,
                            ]
                        );
                    }
                };
                if ($uploadPdf) $upload(
                    $request->file("files"),
                    [
                        'yearbook_id' => $book->yearbook_id,
                        'file_type' => 'application/pdf'
                    ]
                );
                if ($uploadImg) $upload(
                    $request->file("images"),
                    [
                        'yearbook_id' => $book->yearbook_id,
                        'file_type' => ['image/jpeg', 'image/png', 'image/jpg']
                    ]
                );
            });
            return redirect()->route("year-book.index")->with("success", "บันทึกข้อมูลหนังสือรุ่นเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\YearBook  $yearBook
     * @return \Illuminate\Http\Response
     */
    public function destroy(YearBook $book)
    {
        $book->update(["status" => 0, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลหนังสือรุ่นเรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\YearBook  $book
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, YearBook $book)
    {
        try {
            DB::transaction(function () use ($request, $book) {
                $book->update([
                    "approved" => $request->action,
                    "note" => $request->input("note"),
                    "status" => $request->action == "2"
                        ? 0
                        : $book->status,
                    "user_approve_id" => auth()->id(),
                    "approved_at" => DB::raw("CURRENT_DATE"),
                ]);
            });
            return redirect()->back()->with("success", "บันทึกข้อมูลหนังสือรุ่นเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\YearBook  $book
     * @return \Illuminate\Http\Response
     */
    public function restore(YearBook $book)
    {
        $book->update(["status" => 1, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลหนังสือรุ่นเรียบร้อย");
    }
}
