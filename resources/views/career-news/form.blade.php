@extends("layouts.main")
@section("css") 
@endsection
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>ข่าวอบรมหลักสูตรอาชีพ</strong>
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
                                @if (Request::route()->getName() == "career-news.create")
                                    action="{{route("career-news.store")}}"
                                @else
                                    action="{{route("career-news.update",["news" => $data->career_news_id])}}"
                                @endif
                                method="POST" id="frmSave" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">หัวข้อข่าว</label>
                                    </div>
                                    <div class="col-xl-8 col-12">
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="title"
                                            placeholder="หัวข้อข่าว"
                                            required
                                            value="{{old("title",$data->title ?? "")}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">บทนำ</label>
                                    </div>
                                    <div class="col-xl-8 col-12">
                                        <textarea 
                                            class="form-control form-textarea summernote" 
                                            name="introduction" 
                                            cols="30"
                                            rows="6"
                                            required>{{old("introduction",$data->introduction ?? "")}}</textarea>
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
                                        <label for="">วันที่เริ่มข่าว</label>
                                    </div>
                                    <div class="col-xl-8 col-12">
                                        <div class="input-daterange input-group" id="datepicker">
                                            <input 
                                                type="text" 
                                                class="form-control form-control-border" 
                                                name="start_date" 
                                                placeholder="เริ่มต้น"
                                                autocomplete="off"
                                                required
                                                value="{{old("start_date",Helper::convertToDateTh($data->start_date ?? null))}}"/>
                                            <label class="rangex pl-2 pr-2">วันที่สิ้นสุดข่าว</label>
                                            <input 
                                                type="text" 
                                                class="form-control form-control-border" 
                                                name="end_date" 
                                                placeholder="สิ้นสุด"
                                                autocomplete="off"
                                                required
                                                value="{{old("end_date",Helper::convertToDateTh($data->end_date ?? null))}}"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">สถานที่</label>
                                    </div>
                                    <div class="col-xl-8 col-12">
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="location"
                                            placeholder="สถานที่"
                                            required
                                            value="{{old("location",$data->location ?? "")}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">Link</label>
                                    </div>
                                    <div class="col-xl-8 col-12">
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border"
                                            name="link"
                                            autocomplete="off"
                                            data-rule-url="true"
                                            data-msg-url="รูปแบบ URL ไม่ถูกต้อง"
                                            placeholder="http://example.com"
                                            value="{{old("link",$data->link ?? "")}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">รูปโปสเตอร์</label>
                                    </div>
                                    <div class="col-xl-8 col-12">
                                        @include("inputs.fileupload_img",[
                                            "name" => "files",
                                            "required" => is_null($data->image ?? null) ,
                                            "image" => $data->image ?? null
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
    <script src="{{ URL::asset("js/career-news/form.js?t=".time()) }}"></script>
    {{-- input fileupload --}}
    <script src="{{ URL::asset("js/inputs/fileupload_img.js?t=".time()) }}"></script>
@endsection
