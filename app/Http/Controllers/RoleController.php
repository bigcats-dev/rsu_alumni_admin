<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct() {
        $this->middleware("can:view-role");
        $this->middleware("can:update-role")->only("update");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::notSuperAdmin()->get();
        return view("roles.index", compact("roles"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("roles.form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $permissions = Permission::all()->mapToGroups(function ($i) {
            return [$i->menu_id => $i];
        });
        $menus = Menu::all(["menu_id", "name"])->keyBy("menu_id")
            ->transform(function ($m) {
                return $m->name;
            })
            ->sortKeys()
            ->toArray();
        return view("roles.form", compact("role", "menus", "permissions"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view("roles.form", compact("role"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        try {
            DB::transaction(function () use ($request, $role) {
                $role->update(["role_name_th" => $request->role_name_th]);
                if ($request->has('permissions')) $role->permissions()->sync($request->input('permissions'));
                else $role->permissions()->detach();
            });
            return redirect()->route("role.index")->with("success", "บันทึกสิทธิ์การเข้าใช้งานระบบเรียบร้อย");
        } catch (\PDOException $e) {
            return redirect()->back()->withInput()->withErrors(["msg" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        //
    }
}
