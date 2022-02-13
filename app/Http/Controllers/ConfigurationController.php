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
                        Config::where("name",$key)->update([
                            "value" => $value
                        ]);
                }
            });
            return redirect()->back()->with("success","บันทึกข้อมูลเรียบร้อย");
        } catch (\PDOException $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }
}
