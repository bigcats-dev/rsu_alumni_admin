<?php

namespace App\Http\Controllers;

use App\Models\SpiritCoin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;
use App\Http\Requests\SpiritCoinRequest;
use App\Models\SpiritCoinImage;
use Illuminate\Support\Str;

class SpiritCoinController extends Controller
{
    public function __construct() {
        $this->middleware("can:view-spirit-coin");
        $this->middleware("can:create-spirit-coin")->only(["create","store"]);
        $this->middleware("can:update-spirit-coin")->only("update");
        $this->middleware("can:del-spirit-coin")->only("destroy");
        $this->middleware("can:approve-spirit-coin")->only("approve");
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
            $columnSorts = [DB::raw("seqnum"), "spirit_coin.created_at", "spirit_coin.updated_at"];
            $typeQuery = request()->input("type");
            $whereCondition = function ($query) use ($typeQuery) {
                if ($typeQuery != "3") {
                    $query->where("approved", $typeQuery)->where("status", 1);
                } else {
                    $query->where("status", 0);
                }
            };
            $totalData = SpiritCoin::where($whereCondition)->count();

            $buildQuery = SpiritCoin::selectRaw("row_number() over (order by spirit_coin.spirit_coin_id desc) as seqnum,spirit_coin.*,u_create.fullname as u_create_name,u_update.fullname as u_update_name
                ,spirit_coin_images.file_name,spirit_coin_images.file_origin_name,spirit_coin_images.file_size,spirit_coin_images.file_path")
                ->join("users as u_create", "spirit_coin.user_create_id", "=", "u_create.id")
                ->leftjoin("users as u_update", "spirit_coin.user_update_id", "=", "u_update.id")
                ->leftjoin("spirit_coin_images","spirit_coin.spirit_coin_id","=","spirit_coin_images.spirit_coin_id")
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
                    $i["action"]["view"] = route("spirit-coin.view", ["coin" => $i["spirit_coin_id"]]);
                    $i["action"]["edit"] = route("spirit-coin.edit", ["coin" => $i["spirit_coin_id"]]);
                    $i["action"]["delete"] = route("spirit-coin.destroy", ["coin" => $i["spirit_coin_id"]]);
                    $i["action"]["approve"] = route("spirit-coin.approve", ["coin" => $i["spirit_coin_id"]]);
                    $i["action"]["active"] = route("spirit-coin.active", ["coin" => $i["spirit_coin_id"]]);
                    $i["action"]["priority"] = route("spirit-coin.priority", ["coin" => $i["spirit_coin_id"]]);
                    $i["action"]["restore"] = route("spirit-coin.restore", ["coin" => $i["spirit_coin_id"]]);
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
        return view("spirit-coins.index", [
            "badge" => SpiritCoin::where([["approved", "=", 0], ["status", "=", 1]])->count(),
            "records" => SpiritCoin::where([["approved", "=", 1], ["status", "=", 1]])->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("spirit-coins.form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SpiritCoinRequest $request)
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

                $model = SpiritCoin::create($request->merge(["user_create_id" => auth()->id()])->except(["files"]));
                if ($model) {
                   
                    if ($upload) {
                        $file = $request->file("files");
                        $extension = $file->getClientOriginalExtension();
                        $directory = "spirit-cons" . DIRECTORY_SEPARATOR . $model->spirit_coin_id;
                        $filename = uniqid() . "." . $extension;
                        $path = $file->storeAs($directory, $filename, "public");
                        if ($path) {
                            $model->image()->save(new SpiritCoinImage([
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
            return redirect()->route("spirit-coin.index")->with("success", "บันทึกข้อมูลร้านค้าสปิริตคอยน์เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SpiritCoin  $spiritCoin
     * @return \Illuminate\Http\Response
     */
    public function show(SpiritCoin $coin)
    {
        return view("spirit-coins.form",["data" => $coin]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SpiritCoin  $spiritCoin
     * @return \Illuminate\Http\Response
     */
    public function edit(SpiritCoin $coin)
    {
        return view("spirit-coins.form",["data" => $coin]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SpiritCoin  $spiritCoin
     * @return \Illuminate\Http\Response
     */
    public function update(SpiritCoinRequest $request, SpiritCoin $coin)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request,$coin) {
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
                    $directory = "spirit-cons" . DIRECTORY_SEPARATOR . $coin->spirit_coin_id;
                    $filename = uniqid() . "." . $extension;
                    $path = $file->storeAs($directory, $filename, "public");
                    if ($path) {
                        SpiritCoinImage::updateOrCreate(
                            ["spirit_coin_id" => $coin->spirit_coin_id],
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
            return redirect()->route("spirit-coin.index")->with("success", "บันทึกข้อมูลร้านค้าสปิริตคอยน์เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SpiritCoin  $spiritCoin
     * @return \Illuminate\Http\Response
     */
    public function destroy(SpiritCoin $coin)
    {
        $coin->update(["status" => 0 , "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลร้านค้าสปิริตคอยน์เรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\SpiritCoin  $coin
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, SpiritCoin $coin)
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
            return redirect()->back()->with("success", "บันทึกข้อมูลร้านค้าสปิริตคอยน์เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\SpiritCoin  $coin
     * @return \Illuminate\Http\Response
     */
    public function restore(SpiritCoin $coin)
    {
        $coin->update(["status" => 1 , "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูลร้านค้าสปิริตคอยน์เรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\SpiritCoin  $coin
     * @return \Illuminate\Http\Response
     */
    public function active(SpiritCoin $coin)
    {
        $coin->update(["active" => $coin->active == 1 ? 0 : 1]);
        return response()->json(["status" => true]);
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\SpiritCoin  $coin
     * @return \Illuminate\Http\Response
     */
    public function priority(Request $request, SpiritCoin $coin)
    {
        $coin->update(["priority" => $request->priority]);
        return response()->json(["status" => true]);
    }
}
