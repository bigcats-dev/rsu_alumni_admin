@extends("layouts.main")
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>การรับสมัครงาน</strong>
                        @can("create-recruitment")
                            <a href="{{route("recruitment.create")}}" class="btn  btn-success"><i class="far fa-plus"></i>
                                สร้างการรับสมัครงาน
                            </a>
                        @endcan
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="div-box card-outline card-info p-4">
                <ul class="nav nav-tabs" role="tablist" id="tab-status">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" data-type="1"  data-toggle="tab">
                            รายการที่อนุมัติแล้ว
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"  data-type="0" data-toggle="tab">
                            รายการรออนุมัติ <span class="right badge badge-danger">{{$badge}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-type="3" data-toggle="tab">
                            รายการที่ยกเลิก
                        </a>
                    </li>
                </ul> 
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade show active" id="tab1">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="overlay-wrapper">
                            <div class="overlay" id="tab-overlay" style="display: none;">
                                <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                <div class="text-bold pt-2">Loading...</div>
                            </div>
                            <div class="col-12 table-responsive">
                                <table id="tb-lists" class="table table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>วันที่สร้าง</th>
                                            <th>ผู้สร้าง</th>
                                            <th>ชื่อตำแหน่ง</th>
                                            <th>บริษัท / หน่วยงาน</th>
                                            <th>วุฒิการศึกษา</th>
                                            <th>จำนวนที่รับสมัคร</th>  
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalAprove" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="frmApprove" action="{{route("recruitment.approve",["recruitment" => ":id"])}}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="1">
                    <div class="modal-header">
                        <h4 class="modal-title"><strong>ยืนยันอนุมัติ ประกาศรับสมัครงาน <i class="fas fa-check-circle text-success"></i> </strong></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div> 
                    <div class="modal-body">
                        <div class="col">
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="rad1" name="rad" value="1" checked>
                                    <label for="rad1" class="custom-control-label">ไม่ต้องการส่งอีเมล</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="rad2" name="rad" value="2">
                                    <label for="rad2" class="custom-control-label">ส่งถึงศิษย์เก่าทั้งหมด</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="rad3" name="rad" value="3">
                                    <label for="rad3" class="custom-control-label">ส่งแบบตั้งค่า</label>
                                </div>
                            </div>
                            <div class="form-group form-email mgb">
                                <div class="row col-12">
                                    <div class="col-1"> 
                                        <label for=""><strong> &nbsp;</strong></label>  
                                    </div>
                                    <div class="col-3">
                                        <label for="">ปีการศึกษา</label>   
                                    </div>
                                    <div class="col-3 sel2">
                                        <label for="">คณะ</label>   
                                    </div>
                                    <div class="col-4 sel3">
                                        <label for="">สาขา</label>   
                                    </div>
                                </div>
                                <div class="form-group btn-add">
                                    <button type="button" id="btn-add" class="btn btn-success btn-sm"><i class="far fa-plus"></i> เพิ่มตัวเลือก</button>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="modal-footer">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-success" data-loading-text="กรุณารอซักครู่...">ยืนยัน</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modalDisApprove" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{route("recruitment.approve",["recruitment" => ":id"])}}" id="frmDisApprove" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="2">
                    <div class="modal-header">
                        <h4 class="modal-title"><strong>ไม่อนุมัติ ประกาศรับสมัครงาน <i class="far fa-times-circle text-danger"></i> </strong></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div> 
                    <div class="modal-body">
                        <div class="col">
                            <h5><strong>หมายเหตุที่ไม่อนุมัติ</strong></h5>
                            <div class="form-group">
                                <textarea class="form-control" name="note" cols="30" rows="4" required></textarea>
                            </div>
                        </div>
                    </div> 
                    <div class="modal-footer">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-success" data-loading-text="กรุณารอซักครู่...">ยืนยัน</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <textarea id="mail-setting-template" style="display: none">
        <div class="row col-12 setting-box mb-2" id="setting-mail-box_{0}" data-index="{0}">
            <div class="col-1"> 
                <label for=""><strong>{1}</strong></label>  
            </div>
            <div class="col-3"> 
                <select 
                    class="selectpicker" 
                    title="เลือกปีการศึกษา" 
                    data-show-subtext="true" 
                    data-live-search="true" 
                    name="setting[{0}][year]"
                    data-msg-required="กรุณาเลือก"
                    required> 
                </select>
            </div>
            <div class="col-3 sel2"> 
                <select 
                    class="selectpicker" 
                    title="เลือกคณะ" 
                    data-show-subtext="true" 
                    data-live-search="true"
                    name="setting[{0}][faculty]"
                    data-msg-required="กรุณาเลือก"
                    required> 
                </select>
            </div>
            <div class="col-4 sel3"> 
                <select 
                    class="selectpicker" 
                    title="เลือกสาขาวิชา" 
                    data-show-subtext="true" 
                    data-live-search="true"
                    name="setting[{0}][department]"
                    data-msg-required="กรุณาเลือก"
                    required> 
                    </select>
            </div> 
            <div class="col-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="$(this).parent().parent().remove()">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
    </textarea>
