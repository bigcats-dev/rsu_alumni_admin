<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config as FacadesConfig;
use Illuminate\Support\Facades\DB;

class ConfigurationController extends Controller
{
    public function contact()
    {
        return view("contact.index",[
            "contact_address" => FacadesConfig::get("global.contact_address"),
            "contact_email" => FacadesConfig::get("global.contact_email"),
            "contact_tel" => FacadesConfig::get("global.contact_tel"),
            "alumni_web_url" => FacadesConfig::get("global.alumni_web_url"),
            "banner" => (object) json_decode(FacadesConfig::get("global.banner")),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                foreach ($request->all() as $key => $value) {
                    if (FacadesConfig::has("global." . $key))
                        if (strcmp($key,"banner") == 0) {
                            if ($request->hasFile("banner")) {
                                $file = $request->file("banner");
                                $extension = $file->getClientOriginalExtension();
                                if (!in_array($extension, ["png", "jpg", "jpeg"])) {
                                    throw new \Exception("กรุณาเลือกไฟล์รูปภาพ (นามสกุล .png .jpg .jpeg)");
                                }  
                                $filename = uniqid() . "." . $extension;
                                $directory = "banners" . DIRECTORY_SEPARATOR;
                                $path = $file->storeAs($directory, $filename, "public");
                                if ($path) {
                                    Config::where("name",$key)->update([
                                        "value" => json_encode([
                                            "file_path" => $path,
                                            "file_origin_name" => $file->getClientOriginalName(),
                                            "file_name" => $filename,
                                            "file_size" => $file->getSize(),
                                        ])
                                    ]);
                                }
                            }
                        } else {
                            Config::where("name",$key)->update([
                                "value" => $value
                            ]);
                        }
                }
            });
            return redirect()->back()->with("success","บันทึกข้อมูลเรียบร้อย");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }
}
