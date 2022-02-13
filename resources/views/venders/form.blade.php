@extends("layouts.main")
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>ลงทะเบียนคู่ค้า</strong>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="div-box card-outline card-info p-4">
                <div class="col tab-pane">
                    <div class="overlay-wrapper">
                        <div class="overlay" id="tab-overlay" style="display: none;">
                            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                            <div class="text-bold pt-2">Loading...</div>
                        </div>
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
                                    @if (Request::route()->getName() == "vender.create")
                                        action="{{route("vender.store")}}"
                                    @else
                                        action="{{route("vender.update",["vender" => $data->vender_id])}}"
                                    @endif 
                                    method="POST" id="frmSave" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group row">
                                        <div class="col-xl-6">
                                            <label for="">ชื่อนิติบุคคล</label>
                                            <input 
                                                type="text" 
                                                class="form-control form-control-border" 
                                                required
                                                name="corporation_name"
                                                placeholder="ชื่อนิติบุคคล"
                                                value="{{old("corporation_name",$data->corporation_name ?? "")}}">
                                        </div> 
                                        <div class="col-xl-6">
                                            <label for="">เลขที่การจดทะเบียน</label>
                                            <input 
                                                type="text" 
                                                class="form-control form-control-border" 
                                                name="corporation_no"
                                                placeholder="เลขที่การจดทะเบียน"
                                                required
                                                value="{{old("corporation_no",$data->corporation_no ?? "")}}">
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <div class="col-xl-12">
                                            <label for="">ที่อยู่ </label>
                                            <input 
                                                type="text" 
                                                class="form-control form-control-border" 
                                                name="address"
                                                placeholder="ที่อยู่"
                                                required
                                                value="{{old("address",$data->address ?? "")}}">
                                        </div>  
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-xl-6">
                                            <label for="">อีเมล</label>
                                            <input 
                                                type="text" 
                                                class="form-control form-control-border @error('email') error @enderror" 
                                                required
                                                data-rule-regex="^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$"
                                                data-msg-regex="รูปแบบการกรอกอีเมลไม่ถูกต้อง"
                                                name="email"
                                                placeholder="อีเมล"
                                                value="{{old("email",$data->email ?? "")}}">
                                            <small class="form-text text-warning">* โปรดกรอกอีเมลที่ใช้งานจริง ระบบจะส่งรหัสผ่านในการเข้าสู่ระบบครั้งแรกไปที่อีเมล</small>
                                        </div> 
                                        <div class="col-xl-6">
                                            <label for="">เบอร์โทร 1</label>
                                            <input 
                                                type="text" 
                                                class="form-control form-control-border" 
                                                name="tel_1"
                                                placeholder="เบอร์โทร 1"
                                                required
                                                value="{{old("tel_1",$data->tel_1 ?? "")}}">
                                        </div> 
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-xl-6">
                                            <label for="">เบอร์โทร 2</label>
                                            <input 
                                                type="text" 
                                                class="form-control form-control-border" 
                                                name="tel_2"
                                                placeholder="เบอร์โทร 2"
                                                value="{{old("tel_2",$data->tel_2 ?? "")}}">
                                        </div> 
                                        <div class="col-xl-6">
                                            <label for="">เบอร์โทร 3</label>
                                            <input 
                                                type="text" 
                                                class="form-control form-control-border" 
                                                name="tel_3"
                                                placeholder="เบอร์โทร 3"
                                                value="{{old("tel_3",$data->tel_3 ?? "")}}">
                                        </div> 
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-xl-12 mb-2">
                                            <label for="">ผู้ประสานงาน
                                                <button type="button" class="btn btn-info btn-sm btn-flat pt-2" id="btn-add-coordinator">
                                                    <i class="fas fa-plus"></i>   เพิ่ม
                                                </button>
                                            </label>
                                            <div class="col-xl-12 col-12" id="coordinators-panel">
                                                @php
                                                    $coordinators = old("coordinators",$data->coordinators ?? [])
                                                @endphp
                                                @foreach ($coordinators as $key => $item)
                                                    <div class="row mb-2" id="coordinators_{{$key}}" data-index="{{$key}}"> 
                                                        <div class="col-xl-3 col-12"> 
                                                            <input 
                                                                type="text" 
                                                                class="form-control form-control-border" 
                                                                name="coordinators[{{$key}}][name]" 
                                                                placeholder="ชื่อ - นามสกุล"
                                                                required
                                                                value="{{$item["name"]}}">    
                                                        </div>    
                                                        <div class="col-xl-3 col-12">
                                                            <input 
                                                                type="text" 
                                                                class="form-control form-control-border" 
                                                                name="coordinators[{{$key}}][email]"
                                                                placeholder="อีเมล"
                                                                required
                                                                value="{{$item["email"]}}">    
                                                        </div>   
                                                        <div class="col-xl-2 col-12">
                                                            <input 
                                                                type="text" 
                                                                class="form-control form-control-border" 
                                                                name="coordinators[{{$key}}][tel]"
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
                                    </div>
                                    <div class="form-group row border-bottom">
                                        <div class="col-xl-3">
                                            <h3 ><strong>เอกสารอัพโหลด</strong></h3>   
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label for="">ภ.พ.20</label>
                                        @include("inputs.fileupload_pdf",[
                                            "id" => 1,
                                            "name" => "files_1[]",
                                            "required" => false ,
                                            "pdf" => $files[0] ?? null,
                                            "multiple" => true,
                                            "del" => true,
                                        ])
                                    </div>
                                    <div class="form-group">
                                        <label for="">หนังสือรับรอง</label>
                                        @include("inputs.fileupload_pdf",[
                                            "id" => 2,
                                            "name" => "files_2[]",
                                            "required" => false ,
                                            "pdf" => $files[1] ?? null,
                                            "multiple" => true,
                                            "del" => true,
                                        ])
                                    </div>
                                    <div class="form-group">
                                        <label for="">สำเนาผู้มีอำนาจของบริษัท</label>
                                        @include("inputs.fileupload_pdf",[
                                            "id" => 3,
                                            "name" => "files_3[]",
                                            "required" => false ,
                                            "pdf" => $files[2] ?? null,
                                            "multiple" => true,
                                            "del" => true,
                                        ])
                                    </div>
                                    <div class="form-group">
                                        <label for="">สำเนาหน้าบัญชีธนาคาร</label>
                                        @include("inputs.fileupload_pdf",[
                                            "id" => 4,
                                            "name" => "files_4[]",
                                            "required" => false ,
                                            "pdf" => $files[3] ?? null,
                                            "multiple" => true,
                                            "del" => true,
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
        </div>
    </section>

    <textarea id="coordinator-template" style="display: none">
        <div class="row mb-2" id="coordinators_{0}" data-index="{0}"> 
            <div class="col-xl-3 col-12"> 
                <input 
                    type="text" 
                    class="form-control form-control-border" 
                    name="coordinators[{0}][name]" 
                    placeholder="ชื่อ - นามสกุล"
                    required>    
            </div>    
            <div class="col-xl-3 col-12">
                <input 
                    type="text" 
                    class="form-control form-control-border" 
                    name="coordinators[{0}][email]"
                    placeholder="อีเมล"
                    required>    
            </div>   
            <div class="col-xl-2 col-12">
                <input 
                    type="text" 
                    class="form-control form-control-border" 
                    name="coordinators[{0}][tel]"
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
    <script src="{{ URL::asset("js/vender/form.js?t=".time()) }}"></script>
    {{-- input fileupload --}}
    <script src="{{ URL::asset("js/inputs/fileupload_pdf.js?t=".time()) }}"></script>
@endsection