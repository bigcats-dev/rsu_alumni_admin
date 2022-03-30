@extends("layouts.main")
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>รายชื่อศิษย์เก่า</strong>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="div-box card-outline card-info p-4">
                <form action="" id="frmSearch">
                    <div class="form-group row">
                        <div class="col-2">
                            <label for="">ปีการศึกษาที่จบ</label>
                            <input type="text" class="form-control" name="year" placeholder="ปีการศึกษาที่จบ">
                        </div>
                        <div class="col-2">
                            <label for="">คณะ</label>
                            <select 
                                class="selectpicker" 
                                data-show-subtext="true" 
                                data-live-search="true"
                                name="faculty">
                                <option value="">-- เลือก --</option>
                                @if (count($ms_faculty) > 0)
                                    @foreach ($ms_faculty as $i)
                                        <option value="{{ $i->faculty_code }}">{{ $i->faculty_name_th }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="">สาขาวิชา</label>
                            <select 
                                class="selectpicker" 
                                data-show-subtext="true" 
                                data-live-search="true"
                                name="major">
                                <option value="">-- เลือก --</option>
                                @if (count($ms_major) > 0)
                                    @foreach ($ms_major as $i)
                                        <option value="{{ $i->major_code }}">{{ $i->major_name_th }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="">รหัส/ชื่อ-นามสกุล</label>
                            <input type="text" class="form-control" name="name" placeholder="รหัส/ชื่อ-นามสกุล">
                        </div>
                        <div class="col-2">
                            <label for="">&nbsp;</label>
                            <button 
                                class="btn btn-info btn-block btn-flat" 
                                type="button" 
                                id="btnSearch" 
                                onclick="datatable.ajax.reload()"
                                data-loading-text="กรุณารอซักครู่..."><i class="far fa-search"></i> ค้นหา</button>
                        </div>
                    </div>
                </form>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade show active" id="tab1">
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
                                            <th>รหัสนักศึกษา</th>
                                            <th>ปีที่จบ</th>
                                            <th>คณะ</th>
                                            <th>สาขาวิชา</th>
                                            <th>ชื่อ - นามสกุล</th>
                                            <th>ที่อยู่ติดต่อ</th>
                                            <th>อีเมล</th>
                                            <th>เบอร์โทรศัพท์</th>
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
@endsection
@section("script")
    <script>
        var frmSearch = $('form#frmSearch'),
            majors = @json($ms_major);
        var configTable = Object.assign($.fn.dataTable.defaults,{
            serverSide: true,
            ordering: false,
            ajax: {
                url: '{{ route("alumni.index") }}',
                type: "GET",
                data: function(data) {
                    data.year = $('[name="year"]',frmSearch).val()
                    data.faculty = $('[name="faculty"]',frmSearch).val()
                    data.major = $('[name="major"]',frmSearch).val()
                    data.name = $('[name="name"]',frmSearch).val()
                },
                beforeSend: function(request) {$('#btnSearch',frmSearch).button('loading')},
                dataSrc: function(json) {
                    $('#btnSearch',frmSearch).button('reset')
                    return json.data
                },
            },
            columns:[
                {data: 'seqnum', orderable: false, width: '5%'},
                {data: 'alumni_code', orderable: false, width: '7%'},
                {data: 'graduate_year', orderable: false, width: '5%'},
                {data: 'faculty_name_th', orderable: false, width: '10%'},
                {data: 'major_name_th', orderable: false, width: '18%'},
                {
                    orderable: false, 
                    width: '10%',
                    render: function(value,type,row){
                        return(`${row.alumni_name_tha} ${row.alumni_lastname_tha}`)
                    }
                },
                {data: 'contact_address', orderable: false, width: '25%'},
                {data: 'e_mail', orderable: false, width: '10%'},
                {data: 'contact_phone_no', orderable: false, width: '10%'},
            ],
            rowCallback: function(row, data) {}
        });
    </script>
    <script src="{{ URL::asset("js/alumni/index.js?t=".time()) }}"></script>
@endsection
