<?php
# Backend Controllers
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\BackendAdminController;
use App\Http\Controllers\Backend\BackendNotificationsController;
use App\Http\Controllers\Backend\BackendHelperController;
use App\Http\Controllers\Backend\BackendTestController;
use App\Http\Controllers\Backend\BackendProfileController;
use App\Http\Controllers\Backend\BackendArticleController;
use App\Http\Controllers\Backend\BackendArticleCommentController;
use App\Http\Controllers\Backend\BackendSiteMapController;
use App\Http\Controllers\Backend\BackendSettingController;
use App\Http\Controllers\Backend\BackendContactController;
use App\Http\Controllers\Backend\BackendCategoryController;
use App\Http\Controllers\Backend\BackendRedirectionController;
use App\Http\Controllers\Backend\BackendUserController;
use App\Http\Controllers\Backend\BackendTrafficsController;
use App\Http\Controllers\Backend\BackendPageController;
use App\Http\Controllers\Backend\BackendMenuController;
use App\Http\Controllers\Backend\BackendMenuLinkController;
use App\Http\Controllers\Backend\BackendFileController;
use App\Http\Controllers\Backend\BackendFaqController;
use App\Http\Controllers\Backend\BackendAdvertisementController;
use App\Http\Controllers\Backend\BackendContactReplyController;
use App\Http\Controllers\Backend\BackendAnnouncementController;
use App\Http\Controllers\Backend\BackendPermissionController;
use App\Http\Controllers\Backend\BackendUserPermissionController;
use App\Http\Controllers\Backend\BackendUserRoleController;
use App\Http\Controllers\Backend\BackendRoleController;
use App\Http\Controllers\Backend\BackendTagController;
use App\Http\Controllers\Backend\BackendBuilderController;
use App\Http\Controllers\Backend\BackendPluginController;
use App\Http\Controllers\Backend\BackendAnalyticsController;


# Frontend Controllers
use App\Http\Controllers\FrontController;
use App\Http\Controllers\FrontendProfileController;


use App\Http\Controllers\Auth\LoginController;
Auth::routes();





Route::get('/', [FrontController::class,'index'])->name('home');
// Temporarily disabled
// Route::get('/index2', function(){return view('front.index2');})->name('index2');


