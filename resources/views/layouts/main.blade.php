<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> {{ config('app.name') }}</title>
    {{-- main css --}}
    <link href="{{ URL::asset('css/adminlte.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('css/main.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('css/custom.css') }}" rel="stylesheet" type="text/css" />
    {{-- bootstrap select --}}
    <link rel="stylesheet" href="{{ URL::asset('plugins/bootstrap-select/bootstrap-select.min.css') }}">
    {{-- sweetalert --}}
    <link rel="stylesheet" href="{{ URL::asset('plugins/sweetalert2/sweetalert2.min.css') }}" />
    {{-- datapicker --}}
    <link href="{{ URL::asset('plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}" rel="stylesheet">
    {{-- timepicker --}}
    <link href="{{ URL::asset('plugins/bootstrap-timepicker/css/wickedpicker.min.css') }}" rel="stylesheet">
    {{-- icon --}}
    <link rel="icon" type="image/x-icon" href="{{ URL::asset('images/rsu-logo.png') }}">
    {{-- datatable --}}
    <link href="{{ URL::asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}" rel="stylesheet">
    {{-- summernote --}}
    <link rel="stylesheet" href="{{ URL::asset('plugins/summernote/summernote-bs4.min.css') }}" />
    {{-- font --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <style>
        small{
            font-size: 1.2rem;
        }
    </style>
    @yield('css')
</head>

<body>
    <div class="wrapper">
        @include('layouts.header')
        @include('layouts.sidebar')
        <div class="content-wrapper">
            @yield('content')
        </div>
        @include('layouts.footer')
    </div>
    {{-- jquery bootstrap --}}
    <script src="{{ URL::asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('js/popper.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap/js/bootstrap-button.js') }}"></script>
    {{-- bootstrap select --}}
    <script src="{{ URL::asset('plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>
    {{-- sweetalert --}}
    <script src="{{ URL::asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    {{-- datepicker --}}
    <script src="{{ URL::asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker-thai.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.th.js') }}"></script>
    {{-- timepicker --}}
    <script src="{{ URL::asset('plugins/bootstrap-timepicker/js/wickedpicker.min.js') }}"></script>
    {{-- loading box --}}
    <script src="{{ URL::asset('js/jquery.LoadingBox.js') }}"></script>
    {{-- validate form --}}
    <script src="{{ URL::asset('plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/jquery-validation/jquery.validate.default.js') }}"></script>
    <script src="{{ URL::asset('plugins/jquery-validation/additional-methods.min.js') }}"></script>
    {{-- util --}}
    <script src="{{ URL::asset('js/util.js') }}"></script>
    {{-- datatable --}}
    <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    {{-- moment --}}
    <script src="{{ URL::asset('plugins/moment/moment.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ URL::asset('js/adminlte.min.js') }}"></script>
    {{-- summernote --}}
    <script src="{{ URL::asset("plugins/summernote/summernote-bs4.min.js") }}"></script>
    <script>
        // setup default ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode:{
                404: function(){
                    if (lLoading) lLoading.close()
                }
            }
        });

        // display error datatable
        $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
            console.log(message);
        }

        // set default option datatable
        $.extend(true, $.fn.dataTable.defaults, {
            searching: false,
            processing: true,
            paging: true,
            ordering: true,
            serverSide: false,
            language: {
                url: "{{ URL::asset('plugins/datatables/DT_th.json') }}",
            },
        })

        // loading box
        var configLoading = {
            // opacity
            opacity: 0.85,
            // background color
            backgroundColor: "#FFF",
            // width / height of the loading GIF
            loadingImageWitdth: "80px",
            loadingImageHeigth: "80px",
            // path to the loading gif
            loadingImageSrc: '{{ asset('images/loadding.gif') }}'
        },
        lLoading;

        // set default options sum
        $.extend(true,$.summernote.options,{
            fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New','rsu-r'],
            fontNamesIgnoreCheck: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New','rsu-r'],
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']],
            ],
        })

        $(function() {
            // display tooltip
            $('[data-toggle="tooltip"]').tooltip()
        })

        @if (Session::has('success'))
            $(function(){
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    icon: 'success',
                    title: '{{Session::get("success")}}',
                    timer: 2000 })
            })
        @endif
        // global confirm
        var confirmAlert = Swal.mixin({
            title: 'แจ้งเตือน',
            text: 'คุณต้องการบันทึกข้อมูล ใช่หรือไม่ ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก',
        }),
        optionsdate = {
            format: 'dd/mm/yyyy',
            language: "th-th",
            orientation: "bottom auto",
            thaiyear: true,
        },
        optionstime = {
            now: '08:00',
            twentyFour: true,
            upArrow: 'wickedpicker__controls__control-up',
            downArrow: 'wickedpicker__controls__control-down',
            close: 'wickedpicker__close',
            hoverState: 'hover-state',
            title: 'ตั้งเวลา',
            showSeconds: false,
            timeSeparator: ':',
            secondsInterval: 1,
            minutesInterval: 1,
            beforeShow: null,
            afterShow: null,
            show: null,
            clearable: false,
        };
    </script>
    @yield('script')
</body>
</html>
