@extends("layouts.main")
@section("content")
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>จัดการ Social Account </strong>
                        @can("create-social-account")
                            <a href="{{route("social.create")}}" class="btn  btn-success"  ><i class="far fa-plus"></i>  เพิ่ม Social Account </a> 
                        @endcan
                    </h1> 
                </div>  
            </div> 
        </div> 
    </div>  
    <section class="content">
        <div class="container-fluid">
            <div class="col">
                <div class="row ">
                    @if (sizeof($data) > 0)
                        @foreach ($data as $i)
                            <div class="col-lg-3 social-box">
                                <div class="div-box card-outline card-primary p-4">
                                    <h5>
                                        <strong>ชื่อ : {{$i->name}}</strong>
                                        @can("del-social-account")
                                            <button type="button" class="btn btn-danger float-right  btn-sm btn-flat delete" data-id="{{$i->social_id}}">
                                                <i class="far fa-trash-alt"></i> ลบ
                                            </button> 
                                            <form style="display: none" method="POST" id="frmDestroy_{{$i->social_id}}" action="{{route("social.destroy",["social" => $i->social_id])}}">
                                                @csrf
                                            </form>
                                        @endcan
                                    </h5>
                                    <h5 class="detail">
                                        <strong>Link : </strong><a href="{{$i->hyperlink}}" target="_blank">{{$i->hyperlink}}</a>
                                    </h5>
                                    <div class="col">
                                        <div class="row gallery">
                                            <div class="col-5">
                                                <div class="box-gall" style="background: url('{{asset("storage/" . $i->icon_path)}}');"></div>
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group row">
                                            <div class="col">
                                                <select class="form-control form-control-sm priority">
                                                    @for ($j = 1;$j <= sizeof($data);$j++)
                                                        <option
                                                            data-action-priority="{{route("social.priority",["social" => $i->social_id,"priority" => $j])}}" 
                                                            value="{{$j}}" {{$i->priority == $j ? "selected" : ""}} >{{$j}}</option>
                                                    @endfor
                                                </select>  
                                            </div> 
                                            <div class="col">
                                                @can("update-social-account")
                                                    <a href="{{route("social.view",["social" => $i->social_id])}}" class="btn btn-secondary btn-sm btn-flat">
                                                        <i class="fas fa-eye"></i> ดูรายละเอียด
                                                    </a>
                                                @endcan
                                            </div>
                                            <div class="col">
                                                <div class="custom-control custom-switch">
                                                    <input 
                                                        type="checkbox" 
                                                        class="custom-control-input active" 
                                                        id="active_{{$i->social_id}}" 
                                                        data-action="{{route("social.active",["social" => $i->social_id])}}"
                                                        {{$i->active == 1
                                                            ? "checked"
                                                            : ""}}>
                                                    <label class="custom-control-label" for="active_{{$i->social_id}}">การใช้งาน</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="col-12">
                                            <h5><strong>ผู้สร้าง</strong> : {{$i->user_create->fullname}}</h5>
                                        </div>
                                        <div class="col-12">
                                            <h5><strong>วันที่สร้าง</strong> : {{Helper::convertToDateTimeYTh($i->created_at)}}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
@section("script")
    <script src="{{ URL::asset("js/social/index.js?t=".time()) }}"></script>
@endsection