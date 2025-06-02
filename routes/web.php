<?php

use Illuminate\Support\Facades\Route;
Route::get('/excel','Controller@export');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('employee/logout', 'Auth\EmployeeLoginController@logout')->name('employee.logout');
Route::post('/update-login-status', 'Auth\LoginController@update_login_status')->name('update-login-status');

Route::namespace('Auth')->name('auth.')->middleware('guest')->group(function () {
    Route::get('/login', 'LoginController@index')->name('login');
    Route::post('/login', 'LoginController@processLogin');
    Route::post('/forgot-password', 'LoginController@forgotPassword')->name('forgot_password');


    Route::get('emp/login', 'EmployeeLoginController@index')->name('employeeLogin');
    Route::post('emp/login', 'EmployeeLoginController@login_process');
    Route::post('emp/forgot-password', 'EmployeeForgotPasswordController@recover_process')->name('employee_forgot_password');

});

Route::get('/home', 'Auth\HomeController@index')->middleware('auth')->name('goToHome');


// Employee Route starts
Route::namespace('Employee')->name('employee.')->prefix('emp')->middleware(['RoleEmployee'])->group(function(){

    Route::get('/profile', 'ProfileController@index')->name('profile');
    Route::post('/profile-update', 'ProfileController@profileUpdate')->name('profileUpdate');

    Route::get('', 'HomeController@index')->name('index');
    Route::get('My-Users', 'UsersController@all_users_info')->name('user_list');
    Route::get('low-balance-user-list', 'UsersController@low_balance_users_list')->name('low_balance_users_list');

    Route::get('/transaction-history/{user_id}', 'UsersController@transaction_history_particular')->name('transaction_history');

    Route::get('change-password', 'ChangePasswordController@change_password')->name('change_password');
    Route::post('change-password', 'ChangePasswordController@change_password_process')->name('change_password');

    Route::group(['prefix' => 'package' , 'namespace' => 'Flexiload' , 'as' => 'package.'],function(){
        Route::get('package-list','LoadController@package_list')->name('package-list');
        Route::post('package-flexiload', 'LoadController@packageFormProcess')->name('package-buy');
        Route::post('show-packages', 'LoadController@showPackagesByAjax')->name('show-packages-by-ajax');

        Route::get('package-history', ['uses' => 'LoadController@package_history'])->name('package-history');
    });
});




/*----------------------------------------------
| ----------Start Main Admin/root route------------
| -----------------------------------------------
*/

