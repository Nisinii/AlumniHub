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
|	https://codeigniter.com/userguide3/general/routing.html
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
| When you set this option to TRUE, it will replace ALL dashes with
| underscores in the controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

// ── Default route ─────────────────────────────────────────────
$route['default_controller'] = 'bidding/api_today';
$route['404_override']       = '';
$route['translate_uri_dashes'] = FALSE;
 
// ── Auth ──────────────────────────────────────────────────────
$route['auth/verify/(:any)']         = 'auth/verify/$1';
$route['auth/reset_password/(:any)'] = 'auth/reset_password/$1';
 
// ── Profile API ───────────────────────────────────────────────
$route['profile/api/add/(:any)']             = 'profile/api_add/$1';
$route['profile/api/update/(:any)/(:num)']   = 'profile/api_update/$1/$2';
$route['profile/api/delete/(:any)/(:num)']   = 'profile/api_delete/$1/$2';
$route['profile/api/save']                   = 'profile/api_save';
$route['profile/api']                        = 'profile/api';
 
// ── Profile Web ───────────────────────────────────────────────
$route['profile/add/(:any)']                 = 'profile/add/$1';
$route['profile/edit_item/(:any)/(:num)']    = 'profile/edit_item/$1/$2';
$route['profile/delete/(:any)/(:num)']       = 'profile/delete/$1/$2';
 
// ── Bidding API ───────────────────────────────────────────────
$route['bidding/api/accept_offer/(:num)']    = 'bidding/api_accept_offer/$1';
$route['bidding/api/decline_offer/(:num)']   = 'bidding/api_decline_offer/$1';
$route['bidding/api/status']                 = 'bidding/api_status';
$route['bidding/api/tomorrow']               = 'bidding/api_tomorrow';
$route['bidding/api/monthly_limit']          = 'bidding/api_monthly_limit';
$route['bidding/api/history']                = 'bidding/api_history';
$route['bidding/api/balance']                = 'bidding/api_balance';
$route['bidding/api/place']                  = 'bidding/api_place';
$route['bidding/api/update']                 = 'bidding/api_update';
$route['bidding/api/cancel']                 = 'bidding/api_cancel';
$route['bidding/api/sponsors']               = 'bidding/api_sponsors';
$route['bidding/api/record_event']           = 'bidding/api_record_event';
$route['bidding/api/today']                  = 'bidding/api_today';
 
// ── Bidding Web ───────────────────────────────────────────────
$route['bidding/accept_offer/(:num)']        = 'bidding/accept_offer/$1';
$route['bidding/decline_offer/(:num)']       = 'bidding/decline_offer/$1';
 
// ── API Key Management ────────────────────────────────────────
$route['apikey/api/list']                    = 'apikey/api_list';
$route['apikey/api/generate']                = 'apikey/api_generate';
$route['apikey/api/revoke/(:num)']           = 'apikey/api_revoke/$1';
$route['apikey/api/stats/(:num)']            = 'apikey/api_stats/$1';
$route['apikey/revoke/(:num)']               = 'apikey/revoke/$1';
$route['apikey/stats/(:num)']                = 'apikey/stats/$1';
 
// ── API Docs ──────────────────────────────────────────────────
$route['api-docs']                           = 'apidocs/index';
$route['api-docs/spec']                      = 'apidocs/spec';