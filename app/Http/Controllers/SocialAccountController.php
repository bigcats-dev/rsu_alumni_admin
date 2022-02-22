<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;
use App\Http\Requests\SocialAccountRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SocialAccountController extends Controller
{
    public function __construct() {
        $this->middleware("can:view-social-account");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("socials.index", ["data" => SocialAccount::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("socials.form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SocialAccountRequest $request)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request) {
                $upload = false;
                if ($request->hasFile("images")) {
                    if (!in_array($request->file("images")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $upload = true;
                }

                $model = SocialAccount::create($request->merge(["user_create_id" => auth()->id()])->except(["images"]));
                if ($model) {
                    if ($upload) {
                        $file = $request->file("images");
                        $extension = $file->getClientOriginalExtension();
                        $directory = "socials" . DIRECTORY_SEPARATOR . $model->social_id;
                        $filename = uniqid() . "." . $extension;
                        $path = $file->storeAs($directory, $filename, "public");
                        if ($path) {
                            $model->update([
                                "icon_name" => $filename,
                                "icon_origin_name" => $file->getClientOriginalName(),
                                "icon_type" => $file->getClientMimeType(),
                                "icon_size" => $file->getSize(),
                                "icon_path" => $path,
                            ]);
                        }
                    }
                }
            });
            return redirect()->route("social.index")->with("success", "บันทึกข้อมูล Social Account เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SocialAccount  $social
     * @return \Illuminate\Http\Response
     */
    public function show(SocialAccount $social)
    {
        return view("socials.form", ["data" => $social]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SocialAccount  $social
     * @return \Illuminate\Http\Response
     */
    public function edit(SocialAccount $social)
    {
        return view("socials.form", ["data" => $social]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SocialAccount  $social
     * @return \Illuminate\Http\Response
     */
    public function update(SocialAccountRequest $request, SocialAccount $social)
    {
        $request->validated();
        try {
            DB::transaction(function () use ($request, $social) {
                $upload = false;
                if ($request->hasFile("images")) {
                    if (!in_array($request->file("images")->getClientOriginalExtension(), ["png", "jpg", "jpeg"])) {
                        throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                    }
                    $upload = true;
                }

                $social->update($request->merge(["user_update_id" => auth()->id()])->except(["images"]));
                if ($upload) {
                    $file = $request->file("images");
                    $extension = $file->getClientOriginalExtension();
                    $directory = "socials" . DIRECTORY_SEPARATOR . $social->social_id;
                    $filename = uniqid() . "." . $extension;
                    $path = $file->storeAs($directory, $filename, "public");
                    if ($path) {
                        $social->update([
                            "icon_name" => $filename,
                            "icon_origin_name" => $file->getClientOriginalName(),
                            "icon_type" => $file->getClientMimeType(),
                            "icon_size" => $file->getSize(),
                            "icon_path" => $path,
                        ]);
                    }
                }
            });
            return redirect()->route("social.index")->with("success", "บันทึกข้อมูล Social Account เรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SocialAccount  $social
     * @return \Illuminate\Http\Response
     */
    public function destroy(SocialAccount $social)
    {
        Storage::delete($social->file_path);
        $social->delete();
        return redirect()->back()->with("success", "บันทึกข้อมูล Social Account เรียบร้อย");
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\SocialAccount  $social
     * @return \Illuminate\Http\Response
     */
    public function active(SocialAccount $social)
    {
        $social->update(["active" => $social->active == 1 ? 0 : 1]);
        return response()->json(["status" => true]);
    }

    /**
     * Set the specified resource from storage.
     *
     * @param  \App\Models\SocialAccount  $social
     * @return \Illuminate\Http\Response
     */
    public function priority(Request $request, SocialAccount $social)
    {
        $social->update(["priority" => $request->priority]);
        return response()->json(["status" => true]);
    }
}
