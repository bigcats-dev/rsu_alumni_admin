@extends("layouts.main")
@section("css") 
@endsection
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>หนังสือรุ่น</strong>
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
                                @if (Request::route()->getName() == "year-book.create")
                                    action="{{route("year-book.store")}}"
                                @else
                                    action="{{route("year-book.update",["book" => $data->yearbook_id])}}"
                                @endif
                                method="POST" id="frmSave" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">ชื่อหนังสือรุ่น</label>
                                    </div> 
                                    <div class="col-xl-10 col-12"> 
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="name"
                                            required
                                            placeholder="ชื่อหนังสือรุ่น"
                                            value="{{old("name",$data->name ?? "")}}">
                                    </div>
                                </div> 

                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">Hyperlink</label>
                                    </div> 
                                    <div class="col-xl-10 col-12"> 
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="hyperlink"
                                            required
                                            placeholder="(http,https://example.com)"
                                            data-msg-url="รูปแบบ URL ไม่ถูกต้อง"
                                            value="{{old("hyperlink",$data->hyperlink ?? "")}}">
                                    </div>
                                </div> 

                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">ปีการศึกษา</label>
                                    </div> 
                                    <div class="col-xl-10 col-12"> 
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="year"
                                            required
                                            data-msg-integer="กรุณากรอกข้อมูลที่เป็นตัวเลข"
                                            data-rule-integer="true"
                                            placeholder="ตัวเลข"
                                            value="{{old("year",$data->year ?? "")}}">
                                    </div>
                                </div> 

                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">คณะ</label>
                                    </div> 
                                    <div class="col-xl-10 col-12"> 
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
                                </div> 

                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">สาขาวิชา</label>
                                    </div> 
                                    <div class="col-xl-10 col-12"> 
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
                                    <div class="col-xl-2 col-12">
                                        <label for="">รายละเอียด</label>
                                    </div> 
                                    <div class="col-xl-10 col-12"> 
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
                                        <label for="">ไฟล์</label>
                                    </div>
                                    <div class="col-xl-10 col-12">
                                        @include("inputs.fileupload_pdf",[
                                            "name" => "files",
                                            "required" => is_null($data->pdf ?? null) ,
                                            "pdf" => $data->pdf ?? null
                                        ])
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">รูปหนังสือ</label>
                                    </div>
                                    <div class="col-xl-10 col-12">
                                        @include("inputs.fileupload_img",[
                                            "name" => "images",
                                            "required" => is_null($data->image ?? null) ,
                                            "image" => $data->image ?? null,
                                            "multiple" => false,
                                            "width" => 341,
                                            "height" => 180,
                                        ])
                                    </div>
                                </div>
                                <div class="col-12 p-0">
                                    <hr>
                                </div>

                                <div class="form-group row">
                                    @if (Request::route()->getName() == "year-book.create")
                                        @can("create-year-book")
                                            <div class="col-xl-6 col-12">
                                                <button type="submit" class="btn btn-success btn-block" data-loading-text="กรุณารอซักครู่...">บันทึก</button>
                                            </div>
                                        @endcan
                                    @else
                                        @can("update-year-book")
                                            <div class="col-xl-6 col-12">
                                                <button type="submit" class="btn btn-success btn-block" data-loading-text="กรุณารอซักครู่...">บันทึก</button>
                                            </div>
                                        @endcan
                                    @endif
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
        id="jsFrmYearBook"
        data-major='@json($ms_major)'
        src="{{ URL::asset("js/year-book/form.js?t=".time()) }}"></script>
    {{-- input fileupload --}}
    <script src="{{ URL::asset("js/inputs/fileupload_img.js?t=".time()) }}"></script>
    <script src="{{ URL::asset("js/inputs/fileupload_pdf.js?t=".time()) }}"></script>
    @if (Request::route()->getName() == "year-book.view")
        @cannot("update-year-book")
            <script>
                $(function(){
                    $('form').find('input,select,textarea').prop('disabled',true)
                    $('form').find('textarea.summernote').each(function(){
                        $(this).summernote("disable")
                    })
                })
            </script>
        @endcannot
    @endif
@endsection