<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AlumniAffairsController;
use App\Http\Controllers\AlumniGloryController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\CareerNewsController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\PressReleaseController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\SpiritCoinActivityController;
use App\Http\Controllers\SpiritCoinController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenderController;
use App\Http\Controllers\YearBookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(["auth:token"])
    ->group(function () {

        Route::get("/", [IndexController::class, "index"]);

        Route::prefix("press-release")
            ->name("press-release.")
            ->group(function () {
                Route::get("/", [PressReleaseController::class, "index"])->name("index");
                Route::get('create', [PressReleaseController::class, "create"])->name("create");
                Route::get("{training_new}/view", [PressReleaseController::class, "show"])->name("view");
                Route::get("{training_new}/edit", [PressReleaseController::class, "edit"])->name("edit");
                Route::post("/", [PressReleaseController::class, "store"])->name("store");
                Route::post("{training_new}/update", [PressReleaseController::class, "update"])->name("update");
                Route::post("{training_new}/delete", [PressReleaseController::class, "destroy"])->name("destroy");
                Route::post("{training_new}/show-homepage", [PressReleaseController::class, "showHomePage"])->name("show_homepage");
                Route::post("{training_new}/priority", [PressReleaseController::class, "priority"])->name("priority");
                Route::post("{training_new}/approve", [PressReleaseController::class, "approve"])->name("approve");
                Route::post("{training_new}/restore", [PressReleaseController::class, "restore"])->name("restore");
            });

        Route::prefix("event-news")
            ->name("event-news.")
            ->group(function () {
                Route::get("/", [ActivityController::class, "index"])->name("index");
                Route::get('create', [ActivityController::class, "create"])->name("create");
                Route::get("{activity}/view", [ActivityController::class, "show"])->name("view");
                Route::get("{activity}/edit", [ActivityController::class, "edit"])->name("edit");
                Route::get("{activity}/book-room", [ActivityController::class, "book"])->name("book-room");
                Route::post("/", [ActivityController::class, "store"])->name("store");
                Route::post("{activity}/update", [ActivityController::class, "update"])->name("update");
                Route::post("{activity}/delete", [ActivityController::class, "destroy"])->name("destroy");
                Route::post("{activity}/show-homepage", [ActivityController::class, "showHomePage"])->name("show_homepage");
                Route::post("{activity}/priority", [ActivityController::class, "priority"])->name("priority");
                Route::post("{activity}/approve", [ActivityController::class, "approve"])->name("approve");
                Route::post("{activity}/restore", [ActivityController::class, "restore"])->name("restore");
                Route::post("{activity}/book-room", [ActivityController::class, "booking"])->name("book-store");
            });

        Route::prefix("recruitment")
            ->name("recruitment.")
            ->group(function () {
                Route::get("/", [RecruitmentController::class, "index"])->name("index");
                Route::get('create', [RecruitmentController::class, "create"])->name("create");
                Route::get("{recruitment}/view", [RecruitmentController::class, "show"])->name("view");
                Route::get("{recruitment}/edit", [RecruitmentController::class, "edit"])->name("edit");
                Route::post("/", [RecruitmentController::class, "store"])->name("store");
                Route::post("{recruitment}/update", [RecruitmentController::class, "update"])->name("update");
                Route::post("{recruitment}/delete", [RecruitmentController::class, "destroy"])->name("destroy");
                Route::post("{recruitment}/active", [RecruitmentController::class, "active"])->name("active");
                Route::post("{recruitment}/approve", [RecruitmentController::class, "approve"])->name("approve");
                Route::post("{recruitment}/restore", [RecruitmentController::class, "restore"])->name("restore");
            });

        Route::prefix("alumni-affairs")
            ->name("alumni-affairs.")
            ->group(function () {
                Route::get("/", [AlumniAffairsController::class, "index"])->name("index");
                Route::get('create', [AlumniAffairsController::class, "create"])->name("create");
                Route::get("{affairs}/view", [AlumniAffairsController::class, "show"])->name("view");
                Route::get("{affairs}/edit", [AlumniAffairsController::class, "edit"])->name("edit");
                Route::post("/", [AlumniAffairsController::class, "store"])->name("store");
                Route::post("{affairs}/update", [AlumniAffairsController::class, "update"])->name("update");
                Route::post("{affairs}/delete", [AlumniAffairsController::class, "destroy"])->name("destroy");
                Route::post("{affairs}/approve", [AlumniAffairsController::class, "approve"])->name("approve");
                Route::post("{affairs}/restore", [AlumniAffairsController::class, "restore"])->name("restore");
            });

        Route::prefix("spirit-coin")
            ->name("spirit-coin.")
            ->group(function () {
                Route::get("/", [SpiritCoinController::class, "index"])->name("index");
                Route::get('create', [SpiritCoinController::class, "create"])->name("create");
                Route::get("{coin}/view", [SpiritCoinController::class, "show"])->name("view");
                Route::get("{coin}/edit", [SpiritCoinController::class, "edit"])->name("edit");
                Route::post("/", [SpiritCoinController::class, "store"])->name("store");
                Route::post("{coin}/update", [SpiritCoinController::class, "update"])->name("update");
                Route::post("{coin}/delete", [SpiritCoinController::class, "destroy"])->name("destroy");
                Route::post("{coin}/approve", [SpiritCoinController::class, "approve"])->name("approve");
                Route::post("{coin}/restore", [SpiritCoinController::class, "restore"])->name("restore");
                Route::post("{coin}/active", [SpiritCoinController::class, "active"])->name("active");
                Route::post("{coin}/priority", [SpiritCoinController::class, "priority"])->name("priority");
            });

        Route::prefix("spirit-coin-activity")
            ->name("spirit-coin-activity.")
            ->group(function () {
                Route::get("/", [SpiritCoinActivityController::class, "index"])->name("index");
                Route::get('create', [SpiritCoinActivityController::class, "create"])->name("create");
                Route::get("{coin}/view", [SpiritCoinActivityController::class, "show"])->name("view");
                Route::get("{coin}/edit", [SpiritCoinActivityController::class, "edit"])->name("edit");
                Route::post("/", [SpiritCoinActivityController::class, "store"])->name("store");
                Route::post("{coin}/update", [SpiritCoinActivityController::class, "update"])->name("update");
                Route::post("{coin}/delete", [SpiritCoinActivityController::class, "destroy"])->name("destroy");
                Route::post("{coin}/approve", [SpiritCoinActivityController::class, "approve"])->name("approve");
                Route::post("{coin}/restore", [SpiritCoinActivityController::class, "restore"])->name("restore");
            });

        Route::prefix("year-book")
            ->name("year-book.")
            ->group(function () {
                Route::get("/", [YearBookController::class, "index"])->name("index");
                Route::get('create', [YearBookController::class, "create"])->name("create");
                Route::get("{book}/view", [YearBookController::class, "show"])->name("view");
                Route::get("{book}/edit", [YearBookController::class, "edit"])->name("edit");
                Route::post("/", [YearBookController::class, "store"])->name("store");
                Route::post("{book}/update", [YearBookController::class, "update"])->name("update");
                Route::post("{book}/delete", [YearBookController::class, "destroy"])->name("destroy");
                Route::post("{book}/approve", [YearBookController::class, "approve"])->name("approve");
                Route::post("{book}/restore", [YearBookController::class, "restore"])->name("restore");
            });

        Route::prefix("career-news")
            ->name("career-news.")
            ->group(function () {
                Route::get("/", [CareerNewsController::class, "index"])->name("index");
                Route::get('create', [CareerNewsController::class, "create"])->name("create");
                Route::get("{news}/view", [CareerNewsController::class, "show"])->name("view");
                Route::get("{news}/edit", [CareerNewsController::class, "edit"])->name("edit");
                Route::post("/", [CareerNewsController::class, "store"])->name("store");
                Route::post("{news}/update", [CareerNewsController::class, "update"])->name("update");
                Route::post("{news}/delete", [CareerNewsController::class, "destroy"])->name("destroy");
                Route::post("{news}/approve", [CareerNewsController::class, "approve"])->name("approve");
                Route::post("{news}/restore", [CareerNewsController::class, "restore"])->name("restore");
                Route::post("{news}/priority", [CareerNewsController::class, "priority"])->name("priority");
            });

        Route::prefix("album")
            ->name("album.")
            ->group(function () {
                Route::get("/", [AlbumController::class, "index"])->name("index");
                Route::get('create', [AlbumController::class, "create"])->name("create");
                Route::get("{album}/view", [AlbumController::class, "show"])->name("view");
                Route::get("{album}/edit", [AlbumController::class, "edit"])->name("edit");
                Route::post("/", [AlbumController::class, "store"])->name("store");
                Route::post("{album}/update", [AlbumController::class, "update"])->name("update");
                Route::post("{album}/delete", [AlbumController::class, "destroy"])->name("destroy");
                Route::post("{album}/approve", [AlbumController::class, "approve"])->name("approve");
                Route::post("{album}/restore", [AlbumController::class, "restore"])->name("restore");
                Route::post("{album}/active", [AlbumController::class, "active"])->name("active");
                Route::post('{album}/upload-gallery', [AlbumController::class, "galleryUpload"])->name("gallery.upload");
                Route::post('{gallery}/remove-gallery', [AlbumController::class, "galleryDestroy"])->name("gallery.destroy");
                Route::post('{gallery}/cover-page', [AlbumController::class, "galleryCoverPage"])->name("gallery.cover_page");
            });

        Route::prefix("alumni-glory")
            ->name("alumni-glory.")
            ->group(function () {
                Route::get("/", [AlumniGloryController::class, "index"])->name("index");
                Route::get('create', [AlumniGloryController::class, "create"])->name("create");
                Route::get("{alumni}/view", [AlumniGloryController::class, "show"])->name("view");
                Route::get("{alumni}/edit", [AlumniGloryController::class, "edit"])->name("edit");
                Route::post("/", [AlumniGloryController::class, "store"])->name("store");
                Route::post("{alumni}/update", [AlumniGloryController::class, "update"])->name("update");
                Route::post("{alumni}/delete", [AlumniGloryController::class, "destroy"])->name("destroy");
                Route::post("{alumni}/approve", [AlumniGloryController::class, "approve"])->name("approve");
                Route::post("{alumni}/restore", [AlumniGloryController::class, "restore"])->name("restore");
                Route::post("{alumni}/active", [AlumniGloryController::class, "active"])->name("active");
            });

        Route::prefix("award")
            ->name("award.")
            ->group(function () {
                Route::get("/", [AwardController::class, "index"])->name("index");
                Route::get('create', [AwardController::class, "create"])->name("create");
                Route::get("{award}/view", [AwardController::class, "show"])->name("view");
                Route::get("{award}/edit", [AwardController::class, "edit"])->name("edit");
                Route::post("/", [AwardController::class, "store"])->name("store");
                Route::post("{award}/update", [AwardController::class, "update"])->name("update");
                Route::post("{award}/delete", [AwardController::class, "destroy"])->name("destroy");
                Route::post("{award}/active", [AwardController::class, "active"])->name("active");
            });

        Route::prefix("social")
            ->name("social.")
            ->group(function () {
                Route::get("/", [SocialAccountController::class, "index"])->name("index");
                Route::get('create', [SocialAccountController::class, "create"])->name("create");
                Route::get("{social}/view", [SocialAccountController::class, "show"])->name("view");
                Route::get("{social}/edit", [SocialAccountController::class, "edit"])->name("edit");
                Route::post("/", [SocialAccountController::class, "store"])->name("store");
                Route::post("{social}/update", [SocialAccountController::class, "update"])->name("update");
                Route::post("{social}/delete", [SocialAccountController::class, "destroy"])->name("destroy");
                Route::post("{social}/active", [SocialAccountController::class, "active"])->name("active");
                Route::post("{social}/priority", [SocialAccountController::class, "priority"])->name("priority");
            });

        Route::prefix("vender")
            ->name("vender.")
            ->group(function () {
                Route::get("/", [VenderController::class, "index"])->name("index");
                Route::get('create', [VenderController::class, "create"])->name("create");
                Route::get("{vender}/view", [VenderController::class, "show"])->name("view");
                Route::get("{vender}/edit", [VenderController::class, "edit"])->name("edit");
                Route::post("/", [VenderController::class, "store"])->name("store");
                Route::post("{vender}/update", [VenderController::class, "update"])->name("update");
                Route::post("{vender}/delete", [VenderController::class, "destroy"])->name("destroy");
                Route::post("{vender}/approve", [VenderController::class, "approve"])->name("approve");
                Route::post("{vender}/active", [VenderController::class, "active"])->name("active");
                Route::post("{vender}/restore", [VenderController::class, "restore"])->name("restore");
            });

        Route::prefix("contact")
            ->name("contact.")
            ->group(function () {
                Route::get("/", [ConfigurationController::class, "contact"])->name("index");
                Route::post("/", [ConfigurationController::class, "store"])->name("store");
            });

        Route::prefix("role")
            ->name("role.")
            ->group(function () {
                Route::get("/", [RoleController::class, "index"])->name("index");
                Route::get("create", [RoleController::class, "create"])->name("create");
                Route::get("{role}/view", [RoleController::class, "show"])->name("view");
                Route::get("{role}/edit", [RoleController::class, "edit"])->name("edit");
                Route::post("/", [RoleController::class, "store"])->name("store");
                Route::post("{role}/update", [RoleController::class, "update"])->name("update");
                Route::post("{role}/delete", [RoleController::class, "destroy"])->name("destroy");
            });

        Route::prefix("user")
            ->name("user.")
            ->group(function () {
                Route::get("/", [UserController::class, "index"])->name("index");
                Route::get("{user}/view", [UserController::class, "show"])->name("view");
                Route::post("{user}/update", [UserController::class, "update"])->name("update");
            });

        Route::prefix("service")
            ->name("service.")
            ->group(function () {
                // room
                // group
                Route::get("room-group", [ServiceController::class, "roomgroup"])->name("room.group");
                // sub group required group id
                Route::get("room-sub-group", [ServiceController::class, "roomsubgroup"])->name("room.subgroup");
                // room required sub group id
                Route::get("room", [ServiceController::class, "room"])->name("room");
            });
    });
