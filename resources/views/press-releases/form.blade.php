@extends("layouts.main")
@section("css") 
@endsection
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>ข่าวสารประชาสัมพันธ์</strong>
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
                                @if (Request::route()->getName() == "press-release.create")
                                    action="{{route("press-release.store")}}"
                                @else
                                    action="{{route("press-release.update",["training_new" => $data->training_news_id])}}"
                                @endif
                                method="POST" id="frmSave" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-xl-1 col-12">
                                        <label for="">หัวข้อข่าว</label>
                                    </div>
                                    <div class="col-xl-11 col-12">
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
                                    <div class="col-xl-1 col-12">
                                        <label for="">บทนำ</label>
                                    </div>
                                    <div class="col-xl-11 col-12">
                                        <textarea 
                                            class="form-control form-textarea summernote" 
                                            name="introduction" 
                                            cols="30"
                                            rows="6"
                                            required>{{old("introduction",$data->introduction ?? "")}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-1 col-12">
                                        <label for="">รายละเอียด</label>
                                    </div>
                                    <div class="col-xl-11 col-12">
                                        <textarea 
                                            class="form-control form-textarea summernote" 
                                            name="detail" 
                                            cols="30"
                                            rows="4"
                                            required>{{old("detail",$data->detail ?? "")}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-1 col-12">
                                        <label for="">วันที่เริ่มข่าว</label>
                                    </div>
                                    <div class="col-xl-11 col-12">
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
                                    <div class="col-xl-1 col-12">
                                        <label for="">รูปข่าวสาร</label>
                                    </div>
                                    <div class="col-xl-11 col-12">
                                        @include("inputs.fileupload_img",[
                                            "name" => "files",
                                            "required" => is_null($data->image ?? null) ,
                                            "image" => $data->image ?? null,
                                            "width" => 700,
                                            "height" => 350
                                        ])
                                    </div>
                                </div>
                                <div class="col-12 p-0">
                                    <hr>
                                </div>

                                <div class="form-group row">
                                    <div class="col-xl-6 col-12">
                                        <button type="submit" class="btn btn-success btn-block" data-loading-text="กรุณารอซักครู่...">บันทึก</button>
                                    </div>
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
    </section>
@endsection
@section("script") 
    <script src="{{ URL::asset("js/press-release/form.js?t=".time()) }}"></script>
    {{-- input fileupload --}}
    <script src="{{ URL::asset("js/inputs/fileupload_img.js?t=".time()) }}"></script>
@endsection
