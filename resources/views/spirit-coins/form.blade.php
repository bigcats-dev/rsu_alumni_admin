@extends("layouts.main")
@section("css") 
@endsection
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>ร้านค้าสปิริตคอยน์</strong>
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
                                @if (Request::route()->getName() == "spirit-coin.create")
                                    action="{{route("spirit-coin.store")}}"
                                @else
                                    action="{{route("spirit-coin.update",["coin" => $data->spirit_coin_id])}}"
                                @endif 
                                method="POST" id="frmSave" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">ชื่อร้าน</label>
                                    </div> 
                                    <div class="col-xl-10 col-12"> 
                                        <input 
                                        type="text" 
                                        class="form-control form-control-border" 
                                        name="name" 
                                        required
                                        placeholder="ชื่อร้าน"
                                        value="{{old("name",$data->name ?? "")}}">
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
                                            cols="40" 
                                            rows="4" 
                                            required>{{old("detail",$data->detail ?? "")}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-2 col-12">
                                        <label for="">รูปร้านค้าสปิริตคอยน์</label>
                                    </div>
                                    <div class="col-xl-10 col-12">
                                        @include("inputs.fileupload_img",[
                                            "name" => "files",
                                            "required" => is_null($data->image ?? null) ,
                                            "image" => $data->image ?? null
                                        ])
                                    </div>
                                </div> 
                                <div class="col-12 p-0"> 
                                    <hr>
                                </div>
                                <div class="form-group row">
                                    @if (Request::route()->getName() == "spirit-coin.create")
                                        @can("create-spirit-coin")
                                            <div class="col-xl-6 col-12">
                                                <button type="submit" class="btn btn-success btn-block" data-loading-text="กรุณารอซักครู่...">บันทึก</button>
                                            </div>
                                        @endcan
                                    @else
                                        @can("update-spirit-coin")
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
    <script src="{{ URL::asset("js/spirit-coin/form.js?t=".time()) }}"></script>
    {{-- input fileupload --}}
    <script src="{{ URL::asset("js/inputs/fileupload_img.js?t=".time()) }}"></script>
    @if (Request::route()->getName() == "spirit-coin.view")
        @cannot("update-spirit-coin")
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