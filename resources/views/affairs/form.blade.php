@extends("layouts.main")
@section("css")
@endsection
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>กิจการศิษย์เก่า</strong>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
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
                                @if (Request::route()->getName() == "alumni-affairs.create")
                                    action="{{route("alumni-affairs.store")}}"
                                @else
                                    action="{{route("alumni-affairs.update",["affairs" => $data->alumni_affairs_id])}}"
                                @endif 
                                method="POST" id="frmSave" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">ชื่อกิจการ</label>
                                    </div> 
                                    <div class="col-xl-8 col-12"> 
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="title"
                                            required
                                            placeholder="ชื่อกิจการ"
                                            value="{{old("title",$data->title ?? "")}}">
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">Hyperlink </label>
                                    </div> 
                                    <div class="col-xl-8 col-12"> 
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border"
                                            data-msg-url="รูปแบบ URL ไม่ถูกต้อง"
                                            name="hyperlink"
                                            placeholder="http,https://example.com"
                                            value="{{old("hyperlink",$data->hyperlink ?? "")}}">
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">ประเภท Hyperlink</label>
                                    </div> 
                                    <div class="col-xl-8 col-12"> 
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input 
                                                    type="radio" 
                                                    class="form-check-input" 
                                                    name="hyperlink_type" 
                                                    value="1"
                                                    required
                                                    data-msg-required="จำเป็นต้องเลือก"
                                                    {{old("hyperlink_type",$data->hyperlink_type ?? "") == "1"
                                                        ? "checked"
                                                        : ""}}>กลุ่ม facebook
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input 
                                                    type="radio" 
                                                    class="form-check-input" 
                                                    name="hyperlink_type" 
                                                    value="2"
                                                    required
                                                    data-msg-required="จำเป็นต้องเลือก"
                                                    {{old("hyperlink_type",$data->hyperlink_type ?? "") == "2"
                                                        ? "checked"
                                                        : ""}}>Facebook Fanpage และร้านค้า IG 
                                            </label>
                                        </div>
                                        <div class="form-check-inline disabled">
                                            <label class="form-check-label">
                                                <input 
                                                    type="radio" 
                                                    class="form-check-input" 
                                                    name="hyperlink_type" 
                                                    value="3"
                                                    required
                                                    data-msg-required="จำเป็นต้องเลือก"
                                                    {{old("hyperlink_type",$data->hyperlink_type ?? "") == "3"
                                                        ? "checked"
                                                        : ""}}>Website
                                            </label>
                                        </div>
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">รายละเอียด</label>
                                    </div> 
                                    <div class="col-xl-8 col-12"> 
                                        <textarea 
                                            class="form-control form-textarea summernote" 
                                            name="detail" 
                                            cols="30" 
                                            rows="4"
                                            required>{{old("detail",$data->detail ?? "")}}</textarea>
                                    </div>
                                </div>   
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">รูปกิจการ</label>
                                    </div>
                                    <div class="col-xl-8 col-12">
                                        @include("inputs.fileupload_img",[
                                            "name" => "files",
                                            "required" => is_null($data->image ?? null) ,
                                            "image" => $data->image ?? null,
                                            "width" => 341,
                                            "height" => 180,
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
                                        <button type="button" class="btn btn-secondary btn-block" onclick="window.history.back()">กลับ</button>
                                    </div>
                                </div>  
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection    
@section("script")
    <script src="{{ URL::asset("js/affairs/form.js?t=".time()) }}"></script>
    {{-- input fileupload --}}
    <script src="{{ URL::asset("js/inputs/fileupload_img.js?t=".time()) }}"></script>
@endsection