Route::namespace('Admin')->name('admin.')->middleware(['auth', 'RoleRoot'])->prefix('root')->group(function () {

    // ---show dashboard page
    Route::get('/', 'HomeController@index')->name('index');
    Route::get('data-count','HomeController@getCount')->name('data-count');
    Route::get('data-cost','HomeController@getCost')->name('data-cost');
    Route::get('/change-password', 'ProfileController@showChangePasswordForm')->name('change-password');
    Route::post('/change-password', 'ProfileController@updatePassword');
    Route::get('/all-loggedin-users', 'HomeController@loggedInUsers' )->name('loggedInUsers');

    Route::get('/pending-sms-campaigns', 'SmsCampaignController@showPendingSmsCampaigns')->name('pending-campaign-sms');
    Route::get('/accept-sms-campaigns/{id}', 'SmsCampaignController@acceptPendingSmsCampaigns')->name('accept-campaign-sms');

    Route::get('/accept-dynamic-campaigns/{id}', 'SmsCampaignController@acceptDynamicCampaigns')->name('accept-dynamic-sms');


    Route::get('/reject-sms-campaigns/{id}', 'SmsCampaignController@rejectPendingSmsCampaigns')->name('reject-campaign-sms');

    Route::get('api-permission','ApiPermissionController@api_user')->name('api-permission');
    Route::get('api-permission-active/{id}','ApiPermissionController@api_user_active')->name('api-permission-active');
    Route::get('api-permission-suspend/{id}','ApiPermissionController@api_user_suspend')->name('api-permission-suspend');

    Route::get('api-add','ApiAddController@api_add')->name('api-add');
    Route::post('api-add-insert','ApiAddController@api_add_insert')->name('api-add-insert');
    Route::get('api-status-active/{id}','ApiAddController@api_status_active')->name('api-status-active');
    Route::get('api-status-suspend/{id}','ApiAddController@api_status_suspend')->name('api-status-suspend');

    Route::get('dynamic-permission','DynamicPermissionController@dynamic_user')->name('dynamic-permission');
    Route::get('dynamic-permission-active/{id}','DynamicPermissionController@dynamic_user_active')->name('dynamic-permission-active');
    Route::get('dynamic-permission-suspend/{id}','DynamicPermissionController@dynamic_user_suspend')->name('dynamic-permission-suspend');

    Route::get('/system-configuration', 'SystemConfigurationController@showSystemConfiguration')->name('show-system-configuration');
    Route::post('/system-configuration', 'SystemConfigurationController@updateSystemConfiguration')->name('update-system-configuration');

    /* ----all route of reseller in admin panel----- */
    Route::prefix('reseller')->name('reseller.')->group(function () {
        Route::get('/', 'ResellerController@index')->name('index');
        Route::get('/create', 'ResellerController@create')->name('create');
        Route::post('/store', 'ResellerController@store')->name('store');
        Route::post('/update/{id}', 'ResellerController@update')->name('update')->where('id', '[0-9]+');
        Route::get('/edit/{id}', 'ResellerController@edit')->name('edit')->where('id', '[0-9]+');
        Route::get('/transaction-history/{id}', 'BalanceController@show')->name('transactionHistory')->where('id', '[0-9]+');
        Route::get('/price-view/{id}', 'SmsRateController@edit')->name('priceView')->where('id', '[0-9]+');
        Route::post('/price/update/{id}', 'SmsRateController@update')->name('priceView.update')->where('id', '[0-9]+');
        Route::get('/tree-view', 'ResellerController@treeView')->name('tree');
        Route::get('/limit-apply', 'ResellerLimitController@limitApplyForm')->name('limitApply');
        Route::post('/limit/update/{id}', 'ResellerLimitController@limitUpdateForm')->name('limitUpdate');
        Route::get('/suspend/{id}', 'ResellerController@suspend')->name('suspend');
        Route::get('/active/{id}', 'ResellerController@active')->name('active');

        Route::get('/go-to-user/{id}', 'ResellerController@goToThisAccount')->name('goToThisAccount');



        Route::get('/employee-limit', 'EmployeeController@employee_limit_form_view')->name('employee_limit');
        Route::post('/employee-limit', 'EmployeeController@employee_limit_process');
    });

    Route::group(['prefix' => 'english' , 'as' => 'english.'],function(){
        Route::get('route-registers','DynamicPermissionController@route_registers')->name('route-registers');
        Route::post('route-register-store','DynamicPermissionController@route_register_store')->name('route-register-store');
        Route::get('assign-route','DynamicPermissionController@assign_route')->name('assign-route');
        Route::post('assign-route-store','DynamicPermissionController@assign_route_store')->name('assign-route-store');

        Route::get('route-edit/{id}','DynamicPermissionController@route_edit')->name('route-edit');
        Route::post('route-update/{id}','DynamicPermissionController@route_update')->name('route-update');
        Route::get('route-delete/{id}','DynamicPermissionController@route_delete')->name('route-delete');
        Route::get('assign-route-edit/{id}','DynamicPermissionController@assign_route_edit')->name('assign-route-edit');
        Route::post('assign-route-update/{id}','DynamicPermissionController@assign_route_update')->name('assign-route-update');
        Route::get('assign-route-delete/{id}','DynamicPermissionController@assigned_route_delete')->name('assign-route-delete');
    });


    /* ----end route of reseller in admin panel----- */


    /* ----all route of reseller in admin panel----- */
    Route::prefix('employee')->name('employee.')->group(function () {
        Route::get('/', 'EmployeeController@index')->name('index');
        Route::view('/create', 'admin.employee.employee_registration')->name('create');
        Route::post('/store', 'EmployeeController@store')->name('store');
        Route::post('/update/{id}', 'EmployeeController@update')->name('update')->where('id', '[0-9]+');
        Route::get('/edit/{id}', 'EmployeeController@edit')->name('edit')->where('id', '[0-9]+');

    });
    /* ----end route of reseller in admin panel----- */


    /* ----all route of sender id in admin panel----- */
    Route::prefix('senderID')->name('senderID.')->group(function () {
        Route::get('/', 'SenderIDController@index')->name('index');
        Route::get('/add', 'SenderIDController@create')->name('create');
        Route::post('/add', 'SenderIDController@store')->name('create');
        Route::patch('/update', 'SenderIDController@update')->name('update');
        Route::get('/update-status/{id}', 'SenderIDController@updateStatus')->name('update_status')->where('id', '[0-9]+');
        Route::get('/delivery-senderID', 'SenderIDController@deliverySenderIDList')->name('deliverySenderIDList');
        Route::get('/delivery-senderID/check/{id}', 'SenderIDController@checkDeliverySenderID')->name('checkDeliverySenderID')->where('id', '[0-9]+');
        Route::post('/delivery-senderID/check/{id}', 'SenderIDController@updateDeliverySenderId')->where('id', '[0-9]+');
        Route::get('/delivery-senderID/check/{id}/{operator}/{number}', 'SenderIDController@panelCheckDeliverySenderId')->where('id', '[0-9]+')->name('panelCheckDeliverySenderID');

        Route::prefix('non-masking')->name('nonMaskingSenderID.')->group(function () {
            Route::get('/', 'NonMaskingSenderIDController@index')->name('index');
            Route::post('/store', 'NonMaskingSenderIDController@store')->name('store');
            Route::get('/edit/{id}', 'NonMaskingSenderIDController@edit')->name('edit')->where('id', '[0-9]+');
            Route::post('/update/{id}', 'NonMaskingSenderIDController@update')->name('update')->where('id', '[0-9]+');
            Route::get('/delete/{id}', 'NonMaskingSenderIDController@delete')->name('delete')->where('id', '[0-9]+');
        });

        Route::prefix('user-senderID')->name('userSenderID.')->group(function () {
            Route::get('/', 'UserSenderIDController@index')->name('index');
            Route::post('/store', 'UserSenderIDController@store')->name('store');
            Route::get('/edit/{id}', 'UserSenderIDController@edit')->name('edit')->where('id', '[0-9]+');
            Route::post('/update/{id}', 'UserSenderIDController@update')->name('update')->where('id', '[0-9]+');
            Route::get('/delete/{id}', 'UserSenderIDController@delete')->name('delete')->where('id', '[0-9]+');
        });

    });
    /* ----end route of sender id in admin panel----- */


    /* ----all route of virtual number in admin panel----- */
    Route::prefix('virtual-number')->name('virtualNumber.')->group(function () {
        Route::get('/', 'VirtualNumberController@index')->name('index');
        Route::get('/add', 'VirtualNumberController@create')->name('create');
        Route::post('/store', 'VirtualNumberController@store')->name('store');
        Route::get('/edit/{id}', 'VirtualNumberController@edit')->name('edit');
        Route::post('/update/{id}', 'VirtualNumberController@update')->name('update');
        Route::get('/delete/{id}', 'VirtualNumberController@delete')->name('delete');
        Route::get('/balance-query/{id}', 'VirtualNumberController@balanceCheck')->name('balance_query');
    });
    /* ----end route of virtual number in admin panel----- */


    /* ----all route of balance in admin panel----- */
    Route::prefix('balance')->name('balance.')->group(function () {
        Route::prefix('credit')->name('credit.')->group(function () {
            Route::get('/', 'BalanceController@cdtCreate')->name('create');
            Route::post('/store', 'BalanceController@cdtStore')->name('store');
        });

        Route::prefix('debit')->name('debit.')->group(function () {
            Route::get('/', 'BalanceController@dbtCreate')->name('create');
            Route::post('/store', 'BalanceController@dbtStore')->name('store');
        });
    });
    /* ----end route of balance in admin panel----- */


    /* ----all route of category contact in admin panel----- */
    Route::prefix('categoryContact')->name('categoryContact.')->group(function () {
        Route::get('/', 'CatContactController@index')->name('index');
        Route::post('/store', 'CatContactController@storeCategory')->name('storeCategory');
        Route::post('/edit', 'CatContactController@updateCategory')->name('updateCategory');
        Route::get('/delete/{id}', 'CatContactController@deleteCategory')->name('deleteCategory')->where('id', '[0-9]+');
        Route::get('/{slug}', 'CatContactController@show')->name('show');
        Route::post('/storeContact', 'CatContactController@storeContact')->name('storeContact');
        Route::post('/importContact', 'CatContactController@importContact')->name('importContact');
        Route::post('/updateContact', 'CatContactController@updateContact')->name('updateContact');
        Route::get('/{slug}/delete/{id}', 'CatContactController@deleteContact')->name('deleteContact');
    });

// Root Flexiload routes
    Route::group(['prefix' => 'flexiload', 'as' => 'flexiload.', 'namespace' => 'Flexiload'], function(){
        Route::get('allUsers', ['uses' => 'LoadController@index'])->name('allUsers');

        Route::post('edit', ['uses' => 'LoadController@customizeLoadInfo'])->name('customize');
        Route::get('active-inactive', ['uses' => 'LoadController@makeActiveInactive'])->name('activeInactive');


        Route::post('add_package', ['uses' => 'LoadController@addPackage'])->name('addPackage');
        Route::post('edit_package', ['uses' => 'LoadController@editPackage'])->name('editPackage');

        Route::get('all-resellers-comissions', ['uses'=>'LoadController@setComissionsView'])->name('setComissions');
        Route::post('all-resellers-comissions', ['uses'=>'LoadController@setComissions']);

        Route::get('allPackages', ['uses' => 'LoadController@viewAllPackages'])->name('allPackages');
        Route::get('reload-load/{id}', 'LoadController@reload_load')->name('reload-load');
        Route::get('reload-load-all', 'LoadController@reload_all')->name('reload-load-all');
        Route::get('set-trx-id', 'LoadController@set_trx_id_page')->name('set-trx-page');
        Route::post('update-trx-id/{id}','LoadController@update_trx_id')->name('update-trx-id');

        Route::get('balance-enquiry','LoadController@balance_enquiry')->name('balance-enquiry');
        Route::get('load-message','LoadController@load_message')->name('load-message');

    });
    Route::group(['prefix' => 'reports' , 'as' => 'reports.'],function(){
        Route::get('sms-flexi-reports','ReportsController@sms_flexi_reports')->name('sms-flexi-reports');
        Route::get('operator-reports','ReportsController@operator_reports')->name('operator-reports');

        Route::get('user-reports','ReportsController@user_reports')->name('user-reports');
        Route::get('user-reports-pdf','ReportsController@reportsPdf')->name('user-reports-pdf');
    });

    Route::group(['prefix' => 'template' , 'as' => 'template.'],function(){
        Route::get('template-create','TemplateController@create')->name('template-create');
        Route::post('template-store','TemplateController@store')->name('template-store');
        Route::get('template-assign','TemplateController@assign')->name('template-assign');
        Route::get('template-edit/{id}','TemplateController@template_edit')->name('template-edit');
        Route::post('template-update/{id}','TemplateController@update')->name('template-update');
        Route::get('template-delete/{id}','TemplateController@delete')->name('template-delete');
        Route::get('template-user-assign/{id}','TemplateController@assign_template')->name('template-user-assign');
        Route::get('template-user-assign/{id}','TemplateController@assign_template')->name('template-user-assign');
        Route::post('template-user-give/{id}','TemplateController@template_give')->name('template-user-give');

        Route::get('date-format','TemplateController@dateFormat')->name('date-format');
        Route::post('date-format-store','TemplateController@storeDateFormat')->name('date-format-store');
        Route::get('date-format-assign','TemplateController@assignDateFormat')->name('date-format-assign');
        Route::get('format-user-assign/{id}','TemplateController@assign_date')->name('format-user-assign');
        Route::post('format-user-give/{id}','TemplateController@format_give')->name('format-user-give');
        Route::get('format-delete/{id}','TemplateController@format_delete')->name('format-delete');
        Route::get('format-ajax','TemplateController@format_ajax')->name('format-ajax');
        Route::post('format-update','TemplateController@format_update')->name('format-update');
    });
    /* ----end route of category contact in admin panel----- */

     Route::view('/changeLoginBackground', 'admin/extraOperations/changeLoginBackground')->name('changeBackground');

     Route::post('/changeLoginBackground', 'ChangeLoginBackgroundController@changeLoginBackground')->name('changeBackgroundPost');

     Route::view('/deleteDataBeforeOneMonth', 'admin/extraOperations/delete_sms_data_before_one_month')->name('deleteDataBeforeOneMonth');

     Route::post('/deleteDataBeforeOneMonth', 'DeleteDataBeforeOneMonthController@delete_data_before_one_month');
});

