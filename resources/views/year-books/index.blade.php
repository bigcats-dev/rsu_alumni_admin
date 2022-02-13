@extends("layouts.main")
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>หนังสือรุ่น</strong>
                        <a href="{{route("year-book.create")}}" class="btn  btn-success"><i class="far fa-plus"></i>
                            สร้างหนังสือรุ่น
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
                        <a class="nav-link" href="#" data-type="1"  data-toggle="tab">
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
                                <div class="col-12 table-responsive">
                                    <table id="tb-lists" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>วันที่สร้าง</th>
                                                <th>วันที่แก้ไข</th>
                                                <th>ผู้สร้าง</th>
                                                <th>ผู้แก้ไข</th>
                                                <th>ชื่อหนังสือ</th>
                                                <th>ไฟล์</th>
                                                <th>รูปภาพ</th>
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
        </div>
    </section>
    <div class="modal fade" id="modalDisApprove" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{route("year-book.approve",["book" => ":id"])}}" id="frmDisApprove" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="2">
                    <div class="modal-header">
                        <h4 class="modal-title"><strong>ไม่อนุมัติ หนังสือรุ่น <i class="far fa-times-circle text-danger"></i> </strong></h4>
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
@endsection
@section("script")
    <script>
        var configTable = Object.assign($.fn.dataTable.defaults,{
            serverSide: true,
            order:[[ 0, "asc" ]],
            ajax: {
                url: '{{ route("year-book.index") }}',
                type: "GET",
                data: function(data) {
                    data.type = sessionStorage.hasOwnProperty('tab_year_book')
                            ? sessionStorage.tab_year_book
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
                {data: 'name', orderable: false, width: '25%'},
                {
                    data: 'files',
                    width: '5%',
                    orderable: false,
                    render: function(value, type, row){
                        return(`
                            <a href="${value[0].url}" target="_blank">
                                <i data-toggle="tooltip" title="${value[0].name} (${value[0].size})" class="fas fa-file-pdf text-danger" style="font-size: 2rem"></i>
                            </a>
                        `)
                    }
                },
                {
                    data: 'images' ,
                    width: '10%' ,
                    orderable: false,
                    render: function(value, type, row){
                        return(`
                            <a href="${value[0].url}" target="_blank">
                                <img src="${value[0].url}" class="img-thumbnail" title="${value[0].name} (${value[0].size})" data-toggle="tooltip"/>
                            </a>
                        `)
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
                                        <form id="frmApprove_${row.yearbook_id}" method="POST" action="${row.action.approve}" class="mb-2">
                                            @csrf
                                            <input type="hidden" name="action" value="1" />
                                            <button type="button" class="btn btn-success btn-block btn-sm btn-flat">
                                                <i class="far fa-check"></i> อนุมัติ
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-danger btn-block btn-sm btn-flat" data-id="${row.yearbook_id}" data-toggle="modal" data-target="#modalDisApprove">
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
                                                <a href="${row.action?.view}" class="btn btn-secondary btn-block btn-sm btn-flat">
                                                    <i class="fas fa-eye"></i> ดูรายละเอียด
                                                </a> 
                                            </div>
                                            <div class="col">
                                                <form id="frmDestroy_${row.yearbook_id}" method="POST" action="${row.action.delete}">
                                                    @csrf
                                                    <button type="button" class="btn btn-danger btn-block btn-sm btn-flat">
                                                        <i class="fas fa-times"></i> ลบ
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    `)
                            }
                        } else {
                            return(`
                                <form method="POST" action="${row.action?.restore}" id="frmReStore_${row.yearbook_id}">
                                    @csrf
                                    <button type="button" class="btn btn-primary btn-block btn-sm btn-flat" id="btn-restore_${row.yearbook_id}">
                                        <i class="fas fa-redo-alt"></i> Restore 
                                    </button>
                                </form>
                            `)
                        }
                        
                    }
                }
            ],
            rowCallback: function(row, data) {
                const {yearbook_id,action} = data
                $(row).find('[data-toggle="tooltip"]').tooltip()
                // approve
                $(row).find(`form[id="frmApprove_${yearbook_id}"] button[type="button"]`)
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
                // delete
                $(row).find(`form[id="frmDestroy_${yearbook_id}"] button[type="button"]`)
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
                $(row).find(`button[id="btn-restore_${yearbook_id}"]`)
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
    <script src="{{ URL::asset("js/year-book/index.js?t=".time()) }}"></script>
@endsection