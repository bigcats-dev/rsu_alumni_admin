@extends("layouts.main")
@section("content")
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <strong>จัดการผู้ใช้งาน</strong>
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
                            <form action="{{route("user.update",["user" => $user->id])}}" method="POST" id="frmSave" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">ชื่อ - นามสกุล</label>
                                    </div>
                                    <div class="col-xl-10 col-12">
                                        <input
                                            @cannot("update-user")
                                                disabled
                                            @endcannot
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="fullname"
                                            placeholder="ชื่อ - นามสกุล"
                                            required
                                            value="{{old("fullname",$user->fullname ?? "")}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">อีเมล</label>
                                    </div>
                                    <div class="col-xl-10 col-12">
                                        <input
                                            @cannot("update-user")
                                                disabled
                                            @endcannot
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="email"
                                            placeholder="อีเมล"
                                            required
                                            data-rule-regex="^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$"
                                            data-msg-regex="รูปแบบการกรอกอีเมลไม่ถูกต้อง"
                                            value="{{old("email",$user->email ?? "")}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">สิทธิ์การใช้งานระบบ</label>
                                    </div>
                                    <div class="col-xl-10 col-12">
                                        <select 
                                            @cannot("update-user")
                                                disabled
                                            @endcannot
                                            class="custom-select form-control-border" 
                                            name="role_id" 
                                            required 
                                            data-msg-required="จำเป็นต้องเลือก">
                                            <option value="">-- เลือก --</option>
                                            @if (sizeof($roles) > 0)
                                                @foreach ($roles as $role)
                                                    <option 
                                                        value="{{$role->role_id}}"
                                                        {{old("role_id",$user->role_id) == $role->role_id
                                                            ? "selected"
                                                            : ""}}>{{$role->role_name_th}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 p-0"> 
                                    <hr>
                                </div>
                                <div class="form-group row">
                                    @can("update-user")
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
    <script src="{{asset("js/user/form.js?t=" . time())}}"></script>
@endsection