@endsection
@section("script")
    <script>
        var configTable = Object.assign($.fn.dataTable.defaults,{
            serverSide: true,
            order:[[ 0, "asc" ]],
            ajax: {
                url: '{{ route("recruitment.index") }}',
                type: "GET",
                data: function(data) {
                    data.type = sessionStorage.hasOwnProperty('tab_recruitment')
                            ? sessionStorage.getItem('tab_recruitment')
                            : $("ul#tab-status li a.active").attr('data-type')
                },
                beforeSend: function(request) {
                    $("ul#tab-status li a:not(.active)").addClass('disabled')
                },
                dataSrc: function(json) {
                    $("ul#tab-status li a:not(.active)").removeClass('disabled')
                    return json.data
                },
            },
            columns:[
                {data: 'seqnum', width: '5%'},
                {data: 'created_at', orderable: true, width: '10%'},
                {data: 'u_create_name', orderable: true, width: '10%'},
                {data: 'position', orderable: false, width: '15%'},
                {data: 'company', orderable: false, width: '15%'},
                {data: 'education', orderable: false, width: '10%'},
                {data: 'number_of_applications', orderable: false, width: '10%'},
                {
                    data: 'approved',
                    orderable: false,
                    render: function(value, type, row){
                        if (row.status == '1') {
                            switch (value) {
                                case '0':
                                    return(`
                                        @can("approve-recruitment")
                                            <button type="button" class="btn btn-success btn-block btn-sm btn-flat" data-id="${row.recruitment_id}" data-toggle="modal" data-target="#modalAprove">
                                                <i class="far fa-check"></i> อนุมัติ
                                            </button>
                                            <button type="button" class="btn btn-danger btn-block btn-sm btn-flat" data-id="${row.recruitment_id}" data-toggle="modal" data-target="#modalDisApprove">
                                                <i class="fas fa-times"></i> ไม่อนุมัติ
                                            </button>
                                        @endcan
                                        <a href="${row.action?.view}" class="btn btn-secondary btn-block btn-sm btn-flat">
                                            <i class="fas fa-eye"></i> ดูรายละเอียด
                                        </a>
                                    `)
                                case '1':
                                    return(`
                                        <div class="row">
                                            <div class="col">
                                                <a href="${row.action?.view}" class="btn btn-secondary btn-block btn-sm btn-flat">
                                                    <i class="fas fa-eye"></i> ดูรายละเอียด
                                                </a>  
                                                <div class="custom-control custom-switch p-2">
                                                    <input type="checkbox" class="custom-control-input" id="active_${row.recruitment_id}" ${row.active == '1' ? 'checked' : ''}>
                                                    <label class="custom-control-label" for="active_${row.recruitment_id}">การใช้งาน</label>
                                                </div> 
                                            </div>
                                            @can("del-recruitment")
                                                <div class="col">
                                                    <form id="frmDestroy_${row.recruitment_id}" method="POST" action="${row.action.delete}">
                                                        @csrf
                                                        <button type="button" class="btn btn-danger btn-block btn-sm btn-flat">
                                                            <i class="fas fa-times"></i> ลบ
                                                        </button>
                                                    </form>
                                                </div>
                                            @endcan
                                        </div>
                                    `)
                            }
                        } else {
                            return(`
                                <a href="${row.action?.view}" class="btn btn-secondary btn-block btn-sm btn-flat mb-2">
                                    <i class="fas fa-eye"></i> ดูรายละเอียด
                                </a>
                                <form method="POST" action="${row.action?.restore}" id="frmReStore_${row.recruitment_id}">
                                    @csrf
                                    <button type="button" class="btn btn-primary btn-block btn-sm btn-flat" id="btn-restore_${row.recruitment_id}">
                                        <i class="fas fa-redo-alt"></i> Restore 
                                    </button>
                                </form>
                            `)
                        }
                        
                    }
                }
            ],
            rowCallback: function(row, data) {
                const {recruitment_id,action} = data
                $(row).find('[data-toggle="tooltip"]').tooltip()
                // active
                $(row).find(`input[id="active_${recruitment_id}"]`)
                    .on('click',function(e){
                        $.post(action.active,function(){},'json')
                    })
                // delete
                $(row).find(`form[id="frmDestroy_${recruitment_id}"] button[type="button"]`)
                    .on('click',async function(e){
                        e.preventDefault()
                        const rs = await confirmAlert.fire({
                            text: 'คุณต้องการลบข้อมูล ใช่หรือไม่?',
                            icon: 'warning',
                        })
                        if(rs) {
                            if (rs.isConfirmed) {
                                $('#tab-overlay').show()
                                $(e.target).closest('form')[0].submit()
                            }
                        }
                    })
                // restore
                $(row).find(`button[id="btn-restore_${recruitment_id}"]`)
                    .on('click',async function(e){
                        e.preventDefault()
                        const rs = await confirmAlert.fire()
                        if(rs) {
                            if (rs.isConfirmed) {
                                $('#tab-overlay').show()
                                $(e.target).closest('form')[0].submit()
                            }
                        }
                    })
            }
        });
    </script>
    <script src="{{ URL::asset("js/recruitment/index.js?t=".time()) }}"></script>
@endsection
