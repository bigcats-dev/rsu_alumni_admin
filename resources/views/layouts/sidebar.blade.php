<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{ asset("images/sm-logo.png") }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3">
        <p class="brand-text font-weight-light">ระบบบริหารจัดการ <br> ศิษย์เก่าและชุมชนสัมพันธ์</p>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset("images/user2-160x160.jpg") }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{auth()->user()->fullname}}</a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="index.html" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dash Board
                            <span class="right badge badge-success">3</span>
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        href="{{route("press-release.index")}}" 
                        class="nav-link {{in_array(Request::route()->getName(),["press-release.index","press-release.create","press-release.view","press-release.edit"]) 
                            ? "active" 
                            : ""}}">
                        <i class="nav-icon far fa-newspaper"></i>
                        <p>ข่าวสารประชาสัมพันธ์</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        href="{{route("event-news.index")}}" 
                        class="nav-link {{in_array(Request::route()->getName(),["event-news.index","event-news.create","event-news.view","event-news.edit"]) ? "active" : ""}}">
                        <i class="nav-icon far fa-image"></i>
                        <p>ข่าวสารกิจกรรมศิษย์เก่าและชุมชน</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a  
                        href="{{route("recruitment.index")}}" 
                        class="nav-link {{in_array(Request::route()->getName(),["recruitment.index","recruitment.create","recruitment.view","recruitment.edit"]) ? "active" : ""}}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>การรับสมัครงาน</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        href="#" 
                        class="nav-link">
                        <i class="nav-icon fas fa-list"></i>
                        <p>รายชื่อศิษย์เก่า</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        href="{{route("alumni-affairs.index")}}" 
                        class="nav-link {{in_array(Request::route()->getName(),["alumni-affairs.index","alumni-affairs.create","alumni-affairs.view","alumni-affairs.edit"]) ? "active" : ""}}">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>กิจการศิษย์เก่า</p>
                    </a>
                </li>
                <li class="nav-item {{in_array(Request::route()->getName(),[
                    "spirit-coin.index","spirit-coin.create","spirit-coin.view","spirit-coin.edit",
                    "spirit-coin-activity.index","spirit-coin-activity.create","spirit-coin-activity.view","spirit-coin-activity.edit"]) ? "menu-is-opening menu-open" : ""}}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas far fa-money-bill-alt"></i>
                        <p>
                            สปิริตคอยน์
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a 
                                href="{{route("spirit-coin.index")}}" 
                                class="nav-link {{in_array(Request::route()->getName(),["spirit-coin.index","spirit-coin.create","spirit-coin.view","spirit-coin.edit"]) ? "active" : ""}}">
                                <i class="fas fa-store-alt"></i>
                                <p>ร้านค้าสปิริตคอยน์</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a 
                                href="{{route("spirit-coin-activity.index")}}" 
                                class="nav-link {{in_array(Request::route()->getName(),["spirit-coin-activity.index","spirit-coin-activity.create","spirit-coin-activity.view","spirit-coin-activity.edit"]) ? "active" : ""}}">
                                <i class="fas fa-coins"></i>
                                <p>กิจกรรมสปิริตคอยน์</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a 
                        href="{{route("year-book.index")}}" 
                        class="nav-link {{in_array(Request::route()->getName(),["year-book.index","year-book.create","year-book.view","year-book.edit"]) ? "active" : ""}}">
                        <i class="nav-icon far fa-books"></i>
                        <p>หนังสือรุ่น</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        href="{{route("career-news.index")}}" 
                        class="nav-link {{in_array(Request::route()->getName(),["career-news.index","career-news.create","career-news.view","career-news.edit"]) ? "active" : ""}}">
                        <i class="nav-icon fal fa-newspaper"></i>
                        <p>ข่าวอบรมหลักสูตรอาชีพ</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        href="{{route("album.index")}}" 
                        class="nav-link {{in_array(Request::route()->getName(),["album.index","album.create","album.view","album.edit"]) ? "active" : ""}}">
                        <i class="nav-icon fas fa-photo-video"></i>
                        <p>จัดการข้อมูลแกลเลอรี่</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        href="{{route("alumni-glory.index")}}" 
                        class="nav-link {{in_array(Request::route()->getName(),["alumni-glory.index","alumni-glory.create","alumni-glory.view","alumni-glory.edit"]) ? "active" : ""}}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>จัดการข้อมูลศิษย์เก่าดีเด่น</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        href="{{route("vender.index")}}" 
                        class="nav-link {{in_array(Request::route()->getName(),["vender.index","vender.create","vender.view","vender.edit"]) ? "active" : ""}}">
                        <i class="nav-icon far fa-user-circle"></i>
                        <p>จัดการข้อมูล Vender </p>
                    </a>
                </li>
                <li class="nav-item {{in_array(str_replace("/","",Request::route()->getPrefix()),["award","social","contact"]) ? "menu-is-opening menu-open" : ""}}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            จัดการฐานข้อมูลอื่นๆ
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a 
                                href="{{route("award.index")}}" 
                                class="nav-link {{in_array(Request::route()->getName(),["award.index","award.create","award.view","award.edit"]) ? "active" : ""}}">
                                <i class="fas fa-gift"></i>
                                <p>จัดการข้อมูลประเภทรางวัล</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a 
                                href="{{route("contact.index")}}" 
                                class="nav-link {{Request::route()->getName() == "contact.index" ? "active" : ""}}">
                                <i class="fab fa-facebook-square"></i>
                                <p>จัดการข้อมูลการติดต่อ</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a 
                                href="{{route("social.index")}}" 
                                class="nav-link {{in_array(Request::route()->getName(),["social.index","social.create","social.view","social.edit"]) ? "active" : ""}}">
                                <i class="fas fa-headset"></i>
                                <p>จัดการ Social Account</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>
