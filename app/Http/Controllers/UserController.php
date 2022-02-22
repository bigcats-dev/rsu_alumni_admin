<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware("can:view-user");
        $this->middleware("can:update-user")->only("update");
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
            $columnSorts = [DB::raw("seqnum"), "users.created_at",];
            $whereCondition = function ($query) {
                $query->where("is_admin", 1);
                $query->where("id", "<>", Auth::id());
            };
            $totalData = User::where($whereCondition)->count();

            $buildQuery = User::selectRaw("row_number() over (order by users.id desc) as seqnum,users.*,roles.role_name_th")
                ->leftjoin("roles", "users.role_id", "=", "roles.role_id")
                ->where($whereCondition)
                ->where("role_level","<=",$this->user()->role->role_level);

            if (Str::of(request()->input("search.value"))->trim()->isNotEmpty()) {
                $target = request()->input("search.value");
                $buildQuery->where("fullname", "LIKE", "%{$target}%");
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
                    $i["action"]["view"] = route("user.view", ["user" => $i["id"]]);
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
        return view("users.index");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $roles = Role::all();
        /* check user role has super administrator
        * 
        * 
        */
        if ($this->user()->isSuperAdmin()) {
            /**
             * get all roles to assign another user to super administrator
             * 
             */
            $roles = Role::all();
        } else {
            $roles = Role::notSuperAdmin()->get();
        }
        return view("users.form", compact("user", "roles"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        try {
            DB::transaction(function () use ($request, $user) {
                $user->update($request->all());
            });
            return redirect()->route("user.index")->with("success", "บันทึกข้อมูลผู้ใช้งานเรียบร้อย");
        } catch (\PDOException $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }
}
