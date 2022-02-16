<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomGroup;
use App\Models\RoomSubGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function roomgroup()
    {
        return response()->json(RoomGroup::selectRaw("convert(ROOM_GROUP_UID,'utf8','us7ascii') AS ROOM_GROUP_ID,ROOM_GROUP_NAME_TH")->get());
    }

    public function roomsubgroup()
    {
        $build = RoomSubGroup::selectRaw("convert(ROOM_SUB_GROUP_UID,'utf8','us7ascii') AS ROOM_SUB_GROUP_ID,ROOM_SUB_GROUP_NAME_TH");
        if (request()->has("room_group_id")) {
            $build->where(DB::raw("convert(ROOM_GROUP_UID,'utf8','us7ascii')"),request()->input("room_group_id"));
        }   
        return response()->json($build->get());
    }

    public function room()
    {
        $build = Room::selectRaw("convert(ROOM_UID,'utf8','us7ascii') AS ROOM_ID,ROOM_NO,ROOM_NAME_TH,TOTAL_SEAT,EXAM_SEAT,AREA");
        if (request()->has("room_group_id")) {
            $build->where(DB::raw("convert(ROOM_GROUP_UID,'utf8','us7ascii')"),request()->input("room_group_id"));
        }

        if (request()->has("room_sub_group_id")) {
            $build->where(DB::raw("convert(ROOM_SUB_GROUP_UID,'utf8','us7ascii')"),request()->input("room_sub_group_id"));
        }
        return response()->json($build->get());
    }
}
