@extends("layouts.main")
@section("css")
@endsection
@section("content")
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <strong>จัดการสิทธิ์การใช้งาน</strong>
                </h1>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid tab-pane">
        <div class="overlay-wrapper">
            <div class="overlay" id="tab-overlay" style="display: none;">
                <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                <div class="text-bold pt-2">Loading...</div>
            </div>
            <div class="div-box card-outline card-info p-4">
                <div class="col">
                    <div class="row justify-content-center">
                        <div class="col-xl-10 col-12">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form
                                @if (Request::route()->getName() == "role.create")
                                    action="{{route("role.store")}}"
                                @else
                                    action="{{route("role.update",["role" => $role->role_id])}}"
                                @endif 
                                method="POST" id="frmSave" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">ชื่อสิทธิ์การใช้งาน</label>
                                    </div> 
                                    <div class="col-xl-8 col-12"> 
                                        <input
                                            @cannot("update-role")
                                                disabled
                                            @endcannot
                                            type="text" 
                                            name="role_name_th" 
                                            class="form-control form-control-border" 
                                            value="{{$role->role_name_th}}"
                                            required>
                                    </div>
                                </div>
                                <table class="table table-striped" style="width: 100%;font-size:20px">
                                    <thead>
                                        <tr>
                                            <th width="20%">เมนู</th>
                                            <th>การอนุญาต</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($permissions as $key => $permission)
                                            @if (sizeof($permission) > 0)
                                                <tr>
                                                    <td><b>{{$menus[$key]}}</b></td>
                                                    <td>
                                                        @foreach ($permission as $p)
                                                            <div class="form-check form-check-inline">
                                                                <input
                                                                    @cannot("update-role")
                                                                        disabled
                                                                    @endcannot
                                                                    class="form-check-input" 
                                                                    type="checkbox" 
                                                                    id="pemission_{{$p->permission_id}}" 
                                                                    value="{{$p->permission_id}}"
                                                                    name="permissions[]"
                                                                    {{old("permissions." . $p->permission_id,$role->hasPermission($p->slug))
                                                                        ? "checked"
                                                                        : ""}}>
                                                                <label style="font-weight: normal" class="form-check-label" for="pemission_{{$p->permission_id}}">{{$p->name_th}}</label>
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="col-12 p-0"> 
                                    <hr>
                                </div>
                                <div class="form-group row">
                                    @can("update-role")
                                        <div class="col-xl-6 col-12">
                                            <button type="submit" class="btn btn-success btn-block" data-loading-text="กรุณารอซักครู่...">บันทึก</button>
                                        </div> 
                                    @endcan
                                    <div class="col-xl-6 col-12">
                                        <button type="button" class="btn btn-secondary btn-block" onclick="window.history.back()">กลับ</button>
                                    </div>
                                </div>  
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section("script")
    <script src="{{asset("js/role/form.js?t=".time())}}"></script>
@endsection