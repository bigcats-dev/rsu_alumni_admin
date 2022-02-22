@extends("layouts.main")
@section("content")
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>จัดการผู้ใช้งาน</strong>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="div-box card-outline card-info p-2">
                <div class="tab-content tab-pane">
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
                                        <th>ชื่อ - นามสกุล</th>
                                        <th>อีเมล</th>
                                        <th>สิทธิ์การใช้งาน</th>
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
    </section>
@endsection
@section("script")
    <script>
        var configTable = Object.assign($.fn.dataTable.defaults,{
            serverSide: true,
            order:[[ 0, "asc" ]],
            ajax: {
                url: '{{ route("user.index") }}',
                type: "GET",
                beforeSend: function(request) {},
                dataSrc: function(json) {
                    return json.data
                },
            },
            columns:[
                {data: 'seqnum', width: '5%'},
                {data: 'created_at', orderable: true, width: '10%'},
                {data: 'fullname', orderable: false, width: '20%'},
                {data: 'email', orderable: false, width: '20%'},
                {
                    data: 'role_name_th', 
                    orderable: false, 
                    width: '25%',
                    render: function(value){
                        if (!value) return '' 
                        return '<h4><span class="badge badge-primary"> ' + value + '</span></h4>'
                    }
                },
                {
                    orderable: false,
                    render: function(value, type, row){
                        return(`
                            <a href="${row.action?.view}" class="btn btn-secondary btn-block btn-sm btn-flat">
                                <i class="fas fa-eye"></i> ดูรายละเอียด
                            </a>
                        `)
                    }
                }
            ],
            rowCallback: function(row, data) {
                $(row).find('[data-toggle="tooltip"]').tooltip()
            }
        });
    </script>
    <script src="{{ URL::asset("js/user/index.js?t=".time()) }}"></script>
@endsection