@extends("layouts.main")
@section("css")
<link rel="stylesheet" href="{{ URL::asset('plugins/dropzone/min/dropzone.min.css') }}" />
@endsection
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>สร้างอัลบั้มแกลเลอรี</strong>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="div-box card-outline card-info p-4">
                <ul class="nav nav-tabs" id="management_tab" role="tablist">
                    <li class="nav-item">
                        <a 
                            class="nav-link 
                                {{(in_array(Request::route()->getName(),["album.create","album.view","album.edit"]) && !Session::has("success") 
                                    ? 'active'
                                    : '')}}"
                            id="album_tab" 
                            data-toggle="pill" 
                            href="#album_content" 
                            role="tab" 
                            aria-controls="album_tab"
                            aria-selected="true"><i class="fas fa-cube"></i> ข้อมูลอัลบั้ม</a>
                    </li>
                    <li class="nav-item">
                        <a 
                            class="nav-link
                                {{(in_array(Request::route()->getName(),["album.view","album.edit"]) 
                                    ? (Session::has('success')
                                        ? 'active'
                                        : '')
                                    : 'disabled')}}" 
                            id="gallert_tab" 
                            data-toggle="pill" 
                            href="#gallery_content" 
                            role="tab" 
                            aria-controls="gallert_tab"
                            aria-selected="true"><i class="fas fa-image"></i> แกลเลอรี</a>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div 
                            class="tab-pane fade
                                {{(in_array(Request::route()->getName(),["album.create","album.view","album.edit"]) && !Session::has("success") 
                                        ? 'active show'
                                        : '')}}" 
                            id="album_content" 
                            role="tabpanel" 
                            aria-labelledby="album_content">
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
                                @if (Request::route()->getName() == "album.create")
                                    action="{{route("album.store")}}"
                                @else
                                    action="{{route("album.update",["album" => $data->album_id])}}"
                                @endif
                                method="POST" id="frmSave" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">ชื่ออัลบั้ม</label>
                                    </div>
                                    <div class="col-xl-8 col-12">
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="title"
                                            placeholder="ชื่ออัลบั้ม"
                                            required
                                            value="{{old("title",$data->title ?? "")}}">
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
                                            rows="6"
                                            required>{{old("detail",$data->detail ?? "")}}</textarea>
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
                        <div 
                            class="tab-pane fade 
                                {{(in_array(Request::route()->getName(),["album.view","album.edit"]) 
                                    ? (Session::has('success')
                                        ? 'active show'
                                        : '')
                                    : '')}}" 
                            id="gallery_content" 
                            role="tabpanel" 
                            aria-labelledby="gallery_content">
                            <div class="card card-default">
                                <div class="card-header">
                                  <h4><strong>อัพโหลดไฟล์</strong> <small class="text-danger">เฉพาะไฟล์ที่มีนามสกุล jpg,jepg และ png</small></h4>
                                </div>
                                <div class="card-body">
                                    <div id="actions" class="row">
                                        <div class="col-lg-5">
                                        <div class="btn-group w-100">
                                            <span class="btn btn-success col fileinput-button">
                                                <i class="fas fa-plus"></i>
                                                <span>เลือกไฟล์</span>
                                            </span>
                                            <button type="submit" class="btn btn-primary col start" data-loading-text="กรุณารอซักครู่...">
                                                <i class="fas fa-upload"></i>
                                                <span>อัพโหลด</span>
                                            </button>
                                            <button type="reset" class="btn btn-warning col cancel">
                                                <i class="fas fa-times-circle"></i>
                                                <span>ยกเลิก</span>
                                            </button>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="table table-striped files" id="previews">
                                        <div id="template" class="row mt-2">
                                            <div class="col-auto">
                                                <span class="preview"><img class="img-thumbnail" src="data:," alt="" data-dz-thumbnail /></span>
                                            </div>
                                            <div class="col d-flex align-items-center">
                                                <p class="mb-0">
                                                    <span class="lead" data-dz-name></span>
                                                    (<span data-dz-size></span>)
                                                </p>
                                                <strong class="error text-danger" data-dz-errormessage></strong>
                                            </div>
                                            <div class="col-4 d-flex align-items-center">
                                                <div class="progress progress-striped active w-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                                    <div class="bg-success .progress-bar-animated progress-bar progress-bar-success progress-bar-striped" style="width:0%;" data-dz-uploadprogress>
                                                        <span class="progress-text"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-auto d-flex align-items-center">
                                                <div class="btn-group">
                                                <button class="btn btn-primary start" data-loading-text="กรุณารอซักครู่...">
                                                    <i class="fas fa-upload"></i>
                                                    <span>อัพโหลด</span>
                                                </button> 
                                                <button data-dz-remove class="btn btn-danger delete">
                                                    <i class="fas fa-trash"></i>
                                                    <span>ลบ</span>
                                                </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                </div>
                            </div>
                            <div class="card card-default">
                                <div class="card-header">
                                  <h4><strong>จัดการไฟล์</strong></h4>
                                </div>
                                <div class="card-body">
                                    <div class="overlay-wrapper">
                                        <div class="overlay" id="tab-overlay" style="display: none;">
                                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                            <div class="text-bold pt-2">Loading...</div>
                                        </div>
                                        <div class="row" id="panel-images">
                                            @isset($data->gallerys)
                                                @if (sizeof($data->gallerys) > 0)
                                                    @foreach ($data->gallerys as $i)
                                                        <div class="col-1">
                                                            <div class="bg-gal img-thumbnail" style="background: url({{asset("storage/".$i->file_path)}});">
                                                                <a href="javascript:;" class="delete" data-url="{{route("album.gallery.destroy",["gallery" => $i->gallery_id])}}">
                                                                    <i class="far fa-times-circle"></i>
                                                                </a>
                                                            </div>
                                                            <div class="form-check">
                                                                <input 
                                                                    type="radio" 
                                                                    id="gallert_{{$i->gallery_id}}" 
                                                                    name="cover_page" 
                                                                    data-url="{{route("album.gallery.cover_page",["gallery" => $i->gallery_id])}}"
                                                                    {{ $i->cover_page == "Y"
                                                                        ? "checked"
                                                                        : "" }}/>
                                                                <label class="form-check-label" for="gallert_{{$i->gallery_id}}">
                                                                    แสดงหน้าปก
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endisset
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <textarea id="container-image-template" style="display: none">
        <div class="col-1">
            <div class="bg-gal img-thumbnail" style="background: url({0});">
                <a href="javascript:;" class="delete" data-url="{1}">
                    <i class="far fa-times-circle"></i>
                </a>
            </div>
            <div class="form-check">
                <input type="radio" id="gallert_{2}" name="first_image" value="{2}"/>
                <label class="form-check-label" for="gallert_{2}">
                    แสดงหน้าปก
                </label>
            </div>
        </div>
    </textarea>
@endsection
@section("script")
    <script src="{{ URL::asset("plugins/dropzone/min/dropzone.min.js") }} "></script>
    <script src="{{ URL::asset("js/jquery.magnific-popup.js") }}"></script>
    <script src="{{ URL::asset("js/album/form.js?t=".time()) }}"></script>
    {{-- input fileupload --}}
    <script 
        id="jsUpload" 
        data-upload-url="{{route("album.gallery.upload",["album" => $data->album_id ?? 0])}}" 
        src="{{ URL::asset("js/album/upload.js?t=".time()) }}"></script>

@endsection