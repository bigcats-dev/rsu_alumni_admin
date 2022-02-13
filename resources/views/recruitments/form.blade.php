@extends("layouts.main")
@section("css")
@endsection
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>การรับสมัครงาน</strong>
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
                                @if (Request::route()->getName() == "recruitment.create")
                                    action="{{route("recruitment.store")}}"
                                @else
                                    action="{{route("recruitment.update",["recruitment" => $data->recruitment_id])}}"
                                @endif 
                                method="POST" id="frmSave" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-xl-6">
                                        <label for="">ชื่อบริษัท / หน่วยงาน ที่รับสมัคร</label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            required
                                            name="company"
                                            placeholder="ชื่อบริษัท / หน่วยงาน ที่รับสมัคร"
                                            value="{{old("company",$data->company ?? "")}}">
                                    </div> 
                                    <div class="col-xl-6">
                                        <label for="">ประเภทธุรกิจ</label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="business_type"
                                            placeholder="ประเภทธุรกิจ"
                                            required
                                            value="{{old("business_type",$data->business_type ?? "")}}">
                                    </div> 
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-6">
                                        <label for="">ตำแหน่ง </label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border"  
                                            required 
                                            name="position"
                                            placeholder="ตำแหน่ง"
                                            value="{{old("position",$data->position ?? "")}}">
                                    </div> 
                                    <div class="col-xl-6">
                                        <label for="">จำนวนที่รับสมัคร</label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="number_of_applications"
                                            required
                                            placeholder="จำนวนที่รับสมัคร"
                                            value="{{old("number_of_applications",$data->number_of_applications ?? "")}}">
                                    </div> 
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-6">
                                        <label for="" class="col-12">เพศ </label>
                                        <div class="col-12">
                                            <div class="form-check form-check-inline">
                                                <input
                                                    required
                                                    data-msg-required="จำเป็นต้องเลือก"
                                                    class="form-check-input" 
                                                    type="radio" 
                                                    name="gender" 
                                                    id="gender_1" 
                                                    value="M"
                                                    {{old("gender",$data->gender ?? "") == "M"
                                                        ? "checked"
                                                        : ""}}>
                                                <label class="form-check-label" for="gender_1">ชาย</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input
                                                    required
                                                    data-msg-required="จำเป็นต้องเลือก"
                                                    class="form-check-input" 
                                                    type="radio" 
                                                    name="gender" 
                                                    id="gender_2" 
                                                    value="F"
                                                    {{old("gender",$data->gender ?? "") == "F"
                                                        ? "checked"
                                                        : ""}}>
                                                <label class="form-check-label" for="gender_2">หญิง</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input
                                                    required
                                                    data-msg-required="จำเป็นต้องเลือก"
                                                    class="form-check-input" 
                                                    type="radio" 
                                                    name="gender" 
                                                    id="gender_3" 
                                                    value="A"
                                                    {{old("gender",$data->gender ?? "") == "A"
                                                        ? "checked"
                                                        : ""}}>
                                                <label class="form-check-label" for="gender_3">ทั้งชายและหญิง</label>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-xl-6">
                                        <label for="">อายุ  </label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="age"
                                            placeholder="อายุ"
                                            required  
                                            value="{{old("age",$data->age ?? "")}}">
                                    </div> 
                                </div> 
                                <div class="form-group row">
                                    <div class="col-xl-6">
                                        <label for="">วุฒิการศึกษา </label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="education"
                                            placeholder="วุฒิการศึกษา"
                                            required  
                                            value="{{old("education",$data->education ?? "")}}">
                                    </div> 
                                    <div class="col-xl-6">
                                        <label for="">ประสบการณ์</label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="experience"
                                            placeholder="ประสบการณ์"
                                            required  
                                            value="{{old("experience",$data->experience ?? "")}}">
                                    </div> 
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-12">
                                        <label for="">ลักษณะงาน</label>
                                        <textarea 
                                            class="form-control form-textarea summernote" 
                                            name="nature_of_work" 
                                            cols="30" 
                                            rows="4" 
                                            required>{{old("nature_of_work",$data->nature_of_work ?? "")}}</textarea>
                                    </div> 
                                </div> 
                                <div class="form-group row">
                                    <div class="col-xl-12">
                                        <label for="">คุณสมบัติ </label>
                                        <textarea 
                                            class="form-control form-textarea summernote" 
                                            name="qualification" 
                                            cols="30" 
                                            rows="4" 
                                            required>{{old("qualification",$data->qualification ?? "")}}</textarea>
                                    </div> 
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-12">
                                        <label for="">คุณสมบัติอื่นๆ </label>
                                        <textarea 
                                            class="form-control form-textarea summernote" 
                                            name="other_qualification" 
                                            cols="30" 
                                            rows="4">{{old("other_qualification",$data->other_qualification ?? "")}}</textarea>
                                    </div> 
                                </div> 


                                <div class="form-group row">
                                    <div class="col-xl-6">
                                        <label for="">ค่าจ้าง </label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="salary"
                                            placeholder="ค่าจ้าง"
                                            required  
                                            value="{{old("salary",$data->salary ?? "")}}">
                                    </div> 
                                    <div class="col-xl-6">
                                        <label for="">สถานที่ทำงาน </label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="workplace"
                                            placeholder="สถานที่ทำงาน"
                                            required  
                                            value="{{old("workplace",$data->workplace ?? "")}}">
                                    </div> 
                                </div> 
                                <div class="form-group row">
                                    <div class="col-xl-6">
                                        <label for="">วันที่สิ้นสุด </label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="end_date"
                                            placeholder="วันที่สิ้นสุด"
                                            required  
                                            value="{{old("end_date",Helper::convertToDateTh($data->end_date ?? ""))}}">
                                    </div> 
                                    <div class="col-xl-6">
                                        <label for="">ชื่อผู้ติดต่อ</label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="contact_name"
                                            placeholder="ชื่อผู้ติดต่อ"
                                            required  
                                            value="{{old("contact_name",$data->contact_name ?? "")}}">
                                    </div> 
                                </div> 
                                <div class="form-group row">
                                    <div class="col-xl-6">
                                        <label for="">เบอร์โทรศัพท์หน่วยงาน </label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="tel"
                                            placeholder="เบอร์โทรศัพท์"
                                            required  
                                            value="{{old("tel",$data->tel ?? "")}}">
                                    </div> 
                                    <div class="col-xl-6">
                                        <label for="">เบอร์โทรศัพท์มือถือ </label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="mobile"
                                            placeholder="เบอร์โทรศัพท์"
                                            required  
                                            value="{{old("mobile",$data->mobile ?? "")}}">
                                    </div> 
                                </div> 
                                
                                <div class="form-group row">
                                    <div class="col-xl-6">
                                        <label for="">อีเมล </label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="email"
                                            placeholder="อีเมล"
                                            required  
                                            value="{{old("email",$data->email ?? "")}}">
                                    </div> 
                                    <div class="col-xl-6">
                                        <label for="">LineID</label>
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="line_id"
                                            placeholder="LineID"
                                            value="{{old("line_id",$data->line_id ?? "")}}">
                                    </div> 
                                </div> 

                                <div class="form-group">
                                    <label for="">รูปการรับสมัคร</label>
                                    @include("inputs.fileupload_img",[
                                        "name" => "files",
                                        "required" => is_null($data->image ?? null) ,
                                        "image" => $data->image ?? null
                                    ])
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
    <script src="{{ URL::asset("js/recruitment/form.js?t=".time()) }}"></script>
    {{-- input fileupload --}}
    <script src="{{ URL::asset("js/inputs/fileupload_img.js?t=".time()) }}"></script>
@endsection