Route::prefix('dashboard')->middleware(['auth','ActiveAccount','verified'])->name('user.')->group(function () {
    Route::get('/', [FrontendProfileController::class,'dashboard'])->name('dashboard');
    Route::get('/support', [FrontendProfileController::class,'support'])->name('support');
    Route::get('/support/create-ticket', [FrontendProfileController::class,'create_ticket'])->name('create-ticket');
    Route::post('/support/create-ticket', [FrontendProfileController::class,'store_ticket'])->name('store-ticket');
    Route::get('/support/{ticket}', [FrontendProfileController::class,'ticket'])->name('ticket');
    Route::post('/support/{ticket}/reply', [FrontendProfileController::class,'reply_ticket'])->name('reply-ticket');
    Route::get('/notifications', [FrontendProfileController::class,'notifications'])->name('notifications');
    
    // Analytics routes for users
    Route::resource('analytics', BackendAnalyticsController::class, ['parameters' => ['analytics' => 'site']])->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::post('analytics/reorder', [BackendAnalyticsController::class, 'reorder'])->name('analytics.reorder');
    Route::get('analytics/{site}/tracking-code', [BackendAnalyticsController::class, 'trackingCode'])->name('analytics.tracking-code');
    Route::get('analytics/{site}/search', [BackendAnalyticsController::class, 'search'])->name('analytics.search');
    Route::post('analytics/{site}/search', [BackendAnalyticsController::class, 'searchResults'])->name('analytics.search-results');
    Route::get('analytics/{site}/members', [BackendAnalyticsController::class, 'members'])->name('analytics.members');
    Route::get('analytics/{site}/visits/{sessionId}', [BackendAnalyticsController::class, 'visitDetails'])->name('analytics.visit-details');
    Route::get('analytics/{site}/patterns', [BackendAnalyticsController::class, 'patterns'])->name('analytics.patterns');
    Route::get('analytics/{site}/advertisements', [BackendAnalyticsController::class, 'siteAdvertisements'])->name('analytics.advertisements');
    Route::get('analytics/{site}/patterns/create', [BackendAnalyticsController::class, 'createPattern'])->name('analytics.patterns.create');
    Route::get('analytics/{site}/patterns/{pattern}/edit', [BackendAnalyticsController::class, 'editPattern'])->name('analytics.patterns.edit');
    Route::post('analytics/{site}/patterns', [BackendAnalyticsController::class, 'storePattern'])->name('analytics.patterns.store');
    Route::put('analytics/{site}/patterns/{pattern}', [BackendAnalyticsController::class, 'updatePattern'])->name('analytics.patterns.update');
    Route::post('analytics/{site}/patterns/regenerate', [BackendAnalyticsController::class, 'regeneratePatterns'])->name('analytics.patterns.regenerate');
    Route::delete('analytics/{site}/patterns/{pattern}', [BackendAnalyticsController::class, 'deletePattern'])->name('analytics.patterns.delete');
    Route::post('analytics/{site}/invite', [BackendAnalyticsController::class, 'sendInvitation'])->name('analytics.invite');
    Route::post('analytics/{site}/remove-member', [BackendAnalyticsController::class, 'removeMember'])->name('analytics.remove-member');
    Route::delete('analytics/invitations/{invitation}', [BackendAnalyticsController::class, 'cancelInvitation'])->name('analytics.cancel-invitation');
    
    // Public invitation routes
    Route::post('analytics/invitations/{token}/accept', [BackendAnalyticsController::class, 'acceptInvitation'])->name('analytics.accept-invitation');
    Route::post('analytics/invitations/{token}/reject', [BackendAnalyticsController::class, 'rejectInvitation'])->name('analytics.reject-invitation');
    
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/settings',[FrontendProfileController::class,'profile_edit'])->name('edit');
        Route::put('/update',[FrontendProfileController::class,'profile_update'])->name('update');
        Route::put('/update-password',[FrontendProfileController::class,'profile_update_password'])->name('update-password');
        Route::put('/update-email',[FrontendProfileController::class,'profile_update_email'])->name('update-email');
    });
});




