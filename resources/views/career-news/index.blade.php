@extends("layouts.main")
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>ข่าวอบรมหลักสูตรอาชีพ</strong>
                        <a href="{{route("career-news.create")}}" class="btn  btn-success"><i class="far fa-plus"></i>
                            สร้างข่าวอบรมหลักสูตรอาชีพ
                        </a>
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
                                            <th>วันที่แก้ไข</th>
                                            <th>ผู้สร้าง</th>
                                            <th>ผู้แก้ไข</th>
                                            <th>หัวข้อข่าว</th>
                                            <th>วันที่เริ่ม</th>
                                            <th>วันที่สิ้นสุด</th>
                                            <th>รูปโปสเตอร์</th>
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
                <form id="frmApprove" action="{{route("career-news.approve",["news" => ":id"])}}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="1">
                    <div class="modal-header">
                        <h4 class="modal-title"><strong>ยืนยันอนุมัติ ข่าวอบรมหลักสูตรอาชีพ <i class="fas fa-check-circle text-success"></i> </strong></h4>
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
                <form action="{{route("career-news.approve",["news" => ":id"])}}" id="frmDisApprove" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="2">
                    <div class="modal-header">
                        <h4 class="modal-title"><strong>ไม่อนุมัติ ข่าวสารประชาสัมพันธ์ <i class="far fa-times-circle text-danger"></i> </strong></h4>
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
                url: '{{ route("career-news.index") }}',
                type: "GET",
                data: function(data) {
                    data.type = sessionStorage.hasOwnProperty('tab_career_news')
                            ? sessionStorage.getItem('tab_career_news')
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
                {data: 'updated_at', orderable: true, width: '10%'},
                {data: 'u_create_name', orderable: false, width: '10%'},
                {data: 'u_update_name', orderable: false, width: '10%'},
                {data: 'title', orderable: false, width: '25%'},
                {data: 'start_date', orderable: false, width: '10%'},
                {data: 'end_date', orderable: false, width: '10%'},
                {
                    data: 'file',
                    width: '8%',
                    orderable: false,
                    render: function(value, type, row){
                        return '<img src="' + value.path + '" class="img-thumbnail" />'
                    }
                },
                {
                    data: 'approved',
                    orderable: false,
                    render: function(value, type, row){
                        if (row.status == '1') {
                            switch (value) {
                                case '0':
                                    return(`
                                        <button type="button" class="btn btn-success btn-block btn-sm btn-flat" data-id="${row.career_news_id}" data-toggle="modal" data-target="#modalAprove">
                                            <i class="far fa-check"></i> อนุมัติ
                                        </button>
                                        <button type="button" class="btn btn-danger btn-block btn-sm btn-flat" data-id="${row.career_news_id}" data-toggle="modal" data-target="#modalDisApprove">
                                            <i class="fas fa-times"></i> ไม่อนุมัติ
                                        </button>  
                                        <a href="${row.action?.view}" class="btn btn-secondary btn-block btn-sm btn-flat">
                                            <i class="fas fa-eye"></i> ดูรายละเอียด
                                        </a>
                                    `)
                                case '1':
                                    return(`
                                        <div class="row">
                                            <div class="col">
                                                <a href="${row.action?.view}" class="btn btn-secondary btn-block btn-sm btn-flat mb-1">
                                                    <i class="fas fa-eye"></i> ดูรายละเอียด
                                                </a>
                                                <form id="frmDestroy_${row.career_news_id}" method="POST" action="${row.action.delete}" class="mb-1">
                                                    @csrf
                                                    <button type="button" class="btn btn-danger btn-block btn-sm btn-flat">
                                                        <i class="fas fa-times"></i> ลบ
                                                    </button>
                                                </form>
                                                <select class="form-control form-control-sm" id="sl-priority_${row.career_news_id}">
                                                    @for ($i = 1;$i <= $records;$i++)
                                                        <option value="{{$i}}" ${row.priority == '{{$i}}' ? 'selected' : ''}>{{$i}}</option>
                                                    @endfor
                                                </select> 
                                            </div>
                                        </div>
                                    `)
                            }
                        } else {
                            return(`
                                <form method="POST" action="${row.action?.restore}" id="frmReStore_${row.career_news_id}">
                                    @csrf
                                    <button type="button" class="btn btn-primary btn-block btn-sm btn-flat" id="btn-restore_${row.career_news_id}">
                                        <i class="fas fa-redo-alt"></i> Restore 
                                    </button>
                                </form>
                            `)
                        }
                        
                    }
                }
            ],
            rowCallback: function(row, data) {
                const {career_news_id,action} = data
                $(row).find('[data-toggle="tooltip"]').tooltip()
                // priority
                $(row).find(`select[id="sl-priority_${career_news_id}"]`)
                    .on('change',function(e){
                        $.post(action.priority,{priority: $(this).val()},function(){},'json')
                    })
                // delete
                $(row).find(`form[id="frmDestroy_${career_news_id}"] button[type="button"]`)
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
                $(row).find(`button[id="btn-restore_${career_news_id}"]`)
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
    <script src="{{ URL::asset("js/career-news/index.js?t=".time()) }}"></script>
@endsection
