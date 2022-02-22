@extends("layouts.main")
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>เพิ่ม Social Account</strong>
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
                                @if (Request::route()->getName() == "social.create")
                                    action="{{route("social.store")}}"
                                @else
                                    action="{{route("social.update",["social" => $data->social_id])}}"
                                @endif
                                method="POST" id="frmSave" enctype="multipart/form-data" class="p-4">
                                @csrf
                                <div class="form-group row">                                
                                    <div class="col-xl-2 col-12">
                                        <label for=""><h5>ชื่อ</h5></label>
                                    </div>
                                    <div class="col-xl-8 col-12">
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="name" 
                                            required
                                            value="{{old("name",$data->name ?? "")}}"
                                            placeholder="ชื่อ">
                                    </div>
                                </div>
                                <div class="form-group row">                                
                                    <div class="col-xl-2 col-12">
                                        <label for=""><h5>Hyperlink</h5></label>
                                    </div>
                                    <div class="col-xl-8 col-12">
                                        <input 
                                            type="text" 
                                            class="form-control form-control-border" 
                                            name="hyperlink" 
                                            required
                                            value="{{old("hyperlink",$data->hyperlink ?? "")}}"
                                            placeholder="https://example.com">
                                        <small>ตัวอย่าง https://example.com</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">ไอคอน</label>
                                    </div>
                                    <div class="col-xl-8 col-12">
                                        @include("inputs.fileupload_img",[
                                            "name" => "images",
                                            "required" => is_null($data->icon ?? null) ,
                                            "image" => $data->icon ?? null
                                        ])
                                    </div>
                                </div> 
                                <div class="col-10 p-0"> 
                                    <hr>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-5 col-12">
                                        @canany(['create-social-account', 'update-social-account'])
                                            <button type="submit" class="btn btn-success btn-block" data-loading-text="กรุณารอซักครู่...">บันทึก</button>
                                        @endcanany
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
@endsection
@section("script")
    <script src="{{ URL::asset("js/social/form.js?t=".time()) }}"></script>
    {{-- input fileupload --}}
    <script src="{{ URL::asset("js/inputs/fileupload_img.js?t=".time()) }}"></script>
@endsection
