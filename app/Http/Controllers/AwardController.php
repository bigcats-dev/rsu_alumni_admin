<?php

namespace App\Http\Controllers;

use App\Models\AwardType;
use Illuminate\Http\Request;

class AwardController extends Controller
{
    public function __construct() {
        $this->middleware("can:view-award");
        $this->middleware("can:create-award")->only("store");
        $this->middleware("can:update-award")->only("update");
        $this->middleware("can:del-award")->only("destroy");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("award.index",["data" => AwardType::noneSubType()->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        AwardType::create($request->all());
        return redirect()->back()->with("success","บันทึกข้อมูลประเภทรางวัลเรียบร้อย");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AwardType  $awardType
     * @return \Illuminate\Http\Response
     */
    public function show(AwardType $award)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AwardType  $awardType
     * @return \Illuminate\Http\Response
     */
    public function edit(AwardType $award)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AwardType  $awardType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AwardType $award)
    {
        $award->update($request->only(["award_type_name"]));
        return redirect()->back()->with("success","บันทึกข้อมูลประเภทรางวัลเรียบร้อย");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AwardType  $awardType
     * @return \Illuminate\Http\Response
     */
    public function destroy(AwardType $award)
    {
        $award->delete();
        return redirect()->back()->with("success","บันทึกข้อมูลประเภทรางวัลเรียบร้อย");
    }

     /**
     * set the specified resource from storage.
     *
     * @param  \App\Models\AwardType  $awardType
     * @return \Illuminate\Http\Response
     */
    public function active(AwardType $award)
    {
        $award->update(["active" => $award->active == 1 ? 0 : 1]);
        if (request()->ajax())
        {
            return response()->json(["status" => true]);
        }
        return redirect()->back()->with("success","บันทึกข้อมูลประเภทรางวัลเรียบร้อย");
    }
}