Route::prefix('admin')->middleware(['auth','ActiveAccount'])->name('admin.')->group(function () {

    Route::get('/',[BackendAdminController::class,'index'])->name('index');
    Route::middleware('auth')->group(function () {

        Route::get('plugins/{plugin}/builder',[BackendPluginController::class,'builder_edit'])->name('plugins.builder-edit');
        Route::post('plugins/{plugin}/builder',[BackendPluginController::class,'builder_update'])->name('plugins.builder-update');

        Route::post('/plugins/{plugin}/push',[BackendPluginController::class,'push'])->name('plugins.push');
        Route::resource('plugins',BackendPluginController::class)->only(['index','store','update','show']);


        Route::resource('announcements',BackendAnnouncementController::class);
        Route::resource('files',BackendFileController::class);
        Route::post('files/upload', [BackendFileController::class, 'upload'])->name('files.upload');
        Route::get('files/{file}/url', [BackendFileController::class, 'getUrl'])->name('files.url');
        Route::post('contacts/resolve',[BackendContactController::class,'resolve'])->name('contacts.resolve');
        Route::resource('contacts',BackendContactController::class);
        Route::resource('menus',BackendMenuController::class);
        Route::get('users/{user}/access',[BackendUserController::class,'access'])->name('users.access');
        Route::resource('users',BackendUserController::class);
        Route::resource('roles',BackendRoleController::class);
        Route::get('user-roles/{user}',[BackendUserRoleController::class,'index'])->name('users.roles.index');
        Route::put('user-roles/{user}',[BackendUserRoleController::class,'update'])->name('users.roles.update');
        Route::resource('articles',BackendArticleController::class);
        Route::post('article-comments/change_status',[BackendArticleCommentController::class,'change_status'])->name('article-comments.change_status');
        Route::resource('article-comments',BackendArticleCommentController::class);
        Route::get('pages/{page}/builder',[BackendPageController::class,'builder_edit'])->name('pages.builder-edit');
        Route::post('pages/{page}/builder',[BackendPageController::class,'builder_update'])->name('pages.builder-update');
        Route::resource('pages',BackendPageController::class);
        Route::resource('builders',BackendBuilderController::class);
        Route::resource('tags',BackendTagController::class);
        Route::resource('contact-replies',BackendContactReplyController::class);
        Route::get('advertisements/{advertisement}/stats',[BackendAdvertisementController::class,'stats'])->name('advertisements.stats');
        Route::resource('advertisements',BackendAdvertisementController::class);
        Route::post('faqs/order',[BackendFaqController::class,'order'])->name('faqs.order');
        Route::resource('faqs',BackendFaqController::class);
        Route::post('menu-links/get-type',[BackendMenuLinkController::class,'getType'])->name('menu-links.get-type');
        Route::post('menu-links/order',[BackendMenuLinkController::class,'order'])->name('menu-links.order');
        Route::resource('menu-links',BackendMenuLinkController::class);
        Route::resource('categories',BackendCategoryController::class);
        Route::resource('redirections',BackendRedirectionController::class);
        Route::get('traffics',[BackendTrafficsController::class,'index'])->name('traffics.index');
        Route::get('traffics/logs',[BackendTrafficsController::class,'logs'])->name('traffics.logs');
        Route::get('error-reports',[BackendTrafficsController::class,'error_reports'])->name('traffics.error-reports');
        Route::get('error-reports/{report}',[BackendTrafficsController::class,'error_report'])->name('traffics.error-report');
        
        // Analytics routes for admin dashboard
        // IMPORTANT: Place specific routes BEFORE resource route to avoid route conflicts
        Route::get('analytics/websites/all', [BackendAnalyticsController::class, 'websites'])->name('analytics.websites');
        Route::post('analytics/reorder', [BackendAnalyticsController::class, 'reorder'])->name('analytics.reorder');
        Route::resource('analytics', BackendAnalyticsController::class, ['parameters' => ['analytics' => 'site']])->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::get('analytics/{site}/tracking-code', [BackendAnalyticsController::class, 'trackingCode'])->name('analytics.tracking-code');
        Route::get('analytics/{site}/search', [BackendAnalyticsController::class, 'search'])->name('analytics.search');
        Route::post('analytics/{site}/search', [BackendAnalyticsController::class, 'searchResults'])->name('analytics.search-results');
        Route::get('analytics/{site}/members', [BackendAnalyticsController::class, 'members'])->name('analytics.members');
        Route::get('analytics/{site}/visits/{sessionId}', [BackendAnalyticsController::class, 'visitDetails'])->name('analytics.visit-details');
        Route::get('analytics/{site}/patterns', [BackendAnalyticsController::class, 'patterns'])->name('analytics.patterns');
        Route::get('analytics/{site}/advertisements', [BackendAnalyticsController::class, 'siteAdvertisements'])->name('analytics.advertisements');
        Route::get('analytics/{site}/patterns/create', [BackendAnalyticsController::class, 'createPattern'])->name('analytics.patterns.create');
        Route::get('analytics/{site}/patterns/{pattern}/edit', [BackendAnalyticsController::class, 'editPattern'])->name('analytics.patterns.edit');
        Route::post('analytics/{site}/patterns', [BackendAnalyticsController::class, 'storePattern'])->name('analytics.patterns.store');
        Route::put('analytics/{site}/patterns/{pattern}', [BackendAnalyticsController::class, 'updatePattern'])->name('analytics.patterns.update');
        Route::post('analytics/{site}/patterns/regenerate', [BackendAnalyticsController::class, 'regeneratePatterns'])->name('analytics.patterns.regenerate');
        Route::delete('analytics/{site}/patterns/{pattern}', [BackendAnalyticsController::class, 'deletePattern'])->name('analytics.patterns.delete');
        Route::post('analytics/{site}/invite', [BackendAnalyticsController::class, 'sendInvitation'])->name('analytics.invite');
        Route::post('analytics/{site}/remove-member', [BackendAnalyticsController::class, 'removeMember'])->name('analytics.remove-member');
        Route::delete('analytics/invitations/{invitation}', [BackendAnalyticsController::class, 'cancelInvitation'])->name('analytics.cancel-invitation');
        Route::post('analytics/invitations/{token}/accept', [BackendAnalyticsController::class, 'acceptInvitation'])->name('analytics.accept-invitation');
        Route::post('analytics/invitations/{token}/reject', [BackendAnalyticsController::class, 'rejectInvitation'])->name('analytics.reject-invitation');
        
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/',[BackendSettingController::class,'index'])->name('index');
            Route::put('/update',[BackendSettingController::class,'update'])->name('update');
        });
    });
    Route::prefix('data')->name('data.')->group(function(){
        Route::post('/',[BackendHelperController::class,'get_data'])->name('index');
        Route::post('/load',[BackendHelperController::class,'load_data'])->name('load');
    });
    Route::prefix('upload')->name('upload.')->group(function(){
        Route::post('/image',[BackendHelperController::class,'upload_image'])->name('image');
        Route::post('/file',[BackendHelperController::class,'upload_file'])->name('file');
        Route::post('/remove-file',[BackendHelperController::class,'remove_files'])->name('remove-file');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',[BackendProfileController::class,'index'])->name('index');
        Route::get('/edit',[BackendProfileController::class,'edit'])->name('edit');
        Route::put('/update',[BackendProfileController::class,'update'])->name('update');
        Route::put('/update-password',[BackendProfileController::class,'update_password'])->name('update-password');
        Route::put('/update-email',[BackendProfileController::class,'update_email'])->name('update-email');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',[BackendNotificationsController::class,'index'])->name('index');
        Route::get('/ajax',[BackendNotificationsController::class,'ajax'])->name('ajax');
        Route::post('/see',[BackendNotificationsController::class,'see'])->name('see');
        Route::get('/create',[BackendNotificationsController::class,'create'])->name('create');
        Route::post('/create',[BackendNotificationsController::class,'store'])->name('store');
    });

});

