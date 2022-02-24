<nav class="main-header navbar navbar-expand navbar-white navbar-light"> 
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">หน้าหลักเว็บไซต์</a>
        </li>  
    </ul> 
    <ul class="navbar-nav ml-auto"> 
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-user"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a class="dropdown-item" href="javascript:;"
                    onclick="(() => {
                        if (confirm('ต้องการปิดระบบ?')) {
                            lLoading = new $.LoadingBox(configLoading);
                            $.get('/api/portal/v1/logout', function(data, status){
                                if( data.code === 200 ) {
                                    document.location.href = '/logout';
                                    setTimeout(function(){ window.location.reload() }, 3000);
                                }
                            });
                        }
                    })()">
                    <i class="fas fa-sign-out-alt">&nbsp;</i>
                    ออกจากระบบ
                </a>
            </div>
        </li> 
    </ul>
</nav>