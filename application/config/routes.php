<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//admin routing 
$route['admin'] = 'admin/admin/index';
$route['admin-reset-pass'] = 'admin/admin/reset_password';
$route['admin-reset-pass/(:any)'] = 'admin/admin/reset_password/$1';
$route['admin-forgot-pass'] = 'admin/admin/forgotpassword';
$route['logout'] = 'admin/admin/logout';
$route['admin-login'] = 'admin/admin/login';
$route['users-list'] = 'admin/users/index';
$route['user-details/(:any)'] = 'admin/users/userDetails/$1';
$route['getUsers/(:any)'] = 'admin/users/getUsers/$1';

$route['brands'] = 'admin/brands/index';
$route['create-brands'] = 'admin/brands/create';
$route['edit-brands/(:any)'] = 'admin/brands/edit/$1';
$route['brand-detail-campaign/(:any)'] = 'admin/brands/brand_detail_campaign/$1';
$route['getBrands/(:any)'] = 'admin/brands/getBrands/$1';
$route['brand-detail-post/(:any)'] = 'admin/brands/brand_detail_post/$1';

$route['create-campaign-brand/(:any)'] = 'admin/brands/create_campaign_brand/$1';
$route['create-campaign-brand/(:any)/(:any)'] = 'admin/brands/create_campaign_brand/$1/$2';
$route['edit-campaign-brand/(:any)'] = 'admin/brands/edit_campaign_brand/$1';
$route['edit-campaign-brand/(:any)/(:any)'] = 'admin/brands/edit_campaign_brand/$1/$2';
//campaign
$route['create-campaigns/(:any)'] 		= 'admin/campaigns/create/$1';
$route['create-campaigns/(:any)/(:any)'] 		= 'admin/campaigns/create/$1/$2';
$route['edit-campaigns/(:any)'] = 'admin/campaigns/edit/$1';
$route['edit-campaigns/(:any)/(:any)'] 		= 'admin/campaigns/edit/$1/$2';
$route['getCampaigns/(:any)'] = 'admin/campaigns/getCampaigns/$1';
$route['campaign_detail_samples/(:any)'] = 'admin/campaigns/campaign_detail_samples/$1';
$route['campaign_detail_samples/(:any)/(:any)'] = 'admin/campaigns/campaign_detail_samples/$1/$2';
$route['campaign_detail_audience/(:any)'] = 'admin/campaigns/campaign_detail_audience/$1';
$route['campaign_detail_audience/(:any)/(:any)'] = 'admin/campaigns/campaign_detail_audience/$1/$2';
$route['campaign_detail_reviews/(:any)'] = 'admin/campaigns/campaign_detail_reviews/$1';
$route['campaign_detail_reviews/(:any)/(:any)'] = 'admin/campaigns/campaign_detail_reviews/$1/$2';
//post
$route['create-posts/(:any)'] 		= 'admin/posts/create/$1';
$route['create-posts/(:any)/(:any)'] 		= 'admin/posts/create/$1/$2';
$route['edit-posts/(:any)'] = 'admin/posts/edit/$1';
$route['getPosts/(:any)'] 	= 'admin/posts/getPosts/$1';
$route['post-view/(:any)/(:any)'] 	= 'admin/posts/postView/$1/$2';
$route['post-edit/(:any)/(:any)'] 	= 'admin/posts/postEdit/$1/$2';
//samples

$route['create-samples/(:any)'] 		= 'admin/samples/create/$1';
$route['create-samples/(:any)/(:any)'] 		= 'admin/samples/create/$1/$2';
$route['edit-samples/(:any)'] 	= 'admin/samples/edit/$1';
$route['edit-samples/(:any)/(:any)'] 		= 'admin/samples/edit/$1/$2';
$route['getSamples/(:any)'] 	= 'admin/samples/getSamples/$1';

//TargetAudience
$route['create-questionnaire/(:any)'] 		= 'admin/questionnaire/create/$1';
$route['create-questionnaire/(:any)/(:any)'] 		= 'admin/questionnaire/create/$1/$2';
$route['edit-questionnaire/(:any)'] 	= 'admin/questionnaire/edit/$1';
$route['edit-questionnaire/(:any)/(:any)'] 		= 'admin/questionnaire/edit/$1/$2';
$route['getquestionnaire/(:any)'] 	= 'admin/questionnaire/getquestionnaire/$1';

//TargetAudience
$route['create-targetAudience/(:any)'] 		= 'admin/targetAudience/create/$1';
$route['create-targetAudience/(:any)/(:any)'] 		= 'admin/targetAudience/create/$1/$2';
$route['edit-targetAudience/(:any)'] 	= 'admin/targetAudience/edit/$1';
$route['edit-targetAudience/(:any)/(:any)'] 	= 'admin/targetAudience/edit/$1/$2';
$route['getTargetAudience/(:any)'] 	= 'admin/targetAudience/getTargetAudience/$1';

//reviews
$route['getReviews/(:any)'] = 'admin/reviews/getReviews/$1';
$route['create-vending/(:any)'] = 'admin/vendingMachine/create/$1';
$route['create-vending/(:any)/(:any)'] = 'admin/vendingMachine/create/$1/$2';

$route['city-update'] = 'admin/vendingMachine/city/';
//Reports 
 $route['report'] = 'admin/reports/index';
 $route['city-data'] = 'admin/reports/getCityData';
 $route['city-state-data'] = 'admin/reports/getCityStateData';
 $route['campaign-review-data'] = 'admin/reports/campaignReviewData';
 $route['user-review-data'] = 'admin/reports/userReviewData';
 $route['export-data'] = 'admin/reports/excelExport';
 $route['vending-export-data'] = 'admin/reports/vendingExcelExport';
 $route['campaign-export-data'] = 'admin/reports/campaignExcelExport';
 $route['campaign-detail-data-export'] = 'admin/reports/campaignDetailExcelExport';
 $route['campaign-report'] = 'admin/reports/campaigns';
 $route['campaign-report-detali/(:any)'] = 'admin/reports/campaignReportDetail/$1';
 $route['vending-machine-report'] = 'admin/reports/vendingMachine';
 $route['trash-file'] = 'admin/reports/trashFile';
$route['manage_sms'] = 'admin/manageSms/index';
$route['date-time'] = 'admin/reports/rangePick';
$route['campaign-completion/(:any)'] = 'admin/reports/campaignCompletionReport/$1';

//API
$route['mobile'] = 'mobile/index';
//$route['qrcodeGenerator'] = 'api/mobile/qrcodeGenerator/$1/$2';
//$route['qrcodeGenerator/(:any)'] = 'mobile/qrcodeGenerator/$1';

/// ========== BuyNowPost =================

$route['posts'] = 'admin/posts/buyPosts';
$route['getBuyPosts/(:any)'] = 'admin/posts/getBuyPosts/$1';
$route['buy-post-view/(:any)'] = 'admin/posts/view_buy_now_post/$1';
$route['buy-post-edit/(:any)'] = 'admin/posts/edit_buy_now_post/$1';
$route['create-buy-now-posts'] = 'admin/posts/create_buy_now_post';
$route['addBuyPosts'] = 'admin/posts/addBuyPosts';
$route['editBuyPosts'] = 'admin/posts/editBuyPosts';