Route::get('/login/google/redirect', [LoginController::class,'redirect_google']);
Route::get('/login/google/callback', [LoginController::class,'callback_google']);
Route::get('/login/facebook/redirect', [LoginController::class,'redirect_facebook']);
Route::get('/login/facebook/callback', [LoginController::class,'callback_facebook']);


Route::get('blocked',[BackendHelperController::class,'blocked_user'])->name('blocked');
Route::get('ads.txt',[BackendHelperController::class,'ads_txt']);
Route::get('robots.txt',[BackendHelperController::class,'robots']);
Route::get('manifest.json',[BackendHelperController::class,'manifest'])->name('manifest');
Route::get('sitemap.xml',[BackendSiteMapController::class,'sitemap']);
Route::get('sitemaps/links',[BackendSiteMapController::class,'custom_links']);
Route::get('sitemaps/{name}/{page}/sitemap.xml',[BackendSiteMapController::class,'viewer']);


// Temporarily disabled frontend routes
// Route::view('contact','front.pages.contact')->name('contact');
// Route::get('page/{page}',[FrontController::class,'page'])->name('page.show');
// Route::get('tag/{tag}',[FrontController::class,'tag'])->name('tag.show');
// Route::get('category/{category}',[FrontController::class,'category'])->name('category.show');
// Route::get('article/{article}',[FrontController::class,'article'])->name('article.show');
// Route::get('blog',[FrontController::class,'blog'])->name('blog');
// Route::post('contact',[FrontController::class,'contact_post'])->name('contact-post');
// Route::post('comment',[FrontController::class,'comment_post'])->name('comment-post');






############## For Testing Routes ##############
Route::get('/test',[BackendTestController::class,'test'])->name('test');
Route::get('/test/url-patterns',[BackendTestController::class,'index'])->name('test.url-patterns')->middleware('auth');