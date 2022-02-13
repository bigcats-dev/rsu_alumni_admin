@extends("layouts.main")
@section("css") 
@endsection
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>กิจกรรมสปิริตคอยน์</strong>
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
                                @if (Request::route()->getName() == "spirit-coin-activity.create")
                                    action="{{route("spirit-coin-activity.store")}}"
                                @else
                                    action="{{route("spirit-coin-activity.update",["coin" => $data->spirit_coin_activity_id])}}"
                                @endif 
                                method="POST" id="frmSave" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">ชื่อกิจกรรมสปิริตคอยน์</label>
                                    </div> 
                                    <div class="col-xl-8 col-12"> 
                                        <input 
                                        type="text" 
                                        class="form-control form-control-border" 
                                        name="name" 
                                        required
                                        placeholder="ชื่อกิจกรรมสปิริตคอยน์"
                                        value="{{old("name",$data->name ?? "")}}">
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
                                            cols="40" 
                                            rows="4" 
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
                                            cols="40" 
                                            rows="4" 
                                            required>{{old("detail",$data->detail ?? "")}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">วันที่เริ่ม</label>
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
                                            <label class="rangex pl-2 pr-2">วันที่สิ้นสุด</label>
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
                                            required
                                            placeholder="สถานที่"
                                            value="{{old("location",$data->location ?? "")}}">
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">รูปร้านค้าสปิริตคอยน์</label>
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
    <script src="{{ URL::asset("js/spirit-coin-activity/form.js?t=".time()) }}"></script>
    {{-- input fileupload --}}
    <script src="{{ URL::asset("js/inputs/fileupload_img.js?t=".time()) }}"></script>
@endsection