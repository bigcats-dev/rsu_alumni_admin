<?php

namespace App\Http\Controllers;

use App\Http\Requests\VenderRequest;
use App\Models\Vender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;
use App\Mail\VenderRegistration;
use App\Models\User;
use App\Models\VenderFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function PHPSTORM_META\map;

class VenderController extends Controller
{
    public function __construct() {
        $this->middleware("can:view-vender");
        $this->middleware("can:create-vender")->only(["create","store"]);
        $this->middleware("can:update-vender")->only("update");
        $this->middleware("can:del-vender")->only("destroy");
        $this->middleware("can:approve-vender")->only("approve");
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
            $columnSorts = [DB::raw("seqnum"), "venders.created_at", "venders.updated_at"];
            $typeQuery = request()->input("type");
            $whereCondition = function ($query) use ($typeQuery) {
                if ($typeQuery != "3") {
                    $query->where("approved", $typeQuery)->where("status", 1);
                } else {
                    $query->where("status", 0);
                }
            };
            $totalData = Vender::where($whereCondition)->count();

            $buildQuery = Vender::selectRaw("row_number() over (order by venders.vender_id desc) as seqnum,venders.*,u_create.fullname as u_create_name,u_update.fullname as u_update_name")
                ->join("users as u_create", "venders.user_create_id", "=", "u_create.id")
                ->leftjoin("users as u_update", "venders.user_update_id", "=", "u_update.id")
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
                    $i["created_at"] = Helper::convertToDateTimeYTh($i["created_at"]);
                    $i["updated_at"] = Helper::convertToDateTimeYTh($i["updated_at"]);
                    $i["action"]["view"] = route("vender.view", ["vender" => $i["vender_id"]]);
                    $i["action"]["edit"] = route("vender.edit", ["vender" => $i["vender_id"]]);
                    $i["action"]["delete"] = route("vender.destroy", ["vender" => $i["vender_id"]]);
                    $i["action"]["restore"] = route("vender.restore", ["vender" => $i["vender_id"]]);
                    $i["action"]["active"] = route("vender.active", ["vender" => $i["vender_id"]]);
                    $i["action"]["approve"] = route("vender.approve", ["vender" => $i["vender_id"]]);
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
        return view("venders.index", [
            "badge" => Vender::where([["approved", "=", 0], ["status", "=", 1]])->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("venders.form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VenderRequest $request)
    {
        $request->validated();
        try {
            $password = Helper::generateRandomString(10);
            $vender = DB::transaction(function () use ($request,$password) {
                $user = User::create([
                    "username" => $request->email,
                    "password" => bcrypt($password),
                    "email" => $request->email,
                    "fullname" => $request->corporation_name,
                ]);
                $vender = Vender::create($request->merge(["user_create_id" => auth()->id(), "vender_id" => $user->id])->all());
                $upload = function ($files, $type) use ($vender) {
                    foreach ($files as $file) {
                        $extension = $file->getClientOriginalExtension();
                        $directory = "venders" . DIRECTORY_SEPARATOR . $vender->vender_id;
                        $filename = uniqid() . "." . $extension;
                        $path = $file->storeAs($directory, $filename, "public");
                        if ($path) {
                            $vender->files()->save(new VenderFile([
                                "file_name" => $filename,
                                "file_origin_name" => $file->getClientOriginalName(),
                                "file_type" => $file->getClientMimeType(),
                                "file_size" => $file->getSize(),
                                "file_path" => $path,
                                "type_id" => $type,
                            ]));
                        }
                    }
                };

                for ($i = 1; $i <= 4; $i++) {
                    if ($request->hasFile("files_" . $i)) {
                        $upload($request->file("files_" . $i), $i);
                    }
                }

                return $vender;
            });

            if ($vender) {
                Mail::to($vender->user->email)->send(new VenderRegistration([
                    "password" => $password,
                    "fullname" => $vender->user->fullname,
                ]));
            }

            return redirect()->route("vender.index")->with("success", "บันทึกข้อมูล Vender เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vender  $vender
     * @return \Illuminate\Http\Response
     */
    public function show(Vender $vender)
    {
        $params["data"] = $vender;
        $params["files"] =  collect([1, 2, 3, 4])->map(function ($i) use ($vender) {
            return $vender->files()->type($i)->get();
        })->all();
        return view("venders.form", $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vender  $vender
     * @return \Illuminate\Http\Response
     */
    public function edit(Vender $vender)
    {
        $params["data"] = $vender;
        $params["files"] =  collect([1, 2, 3, 4])->map(function ($i) use ($vender) {
            return $vender->files()->type($i)->get();
        })->all();
        return view("venders.form", $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vender  $vender
     * @return \Illuminate\Http\Response
     */
    public function update(VenderRequest $request, Vender $vender)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request, $vender) {
                $vender->update($request->merge(["user_update_id" => auth()->id()])->all());
                if ($request->has("delfiles")) {
                    foreach ($request->input("delfiles") as $id) {
                        $file = VenderFile::find($id);
                        if (Storage::exists($file->file_path)) Storage::delete($file->file_path);
                        $file->delete();
                    }
                }

                $upload = function ($files, $type) use ($vender) {
                    foreach ($files as $file) {
                        $extension = $file->getClientOriginalExtension();
                        $directory = "venders" . DIRECTORY_SEPARATOR . $vender->vender_id;
                        $filename = uniqid() . "." . $extension;
                        $path = $file->storeAs($directory, $filename, "public");
                        if ($path) {
                            $vender->files()->save(new VenderFile([
                                "file_name" => $filename,
                                "file_origin_name" => $file->getClientOriginalName(),
                                "file_type" => $file->getClientMimeType(),
                                "file_size" => $file->getSize(),
                                "file_path" => $path,
                                "type_id" => $type,
                            ]));
                        }
                    }
                };

                for ($i = 1; $i <= 4; $i++) {
                    if ($request->hasFile("files_" . $i)) {
                        $upload($request->file("files_" . $i), $i);
                    }
                }

                return $vender;
            });
            return redirect()->route("vender.index")->with("success", "บันทึกข้อมูล Vender เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vender  $vender
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vender $vender)
    {
        $vender->update(["status" => 0 , "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูล Vender เรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\Vender  $vender
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, Vender $vender)
    {
        try {
            DB::transaction(function () use ($request, $vender) {
                $vender->update([
                    "approved" => $request->action,
                    "note" => $request->input("note"),
                    "status" => $request->action == "2"
                        ? 0
                        : $vender->status,
                    "user_approve_id" => auth()->id(),
                    "approved_at" => DB::raw("CURRENT_DATE"),
                ]);
            });
            return redirect()->back()->with("success", "บันทึกข้อมูล Vender เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\Vender  $vender
     * @return \Illuminate\Http\Response
     */
    public function active(Vender $vender)
    {
        $vender->update(["active" => $vender->active == 1 ? 0 : 1]);
        return response()->json(["status" => true]);
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\Vender  $vender
     * @return \Illuminate\Http\Response
     */
    public function restore(Vender $vender)
    {
        $vender->update(["status" => 1, "approved" => 0]);
        return redirect()->back()->with("success", "บันทึกข้อมูล Vender เรียบร้อย");
    }
}