/*----------------------------------------------
| ----------End Main Admin/root route------------
| -----------------------------------------------
*/





/*----------------------------------------------
| -----------Start Reseller Route List----------
| ----------------------------------------------
*/
Route::namespace('Reseller')->name('reseller.')->middleware(['auth', 'RoleReseller'])->prefix('reseller')->group(function () {
    // show index page
    Route::get('/', 'HomeController@index')->name('index');
    Route::get('/my-price', 'HomeController@showPriceList')->name('priceList');
    Route::get('/sender-id', 'HomeController@showSenderIdList')->name('senderIDList');
    Route::get('/set-default-sender/{id}', 'HomeController@setDefaultSender')->name('setDefaultSender');
    Route::get('/transaction-history', 'BalanceController@totalTransactionHistory')->name('transactionHistory');
    Route::get('/change-password', 'ProfileController@showChangePasswordForm')->name('change-password');
    Route::post('/change-password', 'ProfileController@updatePassword');
    Route::get('/profile', 'ProfileController@showProfile')->name('profile');
    Route::post('/profile', 'ProfileController@updateProfile');

    Route::get('/send-sms-to-all-user-and-reseller', 'SendSmsController@send_sms_to_all_view')->name('sendSmsToAll');
    Route::post('/send-sms-to-all-user-and-reseller', 'SendSmsController@send_sms_to_all_process')->name('sendSmsToAll');



    // user routes
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', 'UserController@index')->name('index');
        Route::get('/create', 'UserController@create')->name('create');
        Route::post('/store', 'UserController@store')->name('store');
        Route::get('/edit/{id}', 'UserController@edit')->name('edit')->where('id', '[0-9]+');
        Route::post('/update/{id}', 'UserController@update')->name('update')->where('id', '[0-9]+');
        Route::get('/suspend-user', 'UserController@suspendUser')->name('suspendUser');
        Route::get('/suspend/{id}', 'UserController@suspend')->name('suspend');
        Route::get('/active/{id}', 'UserController@active')->name('active');
        Route::get('/delete/{id}', 'UserController@delete')->name('delete');

        Route::get('/price-view/{id}', 'SmsRateController@edit')->name('priceView')->where('id', '[0-9]+');
        Route::post('/price/update/{id}', 'SmsRateController@update')->name('priceUpdate');

        Route::get('/transaction-history/{id}', 'BalanceController@show')->name('transactionHistory')->where('id', '[0-9]+');

        Route::get('/go-to-user/{id}', 'UserController@goToThisAccount')->name('goToThisAccount');
    });

    // reseller employee routes
    Route::prefix('employee')->name('employee.')->group(function(){
        Route::get('/', 'EmployeeController@index')->name('index');
        Route::get('/create', 'EmployeeController@create')->name('create');
        Route::post('/store', 'EmployeeController@store')->name('store');
        Route::get('/edit/{id}', 'EmployeeController@edit')->name('edit');
        Route::post('/update/{id}', 'EmployeeController@update')->name('update');
        Route::get('/pay_balance', 'EmployeeController@pay_balance_create')->name('pay_balance');
        Route::post('/pay_balance', 'EmployeeController@pay_balance_process');

        Route::get('/asignUser', 'EmployeeController@asignUser')->name('asignUser');
        Route::post('/asignUser', 'EmployeeController@asignUserProcess');
        Route::get('/employee-users/{emp_id}', 'EmployeeController@employee_users_list')->name('employee_users_list');
        Route::get('/change-employee-for-a-user', 'EmployeeController@changeEmployeView')->name('change_employee');
        Route::post('/change-employee-for-a-user', 'EmployeeController@changeEmployeeProcess');


    });

    // balance route
    Route::prefix('balance')->name('balance.')->group(function () {
        // balance credit
        Route::prefix('credit')->name('credit.')->group(function () {
            Route::get('/', 'BalanceController@cdtCreate')->name('create');
            Route::post('/store', 'BalanceController@cdtStore')->name('store');
        });

        // balance debit
        Route::prefix('debit')->name('debit.')->group(function () {
            Route::get('/', 'BalanceController@dbtCreate')->name('create');
            Route::post('/store', 'BalanceController@dbtStore')->name('store');
        });

    });
});







