@extends("layouts.main")
@section("content")
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>จัดการสิทธิ์การใช้งาน</strong>
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
                                        <th>ชื่อสิทธิ์การใช้งาน</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (sizeof($roles) > 0)
                                        @foreach ($roles as $i => $role)
                                            <tr>
                                                <td>{{$i + 1}}</td>
                                                <td>{{$role->role_name}} ({{$role->role_name_th}})</td>
                                                <td>
                                                    <a href="{{route("role.view",["role" => $role->role_id])}}" class="btn btn-secondary btn-block btn-sm btn-flat">
                                                        <i class="fas fa-eye"></i> ดูรายละเอียด
                                                    </a>  
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection