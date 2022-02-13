@extends("layouts.main")
@section('css')
    <style>
        .tree,
        .tree ul {
            margin: 0;
            padding: 0;
            list-style: none;
            width: 100%;
        }

        .tree ul {
            margin-left: 1em;
            position: relative
        }

        .tree ul ul {
            margin-left: .5em
        }

        .tree ul:before {
            content: "";
            display: block;
            width: 0;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            border-left: 1px solid
        }

        .tree li {
            margin: 0;
            padding: 0.3em 1em;
            line-height: 2em;
            color: #369;
            font-weight: 500;
            position: relative;
            border-bottom: 1px solid #eee;
        }

        .tree ul li:before {
            content: "";
            display: block;
            width: 10px;
            height: 0;
            border-top: 1px solid;
            margin-top: -1px;
            position: absolute;
            top: 1em;
            left: 0
        }

        .tree ul li:last-child {
            border-bottom: 0px
        }

        .tree ul li:last-child:before {
            background: #fff;
            height: auto;
            top: 1em;
            bottom: 0;
            border-bottom: 0px
        }

        .indicator {
            margin-right: 5px;
        }

        .tree li a {
            text-decoration: none;
            color: #000;
            font-size: 20px;
        }

        .tree li button,
        .tree li button:active,
        .tree li button:focus {
            text-decoration: none;
            color: #369;
            border: none;
            background: transparent;
            margin: 0px 0px 0px 0px;
            padding: 0px 0px 0px 0px;
            outline: 0;
        }

        .tree li i.fa-plus-circle,
        .tree li i.fa-edit,
        .tree li i.fa-trash {
            font-size: 18px;
            cursor: pointer;
            margin-left: 5px;
        }

        .tree li i.fa-plus-circle {
            color: #369;
        }

        .tree li i.fa-edit {
            color: #28a745;
        }

        .tree li a.active {
            background: #1caeb5;
            color: #fff;
            padding: 3px;
            border-radius: 20px;
        }

    </style>
@endsection
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <strong>จัดการข้อมูลประเภทรางวัล</strong>
                        <button data-action="{{route("award.store")}}" class="btn  btn-success" data-toggle="modal" data-target="#mdAward">
                            <i class="far fa-plus"></i> สร้างประเภทรางวัล
                        </button>
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
                        <div class="col-xl-8 col-12 tab-pane">
                            <div class="overlay-wrapper">
                                <div class="overlay" id="tab-overlay" style="display: none;">
                                    <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                    <div class="text-bold pt-2">Loading...</div>
                                </div>
                                <ul id="tree_award">
                                    @if (count($data) > 0)
                                        @foreach ($data as $i => $item)
                                            <li>
                                                <a href="javascript::void(0)">
                                                    {{$i + 1}} . {{ $item->award_type_name }}
                                                </a>
                                                <i  data-toggle="modal"
                                                    data-target="#mdAward"
                                                    data-name="{{$item->award_type_name}}"
                                                    data-action="{{ route("award.update",["award" => $item->award_type_id]) }}"
                                                    class="fas fa-edit float-right">
                                                </i>
                                                <i class="fas fa-trash float-right text-danger" data-id="{{$item->award_type_id}}"></i>                                                                    
                                                <form method="POST" style="display: none" id="frmDestroy_{{$item->award_type_id}}" action="{{ route("award.destroy",["award" => $item->award_type_id]) }}">
                                                        @csrf
                                                </form>
                                                <i  data-toggle="modal" 
                                                    data-target="#mdAward"
                                                    data-parent_id="{{ $item->award_type_id }}"
                                                    data-action="{{ route("award.store") }}"
                                                    class="fas fa-plus-circle float-right">
                                                </i>
                                                <div class="custom-control custom-switch float-right">
                                                    <input
                                                        id="active_{{$item->award_type_id}}"
                                                        type="checkbox" 
                                                        class="custom-control-input" 
                                                        data-action="{{route("award.active",["award" => $item->award_type_id])}}"
                                                        {{$item->active == 1
                                                            ? "checked"
                                                            : ""}}>
                                                    <label class="custom-control-label" for="active_{{$item->award_type_id}}">การใช้งาน</label>
                                                </div>
                                                @if (count($item->award_sub_types) > 0)
                                                    <ul>
                                                        @foreach ($item->award_sub_types as $j => $subitem)
                                                            <li>
                                                                <a href="javascript::void(0)">
                                                                    {{$i + 1}}.{{$j + 1}} {{ $subitem->award_type_name }}
                                                                </a>
                                                                <i  data-toggle="modal"
                                                                    data-target="#mdAward"
                                                                    data-name="{{$subitem->award_type_name}}"
                                                                    data-action="{{ route("award.update",["award" => $subitem->award_type_id]) }}"
                                                                    class="fas fa-edit float-right"></i>
                                                                <i class="fas fa-trash float-right text-danger" data-id="{{$subitem->award_type_id}}"></i>                                                                    
                                                                <form style="display: none" method="POST" id="frmDestroy_{{$subitem->award_type_id}}" action="{{ route("award.destroy",["award" => $subitem->award_type_id]) }}">
                                                                     @csrf
                                                                </form>
                                                                <div class="custom-control custom-switch float-right">
                                                                    <input
                                                                        id="active_{{$subitem->award_type_id}}"
                                                                        type="checkbox" 
                                                                        class="custom-control-input" 
                                                                        data-action="{{route("award.active",["award" => $subitem->award_type_id])}}"
                                                                        {{$subitem->active == 1
                                                                            ? "checked"
                                                                            : ""}}>
                                                                    <label class="custom-control-label" for="active_{{$subitem->award_type_id}}">การใช้งาน</label>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="mdAward" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">ข้อมูลประเภทรางวัล</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="frmAward" action="">
                        {{ csrf_field() }}
                        <input type="hidden" name="award_sub_type_id">
                        <div class="form-group">
                            <label for="">ชื่อประเภทรางวัล</label>
                            <input
                                required
                                autocomplete="off"
                                type="text"
                                class="form-control form-control-border"
                                name="award_type_name"
                                placeholder="ชื่อประเภทรางวัล">
                        </div>
                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" data-loading-text="กรุณารอซักครู่..."> บันทึก</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"> ยกเลิก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('js/award/index.js?t=' . time()) }}"></script>
@endsection
