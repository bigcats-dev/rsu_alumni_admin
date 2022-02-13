@extends("layouts.main")
@section("css")
<link rel="stylesheet" href="{{ URL::asset('plugins/summernote/summernote-bs4.min.css') }}" />
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
                                @if (Request::route()->getName() == "event-news.create")
                                    action="{{route("event-news.store")}}"
                                @else
                                    action="{{route("event-news.update",["activity" => $data->activity_id])}}"
                                @endif
                                method="POST" id="frmSave" enctype="multipart/form-data" class="p-4">
                                @csrf
                                <div class="form-group row">                                
                                    <div class="col-xl-3 col-12">
                                        <label for=""><h5>ชื่อกิจกรรม</h5></label>
                                    </div>
                                    <div class="col-xl-7 col-12">
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="title" 
                                            required
                                            value="{{old("title",$data->title ?? "")}}"
                                            placeholder="ชื่อกิจกรรม">
                                    </div>                            
                                </div> 
                                <div class="form-group row">                                
                                    <div class="col-xl-3 col-12">
                                        <label for=""><h5>วัตถุประสงค์</h5></label>
                                    </div>
                                    <div class="col-xl-7 col-12">
                                        <textarea 
                                            class="form-control form-textarea summernote" 
                                            name="objective" 
                                            cols="30" 
                                            rows="3"
                                            required>{{old("objective",$data->objective ?? "")}}</textarea>
                                    </div>                            
                                </div> 
                                <div class="form-group row">                                
                                    <div class="col-xl-3 col-12">
                                        <label for=""><h5>ลักษณะการดําเนินงาน</h5></label>
                                    </div>
                                    <div class="col-xl-7 col-12">
                                        <textarea 
                                            class="form-control form-textarea summernote" 
                                            name="nature_of_operation" 
                                            cols="30" 
                                            rows="3"
                                            required>{{old("nature_of_operation",$data->nature_of_operation ?? "")}}</textarea>
                                    </div>                            
                                </div> 
                                <div class="form-group row">                                
                                    <div class="col-xl-3 col-12">
                                        <label for=""><h5>คุณสมบัติผู้เข้าร่วม</h5></label>
                                    </div>
                                    <div class="col-xl-7 col-12">
                                        <textarea 
                                            class="form-control form-textarea summernote" 
                                            name="participant_properties" 
                                            cols="30" 
                                            rows="3"
                                            required>{{old("participant_properties",$data->participant_properties ?? "")}}</textarea>
                                    </div>                            
                                </div> 
                                
                                <div class="form-group row">                                
                                    <div class="col-xl-3 col-12">
                                        <label for=""><h5>สถานที่</h5></label>
                                    </div>
                                    <div class="col-xl-7 col-12">
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="location"
                                            value="{{old("location",$data->location ?? "")}}"
                                            placeholder="สถานที่"
                                            required>    
                                    </div>                            
                                </div> 
                                
                                <div class="form-group row">                                
                                    <div class="col-xl-3 col-12">
                                        <label for=""><h5>กำหนดการ</h5></label>
                                        <button type="button" class="btn btn-info btn-flat btn-sm pt-2" id="btn-add-schedule">
                                            <i class="fas fa-plus"></i> สร้างวันจัดกิจกรรม
                                        </button>
                                    </div>
                                    <div class="col-xl-7 col-12" id="schedules-panel">
                                        @if (isset($data) && count($data->activity_schedules) > 0)
                                            @foreach ($data->activity_schedules as $i => $date)
                                                <div class="row col-xl-12 col-12 schedule-box" id="schedule_date_{{ $i }}" data-index="{{ $i }}">
                                                    <div class="row col-12">
                                                        @if ($i > 0)
                                                            <div class="col-12 p-0">
                                                                <hr>
                                                            </div>
                                                        @endif
                                                        <div class="col-xl-1 col-12">
                                                            <label for=""><h5>วันที่ {{ $i + 1 }}</h5></label>
                                                        </div>     
                                                        <div class="col-xl-10 col-12">
                                                            <input 
                                                                type="text" 
                                                                class="form-control form-control-border input-date" 
                                                                name="schedule[{{$i}}][date]" 
                                                                data-msg-required="กรุณาเลือกวันที่" 
                                                                placeholder="ว/ด/ป"
                                                                required
                                                                autocomplete="off"
                                                                value="{{ Helper::convertToDateTh($date->schedule_date) }}"/>
                                                        </div>
                                                        @if ($i > 0)
                                                            <div class="col-xl-1 col-12">
                                                                <button type="button" class="btn btn-danger btn-flat btn-sm pt-2 pa-r remove-schedule">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="row">  
                                                        <div class="col-xl-12 col-12 mt-2 mb-2"> 
                                                            <button type="button" class="btn btn-info btn-flat btn-sm pt-2 btn-add-time" data-target="{0}">
                                                                <i class="fas fa-plus"></i> เพิ่มกำหนดการเวลา
                                                            </button>
                                                        </div> 
                                                    </div>
                                                    <div class="col-12 schedule-time">
                                                        @if ($date->activity_schedule_details)
                                                            @foreach ($date->activity_schedule_details as $j => $time)
                                                                <div class="row" id="schedule_time_{{ $i }}_{{ $j }}" data-index="{{$j}}"> 
                                                                    <div class="col-xl-1 col-12">
                                                                        <label for=""><h5>เวลา</h5></label>
                                                                    </div>     
                                                                    <div class="col-xl-2 col-12">
                                                                        <input 
                                                                            type="text" 
                                                                            class="form-control form-control-border timepicker time-start" 
                                                                            name="schedule[{{$i}}][time][{{$j}}][time_start]"
                                                                            required
                                                                            value="{{ $time->time_start }}">    
                                                                    </div>     
                                                                    <div class="col-xl-2 col-12">
                                                                        <input 
                                                                            type="text" 
                                                                            class="form-control form-control-border timepicker time-end" 
                                                                            name="schedule[{{$i}}][time][{{$j}}][time_end]"
                                                                            required
                                                                            value="{{ $time->time_end }}">    
                                                                    </div>
                                                                    <div class="col-xl-6 col-12">
                                                                        <input 
                                                                            type="text" 
                                                                            class="form-control form-control-border" 
                                                                            name="schedule[{{$i}}][time][{{$j}}][detail]" 
                                                                            required
                                                                            placeholder="รายละเอียด"
                                                                            value="{{ $time->detail }}">    
                                                                    </div>
                                                                    @if ($j > 0)    
                                                                        <div class="col-xl-1 col-12">
                                                                            <button type="button" class="btn btn-danger btn-flat btn-sm pt-2 remove-time">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div> 
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-xl-12 col-12 schedule-box" id="schedule_date_0" data-index="0">
                                                <div class="row">
                                                    <div class="col-xl-1 col-12">
                                                        <label for=""><h5>วันที่ 1</h5></label>
                                                    </div>     
                                                    <div class="col-xl-10 col-12">
                                                        <input 
                                                            type="text" 
                                                            class="form-control form-control-border input-date" 
                                                            name="schedule[0][date]" 
                                                            data-msg-required="กรุณาเลือกวันที่" 
                                                            placeholder="ว/ด/ป"
                                                            autocomplete="off"
                                                            required />
                                                    </div>
                                                </div>
                                    
                                                <div class="row">  
                                                    <div class="col-xl-12 col-12 mt-2 mb-2"> 
                                                        <button type="button" class="btn btn-info btn-flat btn-sm pt-2 btn-add-time" data-target="0">
                                                            <i class="fas fa-plus"></i> เพิ่มกำหนดการเวลา
                                                        </button>
                                                    </div> 
                                                </div>
                                                <div class="col-12 schedule-time">
                                                    <div class="row" id="schedule_time_0_0" data-index="0"> 
                                                        <div class="col-xl-1 col-12">
                                                            <label for=""><h5>เวลา</h5></label>
                                                        </div>     
                                                        <div class="col-xl-2 col-12">
                                                            <input 
                                                            type="text" 
                                                            class="form-control form-control-border timepicker time-start" 
                                                            name="schedule[0][time][0][time_start]"
                                                            required>    
                                                        </div>     
                                                        <div class="col-xl-2 col-12">
                                                            <input 
                                                                type="text" 
                                                                class="form-control form-control-border timepicker time-end" 
                                                                name="schedule[0][time][0][time_end]"
                                                                required>    
                                                        </div>
                                                        <div class="col-xl-6 col-12">
                                                            <input 
                                                                type="text" 
                                                                class="form-control form-control-border" 
                                                                name="schedule[0][time][0][detail]" 
                                                                required
                                                                placeholder="รายละเอียด">    
                                                        </div>  
                                                    </div>
                                                </div>
                                            </div> 
                                        @endif
                                    </div>
                                </div> 
                                <div class="col-10 p-0"> 
                                    <hr>
                                </div>
                                <div class="form-group row">                                
                                    <div class="col-xl-3 col-12">
                                        <label for=""><h5>จำนวนผู้เข้าร่วมสูงสุด</h5></label>
                                    </div>
                                    <div class="col-xl-3 col-12">
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="max_participants"
                                            value="{{old("max_participants",$data->max_participants ?? "")}}"
                                            data-rule-digits="true"
                                            placeholder="จำนวนผู้เข้าร่วมสูงสุด">    
                                    </div>        
                                    <div class="col-xl-3">
                                        <input
                                            type="checkbox" 
                                            id="customCheckbox1" 
                                            value="1" 
                                            name="unlimited_participants"
                                            {{ old("unlimited_participants",$data->unlimited_participants ?? "") == "1"
                                                ? "checked"
                                                : "" }}>
                                        <label for="customCheckbox1"> ไม่จำกัดจำนวนผู้เข้าร่วม</label>
                                    </div>                      
                                </div> 
                                <div class="form-group row">                                
                                    <div class="col-xl-3 col-12">
                                        <label for=""><h5>ค่าใช้จ่าย</h5></label>
                                    </div>
                                    <div class="col-xl-3 col-12">
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="expenses"
                                            data-rule-number="true"
                                            value="{{old("expenses",$data->expenses ?? "")}}"
                                            placeholder="ค่าใช้จ่าย">
                                    </div>     
                                    <div class="col-xl-3">
                                        <input
                                            type="checkbox" 
                                            id="customCheckbox2" 
                                            value="1" 
                                            name="free_activities"
                                            {{ old("free_activities",$data->free_activities ?? "") == "1"
                                                ? "checked"
                                                : "" }}>
                                        <label for="customCheckbox2"> กิจกรรมไม่มีค่าใช้จ่าย</label>
                                    </div>                  
                                </div> 
                                <div class="form-group row">                                
                                    <div class="col-xl-3 col-12">
                                        <label for=""><h5>ผู้ดูแลรับผิดชอบ</h5></label>
                                        <button type="button" class="btn btn-info btn-flat btn-sm pt-2" id="btn-add-officer">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="col-xl-7 col-12" id="officers-panel">
                                        @php
                                            $officers = old("officers",$data->officers ?? [])
                                        @endphp
                                        @foreach ($officers as $key => $item)
                                            <div class="row" id="officers_{{$key}}" data-index="{{$key}}"> 
                                                <div class="col-xl-3 col-12"> 
                                                    <input 
                                                        type="text" 
                                                        class="form-control form-control-border" 
                                                        name="officers[{{$key}}][name]" 
                                                        placeholder="ชื่อ - นามสกุล"
                                                        required
                                                        value="{{$item["name"]}}">    
                                                </div>    
                                                <div class="col-xl-3 col-12">
                                                    <input 
                                                        type="text" 
                                                        class="form-control form-control-border" 
                                                        name="officers[{{$key}}][email]"
                                                        placeholder="อีเมล"
                                                        required
                                                        value="{{$item["email"]}}">    
                                                </div>   
                                                <div class="col-xl-2 col-12">
                                                    <input 
                                                        type="text" 
                                                        class="form-control form-control-border" 
                                                        name="officers[{{$key}}][tel]"
                                                        placeholder="เบอร์โทรศัพท์"
                                                        required
                                                        value="{{$item["tel"]}}">    
                                                </div>    
                                                <div class="col-xl-1 col-12">
                                                    <button type="button" class="btn btn-danger btn-flat btn-sm pt-2" onclick="$(this).parent().parent().remove()">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>       
                                </div> 
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">รูปข่าวสาร</label>
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
                                        <button type="button" class="btn btn-secondary btn-block" onclick="window.history.back()">ยกเลิก</button>
                                    </div>
                                </div>   
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </section>

    <textarea id="schedule-date-template" style="display: none">
        <div class="col-xl-12 col-12 schedule-box" id="schedule_date_{0}" data-index="{0}">
            <div class="row">
                <div class="col-12 p-0">
                    <hr>
                </div>
                <div class="col-xl-1 col-12">
                    <label for=""><h5>วันที่ {1}</h5></label>
                </div>     
                <div class="col-xl-10 col-12">
                    <input 
                        type="text" 
                        class="form-control form-control-border input-date" 
                        name="schedule[{0}][date]" 
                        data-msg-required="กรุณาเลือกวันที่"
                        autocomplete="off"
                        placeholder="ว/ด/ป"
                        required />
                </div>
                <div class="col-xl-1 col-12">
                    <button type="button" class="btn btn-danger btn-flat btn-sm pt-2 pa-r remove-schedule">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="row">  
                <div class="col-xl-12 col-12 mt-2 mb-2"> 
                    <button type="button" class="btn btn-info btn-flat btn-sm pt-2 btn-add-time" data-target="{0}">
                        <i class="fas fa-plus"></i> เพิ่มกำหนดการเวลา
                    </button>
                </div> 
            </div>
            <div class="col-12 schedule-time">
                <div class="row" id="schedule_time_{0}_0" data-index="0"> 
                    <div class="col-xl-1 col-12">
                        <label for=""><h5>เวลา</h5></label>
                    </div>     
                    <div class="col-xl-2 col-12">
                        <input 
                        type="text" 
                        class="form-control form-control-border timepicker time-start" 
                        name="schedule[{0}][time][0][time_start]"
                        required>    
                    </div>     
                    <div class="col-xl-2 col-12">
                        <input 
                            type="text" 
                            class="form-control form-control-border timepicker time-end" 
                            name="schedule[{0}][time][0][time_end]"
                            required>    
                    </div>
                    <div class="col-xl-6 col-12">
                        <input 
                            type="text" 
                            class="form-control form-control-border" 
                            name="schedule[{0}][time][0][detail]" 
                            required
                            placeholder="รายละเอียด">    
                    </div>
                </div>
            </div> 
        </div> 
    </textarea>

    <textarea id="schedule-time-template" style="display: none">
        <div class="row" id="schedule_time_{0}_{1}" data-index="{1}"> 
            <div class="col-xl-1 col-12">
                <label for=""><h5>เวลา</h5></label>
            </div>     
            <div class="col-xl-2 col-12">
                <input 
                    type="text" 
                    class="form-control form-control-border timepicker time-start" 
                    name="schedule[{0}][time][{1}][time_start]"
                    required>    
            </div>     
            <div class="col-xl-2 col-12">
                <input 
                    type="text" 
                    class="form-control form-control-border timepicker time-end" 
                    name="schedule[{0}][time][{1}][time_end]"
                    required>    
            </div>
            <div class="col-xl-6 col-12">
                <input 
                    type="text" 
                    class="form-control form-control-border" 
                    name="schedule[{0}][time][{1}][detail]" 
                    required
                    placeholder="รายละเอียด">    
            </div>         
            <div class="col-xl-1 col-12">
                <button type="button" class="btn btn-danger btn-flat btn-sm pt-2 remove-time">
                    <i class="fas fa-times"></i>
                </button>
            </div>     
        </div>
    </textarea>

    <textarea id="officer-template" style="display: none">
        <div class="row" id="officers_{0}" data-index="{0}"> 
            <div class="col-xl-3 col-12"> 
                <input 
                    type="text" 
                    class="form-control form-control-border" 
                    name="officers[{0}][name]" 
                    placeholder="ชื่อ - นามสกุล"
                    required>    
            </div>    
            <div class="col-xl-3 col-12">
                <input 
                    type="text" 
                    class="form-control form-control-border" 
                    name="officers[{0}][email]"
                    placeholder="อีเมล"
                    required>    
            </div>   
            <div class="col-xl-2 col-12">
                <input 
                    type="text" 
                    class="form-control form-control-border" 
                    name="officers[{0}][tel]"
                    placeholder="เบอร์โทรศัพท์"
                    required>    
            </div>    
            <div class="col-xl-1 col-12">
                <button type="button" class="btn btn-danger btn-flat btn-sm pt-2" onclick="$(this).parent().parent().remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </textarea>
@endsection
@section("script")
    <script src="{{ URL::asset("js/event-news/form.js?t=".time()) }}"></script>
    {{-- input fileupload --}}
    <script src="{{ URL::asset("js/inputs/fileupload_img.js?t=".time()) }}"></script>
@endsection