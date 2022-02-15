@extends("layouts.main")
@section("css") 
@endsection
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>เพิ่มศิษย์เก่าดีเด่น</strong>
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
                                @if (Request::route()->getName() == "alumni-glory.create")
                                    action="{{route("alumni-glory.store")}}"
                                @else
                                    action="{{route("alumni-glory.update",["alumni" => $data->alumni_glory_id])}}"
                                @endif 
                                method="POST" id="frmSave" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-xl-6">
                                        <label for="">ชื่อ-นามสกุล</label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="name" 
                                            required
                                            placeholder="ชื่อ-นามสกุล"
                                            value="{{old("name",$data->name ?? "")}}">
                                    </div> 
                                    <div class="col-xl-6">
                                        <label for="">ปีที่จบการศึกษา</label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="year"
                                            data-rule-integer="true"
                                            data-msg-integer="กรอกข้อมูลที่เป็นตัวเลข"
                                            required
                                            placeholder="ตัวเลข"
                                            value="{{old("year",$data->year ?? "")}}">
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <div class="col-xl-6">
                                        <label for="">คณะ</label>
                                        <select 
                                            class="selectpicker" 
                                            title="คณะ" 
                                            data-show-subtext="true" 
                                            data-live-search="true" 
                                            name="faculty_id"
                                            data-msg-required="กรุณาเลือก"
                                            required>  
                                            <option value="">-- เลือก --</option>
                                            @if (sizeof($ms_faculty) > 0)
                                                @foreach ($ms_faculty as $i)
                                                    <option 
                                                        value="{{$i->faculty_id}}"
                                                        {{old("faculty_id",$data->faculty_id ?? "") == $i->faculty_id
                                                            ? "selected"
                                                            : ""}}>({{$i->faculty_code}}) {{$i->faculty_name_th}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div> 
                                    <div class="col-xl-6">
                                        <label for="">สาขา</label>
                                        <select 
                                            class="selectpicker" 
                                            title="สาขาวิชา" 
                                            data-show-subtext="true" 
                                            data-live-search="true" 
                                            name="major_id"
                                            data-msg-required="กรุณาเลือก"
                                            required>  
                                            <option value="">-- เลือก --</option>
                                            @if (sizeof($ms_major) > 0)
                                                @foreach ($ms_major as $i)
                                                    <option 
                                                        value="{{$i->major_id}}"
                                                        {{old("major_id",$data->major_id ?? "") == $i->major_id
                                                            ? "selected"
                                                            : ""}}>({{$i->major_code}}) {{$i->major_name_th}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <div class="col-xl-6">
                                        <label for="">ตำแหน่งงานปัจจุบัน</label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="position"
                                            required
                                            placeholder="ตำแหน่งงานปัจจุบัน"
                                            value="{{old("position",$data->position ?? "")}}">
                                    </div> 
                                    <div class="col-xl-6">
                                        <label for="">บริษัท / หน่วยงาน</label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="company"
                                            required
                                            placeholder="บริษัท / หน่วยงาน"
                                            value="{{old("company",$data->company ?? "")}}">
                                    </div> 
                                </div> 
                                <div class="form-group row">
                                    <div class="col-xl-6">
                                        <label for="">ระดับการศึกษา</label>
                                        <select 
                                            class="selectpicker" 
                                            title="สาขา" 
                                            data-show-subtext="true" 
                                            data-live-search="true" 
                                            name="education_level_id"
                                            required> 
                                            @if (sizeof($ms_education) > 0)
                                                @foreach ($ms_education as $i)
                                                    <option 
                                                        value="{{$i->education_level_id}}"
                                                        {{old("education_level_id",$data->education_level_id ?? "") == $i->education_level_id
                                                            ? "selected"
                                                            : ""}}>{{$i->education_level_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div> 
                                    <div class="col-xl-6">
                                        <label for="">วันที่ได้รับรางวัล</label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="award_date"
                                            required
                                            placeholder="วัน/เดือน/ปี"
                                            autocomplete="off"
                                            value="{{old("award_date",Helper::convertToDateTh($data->award_date ?? null))}}">
                                    </div> 
                                </div> 
                                <div class="form-group row">
                                    <div class="col-xl-6">
                                        <label for="">ประเภทรางวัล</label>
                                        <select 
                                            class="selectpicker" 
                                            title="ประเภทรางวัล" 
                                            data-show-subtext="true" 
                                            data-live-search="true" 
                                            name="award_type_id"
                                            data-msg-required="กรุณาเลือก"
                                            required>  
                                            <option value="">-- เลือก --</option>
                                            @if (sizeof($ms_award_type) > 0)
                                                @foreach ($ms_award_type as $i)
                                                    <option 
                                                        value="{{$i->award_type_id}}"
                                                        {{old("award_type_id",$data->award_type_id ?? "") == $i->award_type_id
                                                            ? "selected"
                                                            : ""}}>{{$i->award_type_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>  
                                    <div class="col-xl-6 profess">
                                        <label for="">ด้านวิชาชีพ  </label>
                                        <select 
                                            class="selectpicker" 
                                            title="ด้านวิชาชีพ" 
                                            data-show-subtext="true" 
                                            data-live-search="true" 
                                            name="award_sub_type_id"
                                            data-msg-required="กรุณาเลือก">  
                                            <option value="">-- เลือก --</option>
                                            @if (sizeof($ms_award_sub_type) > 0)
                                                @foreach ($ms_award_sub_type as $i)
                                                    <option 
                                                        value="{{$i->award_type_id}}"
                                                        {{old("award_sub_type_id",$data->award_sub_type_id ?? "") == $i->award_type_id
                                                            ? "selected"
                                                            : ""}}>{{$i->award_type_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>  
                                </div>  
                                <div class="form-group"> 
                                    <div class="col-xl-2 col-12">
                                        <label for="">รูป</label>
                                    </div>
                                    <div class="col-xl-6 col-12">
                                        @include("inputs.fileupload_img",[
                                            "name" => "images",
                                            "required" => is_null($data->image ?? null) ,
                                            "image" => $data->image ?? null,
                                            "multiple" => false,
                                            "width" => 170,
                                            "height" => 230
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
    <script 
        id="jsFrmAlumniGlory"
        data-major='@json($ms_major)'
        data-award_sub_type='@json($ms_award_sub_type)'
        src="{{ URL::asset("js/alumni-glory/form.js?t=".time()) }}"></script>
    {{-- input fileupload --}}
    <script src="{{ URL::asset("js/inputs/fileupload_img.js?t=".time()) }}"></script>
@endsection