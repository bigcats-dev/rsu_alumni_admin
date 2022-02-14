@extends("layouts.main")
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>จัดการข้อมูลการติดต่อ</strong>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="div-box card-outline card-info p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('contact.store') }}" method="POST" id="frmSave" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <div class="col-xl-2 col-12">
                            <label for="">ที่อยู่</label>
                        </div>
                        <div class="col-xl-8 col-12">
                            <input 
                                type="text" 
                                class="form-control form-control-border" 
                                name="contact_address"
                                placeholder="ที่อยู่"
                                required
                                value="{{old("contact_address",$contact_address ?? "")}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xl-2 col-12">
                            <label for="">อีเมล</label>
                        </div>
                        <div class="col-xl-8 col-12">
                            <input 
                                type="text" 
                                class="form-control form-control-border"
                                data-rule-regex="^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$"
                                data-msg-regex="รูปแบบการกรอกอีเมลไม่ถูกต้อง"
                                name="contact_email"
                                placeholder="อีเมล"
                                required
                                value="{{old("contact_email",$contact_email ?? "")}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xl-2 col-12">
                            <label for="">เบอร์โทรศัพท์</label>
                        </div>
                        <div class="col-xl-8 col-12">
                            <input 
                                type="text" 
                                class="form-control form-control-border" 
                                name="contact_tel"
                                placeholder="เบอร์โทรศัพท์"
                                required
                                value="{{old("contact_tel",$contact_tel ?? "")}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xl-2 col-12">
                            <label for="">ลิงค์สมาคมศิษย์เก่า</label>
                        </div>
                        <div class="col-xl-8 col-12">
                            <input 
                                type="text" 
                                class="form-control form-control-border" 
                                name="alumni_web_url"
                                placeholder="http://example.com"
                                required
                                value="{{old("alumni_web_url",$alumni_web_url ?? "")}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xl-2 col-12">
                            <label for="">รูปแบนเนอร์สมาคมศิษย์เก่า</label>
                        </div>
                        <div class="col-xl-8 col-12">
                            @include("inputs.fileupload_img",[
                                "name" => "banner",
                                "required" => is_null($banner->file_path ? $banner : null) ,
                                "image" => $banner->file_path ? $banner : null
                            ])
                        </div>
                    </div>
                    <div class="col-10 p-0">
                        <hr>
                    </div>
                    <div class="form-group row">
                        <div class="col-xl-5 col-12">
                            <button type="submit" class="btn btn-success btn-block" data-loading-text="กรุณารอซักครู่...">บันทึก</button>
                        </div>
                        <div class="col-xl-5 col-12">
                            <button type="button" class="btn btn-secondary btn-block" onclick="window.location.reload()">ยกเลิก</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@section("script")
    <script src="{{ URL::asset("js/contact/index.js?t=".time()) }}"></script>
    <script src="{{ URL::asset("js/inputs/fileupload_img.js?t=".time()) }}"></script>
@endsection
