@extends("layouts.main")
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>ข้อมูลสถานที่จัดกิจกรรม</strong>
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
                                action="{{route("event-news.book-store",["activity" => $activity->activity_id])}}" 
                                method="POST" id="frmSave" enctype="multipart/form-data" class="p-4">
                                @csrf
                                <div class="form-group place-book">
                                    <div class="form-group row">
                                        <label class="col-lg-3 text-right" for="place">สถานที่ <span
                                                class="sp-required">*</span></label>
                                        <div class="col-lg-2">
                                            <div class="form-check-inline">
                                                <label class="form-check-label">
                                                    <input 
                                                        data-msg-required="กรุณาเลือกสถานที่" 
                                                        type="radio"
                                                        class="form-check-input" 
                                                        name="type" 
                                                        value="1"
                                                        @isset($activity)
                                                            {{ $activity->activity_room ? (old("type",$activity->activity_room->type) == '1' ? 'checked' : '') : 'checked' }}
                                                        @endisset>ภายในมหาวิทยาลัย
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-check-inline">
                                                <label class="form-check-label">
                                                    <input 
                                                        data-msg-required="กรุณาเลือกสถานที่" 
                                                        type="radio"
                                                        class="form-check-input" 
                                                        name="type" 
                                                        value="2"
                                                        @isset($activity)
                                                            {{ $activity->activity_room ? (old("type",$activity->activity_room->type) == '2' ? 'checked' : '') : '' }}
                                                        @endisset>ภายนอกมหาวิทยาลัย
                                                </label>
                                            </div>
                                        </div>
                                    </div>                     
                                    <div class="form-group row place-outside" 
                                        style="display: @isset($activity) {{(old("type",$activity->activity_room->type ?? "") == "2" ? "block" : "none")}} @endisset">
                                        <label class="col-lg-3 text-right" for="place_price">ค่าใช้จ่าย <span class="sp-required">*</span></label>
                                        <div class="col-lg-7">
                                            <input 
                                                data-msg-required="กรุณากรอกค่าใช้จ่าย" 
                                                data-rule-number="true"
                                                placeholder="ค่าใช้จ่าย" 
                                                type="text" 
                                                class="form-control"
                                                id="place_price" 
                                                name="price" 
                                                autocomplete="off" 
                                                @isset($activity)
                                                    value="{{ old("price",$activity->activity_room->price ?? '') }}" 
                                                @endisset />
                                        </div>
                                    </div>
                                    <div class="form-group row place-outside"
                                        style="display: @isset($activity) {{(old("detail",$activity->activity_room->type ?? "") == "2" ? "block" : "none")}} @endisset">
                                        <label class="col-lg-3 text-right" for="place_des">รายละเอียด <span class="sp-required">*</span></label>
                                        <div class="col-lg-7">
                                            <textarea 
                                                data-msg-required="กรุณากรอกรายละเอียด" 
                                                class="form-control"
                                                id="place_des" 
                                                name="detail" 
                                                rows="5" 
                                                placeholder="รายละเอียด"
                                                autocomplete="off">@isset($activity){{ old("detail",$activity->activity_room->detail ?? '') }}@endisset</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row place-inside">
                                        <label class="col-lg-3 text-right" for="place"></label>
                                        <div class="col-lg-7">
                                            <button type="button" class="btn btn-success btn-sm" id="btn-add-roomschedule"><i class="fas fa-plus"></i> เพิ่มการจองห้อง</button>
                                        </div>
                                    </div>
                                    @isset($activity)
                                        @if ($activity->activity_room && $activity->activity_room->type == "1" && sizeof($activity->activity_room_schedule) > 0)
                                            @foreach ($activity->activity_room_schedule as $key => $item)
                                                <div class="form-group room_box place-inside" id="roomBox_{{$key}}" data-index="{{$key}}">
                                                    <hr/>
                                                    <div class="form-group row">
                                                        <input type="hidden" name="room[{{$key}}][id]" value="{{$item->activity_room_schedules_id}}">
                                                        <label class="col-lg-2 text-right">ช่วงวันที่จอง <span class="sp-required">*</span></label>
                                                        <div class="col-lg-7">
                                                            <div class="input-group input-daterange">
                                                                <input 
                                                                    type="text" 
                                                                    name="room[{{$key}}][date_start]" 
                                                                    required 
                                                                    id="date_start_{0}" 
                                                                    class="form-control group input-search" 
                                                                    placeholder="เริ่มต้น" 
                                                                    value="{{ Helper::convertToDateTh($item->date_start) }}">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">ถึง</span>
                                                                </div>
                                                                <input 
                                                                    type="text" 
                                                                    name="room[{{$key}}][date_end]" 
                                                                    required 
                                                                    id="date_end_{0}" 
                                                                    class="form-control group input-search" 
                                                                    placeholder="สิ้นสุด" value="{{ Helper::convertToDateTh($item->date_end) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 text-right" for="room_group_id_{{$key}}">กลุ่มห้อง <span
                                                                class="sp-required">*</span></label>
                                                        <div class="col-lg-7">
                                                            <select class="selectpicker" required data-show-subtext="true" data-live-search="true"
                                                                data-msg-required="จำเป็นต้องเลือก" name="room[{{$key}}][room_group_uid]" id="room_group_id_{{$key}}">
                                                                <option value="">เลือกกลุ่มห้อง</option>
                                                                @if (count($ms_room_group) > 0)
                                                                    @foreach ($ms_room_group as $room_group)
                                                                        <option value="{{ $room_group->room_group_id }}"
                                                                            {{ $item->room_group_uid == $room_group->room_group_id ? 'selected' : ''}}>
                                                                            {{ $room_group->room_group_name_th }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label
                                                            data-loading-text="<div class='spinner-border-input text-dark' role='status'></div> กรุณารอซักครู่..."
                                                            class="col-lg-2 text-right" for="room_sub_group_id_{{$key}}">กลุ่มห้องย่อย <span
                                                                class="sp-required">*</span></label>
                                                        <div class="col-lg-7">
                                                            <select class="selectpicker" required data-show-subtext="true" data-live-search="true"
                                                                data-msg-required="จำเป็นต้องเลือก" name="room[{{$key}}][room_sub_group_uid]"
                                                                id="room_sub_group_id_{{$key}}">
                                                                <option value="">เลือกกลุ่มห้องย่อย</option>
                                                                @isset($ms_room_subgroup)
                                                                    @if (count($ms_room_subgroup) > 0)
                                                                        @foreach ($ms_room_subgroup as $room_subgroup)
                                                                            <option value="{{ $room_subgroup->room_sub_group_id }}"
                                                                                {{ $item->room_sub_group_uid == $room_subgroup->room_sub_group_id ? 'selected' : ''}}>
                                                                                {{ $room_subgroup->room_sub_group_name_th }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                @endisset
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label
                                                            data-loading-text="<div class='spinner-border-input text-dark' role='status'></div> กรุณารอซักครู่..."
                                                            class="col-lg-2 text-right" for="room_id_{{$key}}">ห้อง <span
                                                                class="sp-required">*</span></label>
                                                        <div class="col-lg-7">
                                                            <select class="selectpicker" required data-show-subtext="true" data-live-search="true"
                                                                data-msg-required="จำเป็นต้องเลือก" name="room[{{$key}}][room_uid]" id="room_id_{{$key}}">
                                                                <option value="">เลือกห้อง</option>
                                                                @isset($ms_room)
                                                                    @if (count($ms_room) > 0)
                                                                        @foreach ($ms_room as $room)
                                                                            <option value="{{ $room->room_id }}"
                                                                                {{ $item->room_uid == $room->room_id ? 'selected' : ''}}>
                                                                                เลขห้อง {{$room->room_no}},{{ $room->room_name_th }} , จำนวนที่นั่ง
                                                                                {{ $room->total_seat }} , พื้นที่ {{ $room->area }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                @endisset
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 text-right" for="place_price_{{$key}}">ค่าใช้จ่าย <span
                                                                class="sp-required">*</span></label>
                                                        <div class="col-lg-7">
                                                            <input 
                                                                data-msg-required="กรุณากรอกค่าใช้จ่าย" 
                                                                data-rule-number="true" 
                                                                required
                                                                placeholder="ค่าใช้จ่าย" 
                                                                type="text" 
                                                                class="form-control"
                                                                id="place_price_{{$key}}" 
                                                                name="room[{{$key}}][price]" 
                                                                autocomplete="off" 
                                                                value="{{$item->price}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 text-right" for="description_{{$key}}">รายละเอียด </label>
                                                        <div class="col-lg-7">
                                                            <input 
                                                                type="text" 
                                                                class="form-control" 
                                                                name="room[{{$key}}][description]"
                                                                placeholder="รายละเอียดเพิ่มเติม"
                                                                id="description_{{$key}}" value="{{$item->description}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-lg-9">
                                                            <div class="form-group form-check float-right">
                                                                <input 
                                                                    type="checkbox" 
                                                                    id="room_del_{{$key}}" 
                                                                    name="room[{{$key}}][del]" 
                                                                    value="{{$item->activity_room_schedules_id}}">
                                                                <label class="form-chesck-label" for="room_del_{{$key}}">ลบออก</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endisset
                                </div>
                                <hr id="hr-place" />
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
    {{-- room --}}
    <textarea style="display: none" id="room-tempalte">
        <div class="form-group room_box place-inside" id="roomBox_{0}" data-index="{0}">
            <hr/>
            <div class="form-group row">
                <label class="col-lg-2 text-right" for="room_group_id_{0}">ช่วงวันที่จอง <span class="sp-required">*</span></label>
                <div class="col-lg-7">
                    <div class="input-group input-daterange">
                        <input 
                            type="text" 
                            name="room[{0}][date_start]" 
                            required id="date_start_{0}" 
                            class="form-control input-date" 
                            placeholder="เริ่มต้น"
                            autocomplete="off">
                        <div class="input-group-append">
                            <span class="input-group-text">ถึง</span>
                        </div>
                        <input 
                            type="text" 
                            name="room[{0}][date_end]" 
                            id="date_end_{0}" 
                            class="form-control input-date" 
                            placeholder="สิ้นสุด"
                            autocomplete="off" >
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-2 text-right" for="room_group_id_{0}">กลุ่มห้อง <span
                        class="sp-required">*</span></label>
                <div class="col-lg-7">
                    <select class="selectpicker" data-show-subtext="true" data-live-search="true" required
                        data-msg-required="จำเป็นต้องเลือก" name="room[{0}][room_group_uid]" id="room_group_id_{0}">
                        <option value="">เลือกกลุ่มห้อง</option>
                        @if (count($ms_room_group) > 0)
                            @foreach ($ms_room_group as $room_group)
                                <option value="{{ $room_group->room_group_id }}">{{ $room_group->room_group_name_th }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label
                    data-loading-text="<div class='spinner-border-input text-dark' role='status'></div> กรุณารอซักครู่..."
                    class="col-lg-2 text-right" for="room_sub_group_id_{0}">กลุ่มห้องย่อย <span
                        class="sp-required">*</span></label>
                <div class="col-lg-7">
                    <select class="selectpicker" data-show-subtext="true" data-live-search="true" required
                        data-msg-required="จำเป็นต้องเลือก" name="room[{0}][room_sub_group_uid]"
                        id="room_sub_group_id_{0}">
                        <option value="">เลือกกลุ่มห้องย่อย</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label
                    data-loading-text="<div class='spinner-border-input text-dark' role='status'></div> กรุณารอซักครู่..."
                    class="col-lg-2 text-right" for="room_id_{0}">ห้อง <span
                        class="sp-required">*</span></label>
                <div class="col-lg-7">
                    <select class="selectpicker" data-show-subtext="true" data-live-search="true" required
                        data-msg-required="จำเป็นต้องเลือก" name="room[{0}][room_uid]" id="room_id_{0}">
                        <option value="">เลือกห้อง</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-2 text-right" for="place_price_{0}">ค่าใช้จ่าย <span
                        class="sp-required">*</span></label>
                <div class="col-lg-7">
                    <input 
                        data-msg-required="กรุณากรอกค่าใช้จ่าย" 
                        data-rule-number="true"
                        placeholder="ค่าใช้จ่าย" 
                        type="text" 
                        class="form-control" 
                        required
                        id="place_price_{0}" 
                        name="room[{0}][price]" 
                        autocomplete="off"/>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-2 text-right" for="description_{0}">รายละเอียด </label>
                <div class="col-lg-7">
                    <input 
                        type="text" 
                        placeholder="รายละเอียดเพิ่มเติม" 
                        class="form-control" 
                        name="room[{0}][description]" 
                        id="description_{0}">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-9">
                    <button class="btn btn-sm btn-danger float-right" type="button"><i class="fas fa-times"></i> ลบออก</button>
                </div>
            </div>
        </div>
    </textarea>
@endsection
@section("script")
    <script 
        id="jsPlace"
        data-date-start="{{Helper::convertToDateTh($activity->first_training_date)}}"
        data-date-end="{{Helper::convertToDateTh($activity->last_training_date)}}"
        data-room-group-url="{{ route('service.room.group') }}"
        data-room-subgroup-url="{{ route('service.room.subgroup') }}" 
        data-room-url="{{ route('service.room') }}"
        src="{{ URL::asset("js/event-news/booking.js?t=".time()) }}"></script>
@endsection