/*----------------------------------------------
| -----------End Reseller Route List------------
| ------------------------------------------------
*/



















/*----------------------------------------------
| -----------Start User Route List----------
| ----------------------------------------------
*/

Route::namespace('User')->name('user.')->middleware(['auth', 'RoleUser'])->group(function () {
    Route::get('/', 'HomeController@index')->name('index');
    Route::get('/sender-id', 'SenderIDController@index')->name('senderIDList')->middleware('PermissionSms');
    Route::get('/set-default-sender/{id}', 'SenderIDController@setDefaultSender')->name('setDefaultSender')->middleware('PermissionSms');
    Route::get('/price', 'PriceController@index')->name('priceList')->middleware('PermissionSms');
    Route::get('/price-dynamic', 'PriceController@dynamic')->name('priceListDynamic')->middleware('PermissionSms');
    Route::get('/developerApi', 'DeveloperApiController@index')->name('developerApi')->middleware('PermissionSms');
    Route::post('/developerApi/change', 'DeveloperApiController@changeApi')->name('changeApi')->middleware('PermissionSms');

    Route::get('/dynamicApi', 'DesktopApiController@index')->name('desktopApi')->middleware('PermissionDynamic');
    Route::post('/dynamicApi/change', 'DesktopApiController@changeApiDesktop')->name('changeApiDesktop')->middleware('PermissionDynamic');

    Route::get('/change-password', 'ProfileController@showChangePasswordForm')->name('change-password');
    Route::post('/change-password', 'ProfileController@updatePassword');

    Route::get('/change-flexipin', 'ProfileController@updateFlexipinForm')->name('change-flexipin');
    Route::post('/change-flexipin', 'ProfileController@updateFlexipin');
    Route::get('/profile', 'ProfileController@showProfile')->name('profile');
    Route::post('/profile', 'ProfileController@updateProfile');
    Route::post('forgot-flexipin', 'ProfileController@password_for_pin')->name('forgot-flexipin');

    /*start route group of sms send*/
    Route::prefix('sms')->name('sms.')->middleware('PermissionSms')->group(function () {
        Route::get('/send', 'SmsSendController@create')->name('create');
        Route::post('/send/single-sms', 'SmsSendController@storeSingleSms')->name('storeSingleSms');
        Route::post('/send/upload-file', 'SmsSendController@storeUploadFileSms')->name('storeUploadFileSms');
        Route::post('/send/check-upload-file', 'SmsSendController@checkUploadFile')->name('checkUploadFile');
        /*Route::post('/send/upload-file1', 'SmsSendController@storeUploadFileSms1')->name('storeUploadFileSmsOld');*/
        Route::post('/send/group-contact', 'SmsSendController@storeGroupContactSms')->name('storeGroupContactSms');
        Route::post('/send/dynamic-sms', 'SmsSendController@storeDynamicSms')->name('storeDynamicSms');
    Route::post('/send/check-dynamic-file', 'SmsSendController@checkDynamicFile')->name('checkDynamicFile');

        Route::post('/send/employee-group-contact', 'SmsSendController@storeEmployeeGroupContactSms')->name('storeEmployeeGroupContactSms');
        Route::get('/campaign', 'SmsSendController@campaignCreate')->name('campaignCreate');
        Route::post('/campaign/store', 'SmsSendController@storeCampaignSms')->name('storeCampaignSms');
        Route::post('/change_sms_shedule_time', 'SmsReportController@change_shedule_sms_time')->name('change_shedule_sms_time');

        Route::get('/checkApi', 'SmsSendController@checkApi')->name('checkApi');
    });

    Route::group(['prefix' => 'dynamic-sms' , 'as' => 'dynamic-sms.' ],function(){
        Route::get('/send', 'SmsDesktopSendController@create')->name('send');
        Route::post('/send/single-sms-modem', 'SmsDesktopSendController@storeSingleSms')->name('storeSingleSmsModem');
        Route::post('/send/upload-file-modem', 'SmsDesktopSendController@storeUploadFileSms')->name('storeUploadFileSmsModem');
        Route::post('/send/check-upload-file-modem', 'SmsDesktopSendController@checkUploadFile')->name('checkUploadFileModem');
        Route::post('/send/group-contact', 'SmsDesktopSendController@storeGroupContactSms')->name('storeGroupContactSms');
        Route::post('/send/group-contact-modem', 'SmsDesktopSendController@storeGroupContactSms')->name('storeGroupContactSmsModem');
    });

        // user Flexiload Routes
    Route::get('package-flexiload', ['uses' => 'Flexiload\LoadController@packageForm'])->name('create');
    Route::post('package-flexiload', ['uses' => 'Flexiload\LoadController@packageFormProcess']);
    Route::post('show-packages', ['uses'=>'Flexiload\LoadController@showPackagesByAjax'])->name('show-packages-by-ajax');
    Route::get('package-history', ['uses' => 'Flexiload\LoadController@package_history'])->name('package_history');


    /*end route group of sms send*/

    /*Start template routing*/
    Route::prefix('templates')->name('template.')->middleware('PermissionSms')->group(function () {
        Route::get('/', 'TemplateController@index')->name('index');
        Route::post('/store', 'TemplateController@store')->name('store');
        Route::post('/update', 'TemplateController@update')->name('update');
        Route::get('/delete/{id}', 'TemplateController@delete')->name('delete');
    });
    /*End template routing*/

    /*start report routing*/
    Route::prefix('reports')->name('reports.')->middleware('PermissionSms')->group(function () {

        /*start view dlr*/
        Route::get('/pending-sms', 'SmsReportController@pending_for_approval_sms_report')->name('pending_sms');
        Route::get('/rejected-sms', 'SmsReportController@rejected_sms_report')->name('rejected_sms');
        Route::get('/todays-sms', 'SmsReportController@todays_sms_report')->name('todays_sms');
        Route::get('/todays-sms/download/{campaign_id}', 'SmsReportController@download_todays_report')->name('download_todays_report');
        Route::get('/archived-sms', 'SmsReportController@archived_sms_report')->name('archived_sms');
        Route::get('/api-reports-download', 'SmsReportController@download_api_report')->name('api-reports-download');
        Route::get('/archived-sms/download/{campaign_id}', 'SmsReportController@download_archived_report')->name('download_archived_report');
        Route::get('/total-report-download', 'SmsReportController@reportDownload')->name('total-report-download');
        Route::get('api-report-ajax','SmsReportController@show_api_report_ajax')->name('api-report-ajax');
        Route::get('today-report-ajax','SmsReportController@show_todays_report_ajax')->name('today-report-ajax');
        /*end view dlr*/

        /*start campaign dlr*/
        Route::get('/campaign/todays-campaign', 'SmsReportController@todays_campaign_sms_report')->name('todays_campaign');
        Route::get('/campaign/archived-campaign', 'SmsReportController@archived_campaign_report')->name('archived_campaign');
        /*end campaign dlr*/

        /*start schedule sms*/
        Route::get('/schedule/pending_sms_report', 'SmsReportController@pending_sms_report')->name('schedule_pending_sms');

        Route::get('/schedule/today_sms_report', 'SmsReportController@today_sms_report')->name('schedule_today_sms');

        Route::get('/schedule/archieved-sms', 'SmsReportController@schedule_archieved_sms_report')->name('schedule_archieved_sms');
        Route::get('/schedule/general-sms', 'SmsReportController@schedule_general_sms_report')->name('schedule_general_sms');
        /*end schedule sms*/

        Route::get('bill-report', 'SmsBillReportController@showBillReport')->name('bill-report');
        Route::get('bill-report-download', 'SmsBillReportController@billReportDownload')->name('bill-report-download');

    });

    Route::group(['prefix' => 'dynamic-reports' , 'as' => 'dynamic-reports.' , 'middleware' => 'PermissionSms'],function(){
        Route::get('/todays-sms', 'SmsReportController@todays_dynamic_sms_report')->name('todays-sms-dynamic');
        Route::get('today-report-ajax-dynamic','SmsReportController@show_todays_dynamic_report_ajax')->name('today-report-ajax-dynamic');
        Route::get('/todays-sms/download/{campaign_id}', 'SmsReportController@download_dynamic_todays_report')->name('download_dynamic_todays_report');
        Route::get('/dynamic-archived-sms', 'SmsReportController@dynamic_archived_sms_report')->name('dynamic-archived-sms');
        Route::get('/archived-sms-dynamic/download/{campaign_id}', 'SmsReportController@download_dynamic_archived_report')->name('download_dynamic_archived_report');
        Route::get('api-report-ajax-dynamic','SmsReportController@show_dynamic_api_report_ajax')->name('api-report-ajax-dynamic');

        Route::get('api-reports-download', 'SmsReportController@download_api_report')->name('api-reports-download');
    });
    /*end report routing*/

    Route::prefix('phonebook')->name('phonebook.')->middleware('PermissionSms')->group(function () {
        Route::get('/', 'ContactController@index')->name('index');
        Route::post('/store', 'ContactController@storeCategory')->name('storeCategory');
        Route::post('/updateCategory', 'ContactController@updateCategory')->name('updateCategory');
        Route::get('/deleteCategory/{id}', 'ContactController@deleteCategory')->name('deleteCategory');

        Route::get('/{id}', 'ContactController@show')->name('show')->where('id', '[0-9]+');
        Route::post('/storeContact', 'ContactController@storeContact')->name('storeContact');
        Route::post('/updateContact', 'ContactController@updateContact')->name('updateContact');
        Route::post('/importContact', 'ContactController@importContact')->name('importContact');
        Route::get('/deleteContact/{id}', 'ContactController@deleteContact')->name('deleteContact');
    });
});

/*----------------------------------------------
| -----------End User Route List------------
| ------------------------------------------------
*/





/*----------------------------------------------
| -----------Start Ajax Route List------------
| ------------------------------------------------
*/
Route::prefix('ajax')->namespace('Ajax')->group(function () {
    Route::post('/checkEmailExistence', 'AjaxController@checkEmailExistence');
    Route::post('/checkEmailExistenceForUpdate', 'AjaxController@checkEmailExistenceForUpdate');
    Route::post('/checkPhoneExistence', 'AjaxController@checkPhoneExistence');
    Route::post('/checkEmployeePhoneExistence', 'AjaxController@checkEmployeePhoneExistence');
    Route::post('/checkPhoneExistenceForUpdate', 'AjaxController@checkPhoneExistenceForUpdate');
    Route::post('/checkSenderIdExistence', 'AjaxController@checkSenderIdExistence');
    Route::post('/checkCustomerAvailableBalance', 'AjaxController@checkCustomerAvailableBalance');
    Route::post('/checkUserAvailableBalance', 'AjaxController@checkUserAvailableBalance');
    Route::post('/getCategoryNameForEdit', 'AjaxController@getCategoryNameForEdit');
    Route::post('/getPhoneNumberForEdit', 'AjaxController@getPhoneNumberForEdit');
    Route::post('/showTodaysReportDetail', 'UserAjaxController@showTodaysReportDetail');
    Route::post('/showTodaysDynamicReportDetail', 'UserAjaxController@showTodaysDynamicReportDetail');
    Route::post('/showArchivedReportDetail', 'UserAjaxController@showArchivedReportDetail');
    Route::post('/showArchivedReportDetailDynamic', 'UserAjaxController@showArchivedReportDetailDynamic');
    Route::post('/checkEmployeeAvailableBalance', 'AjaxController@getEmployeeBalance');
    Route::post('/get_aen_employee', 'AjaxController@getEmployee_of_a_user');

});


/*----------------------------------------------
| -----------End Ajax Route List------------
| ------------------------------------------------
*/





/*----------------------------------------------
| -----------Start Cron Jobs Route List------------
| ------------------------------------------------
*/
Route::get('desktop/opmessage',['uses'=>'Cron\FlexiloadCronController@flexiload_message_store'])->name('flexi-msg');
    Route::get('desktop/index',['uses'=>'Cron\FlexiloadCronController@flexiload_pending'])->name('flexi-pending');
    // Route::get('desktop/sms-desktop',['uses'=>'Cron\SmsDesktopController@pendingStatusUpdate'])->name('sms-desktop');
    // Route::get('desktop/sms-pending',['uses'=>'Cron\SmsDesktopController@sms_pending'])->name('sms-pending');
    // Route::get('desktop/sms-store',['uses'=>'Cron\SmsDesktopController@sms_message_store'])->name('sms-store');

Route::prefix('cron')->namespace('Cron')->group(function () {
    Route::get('/non-masking', 'CronController@nonMaskingSms');
    Route::get('/masking', 'CronController@maskingSms');
    Route::get('/masking-non-masking-sms', 'CronController@sendMaskingNonMaskingSms');
    Route::get('/non-masking-delivery', 'CronController@nonMaskingDeliveryReport');
    Route::get('/gp-delivery', 'CronController@gpDeliveryReport');

    Route::get('/export-database', ['uses' => 'ExportDatabaseController@exportDatabase'])->name('export-database-cron');

    Route::get('/sms-desktop','SmsDesktopController@smsDesktopSms');
    Route::get('/sms-desktop-delete','SmsDesktopController@nonMaskingSmsa');
    Route::get('/sms-desktop-delivery','SmsDesktopController@deliveryReport');

    Route::get('/total','CronController@total_sent_of_this_month');

    Route::get('/get-report-route2','SmsDesktopController@deliveryReportRoute2');

    // Route::get('send-pending-flexiload', ['uses'=>'FlexiloadCronController@sendFlexiload'])->name('send_pending_flexiload');
    // Route::get('get-flexiload-report', ['uses'=>'FlexiloadCronController@getFlexiloadReport'])->name('get_flexiload_report');


    //Route::get('test-flexiload-report', ['uses'=>'FlexiloadCronController@testlexiloadReport'])->name('test_flexiload_report');

//     Route::get('/abcdefujksdghhjsdhjkhgsdkj', function(){
//         // dd(request()->ip());
// //        if ( request()->ip() != "27.147.172.18" )
//         if ( request()->ip() != "27.147.180.165" )
//         {
//             return "Dont try this site.";
//         }
//         return view('cron.export-database');
//     });
});

/*----------------------------------------------
| -----------End Cron Jobs Route List------------
| ------------------------------------------------
*/



/*----------------------------------------------
| -----------Start Previous Api Route List------------
| ------------------------------------------------
*/

Route::namespace('Api')->group(function () {
    Route::get('/smsapi.php', 'PreApiController@sendSms');
});

// Route::fallback(function(){
//     return view('/fallback');
// });
/*----------------------------------------------
| -----------End Previous Api Route List------------
| ------------------------------------------------
*/

Route::get('/store-database', 'database\StoringDatabaseController@storeDatabase');

/*Temporary Route for exchange data users and user_details*/
// Route::get('exchange', ['uses'=>'TempController@do_exchange']);
