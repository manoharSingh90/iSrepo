<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

define('COMPANYNAME', 'iSamplez');
define('FROMMAIL', 'mini@unikove.com');
define('ADMINMAIL', 'vimlesh@unikove.com');
define('ADMINCONTACT', '0110111111');
//tables

define('ADMIN', 'admin');
define('AGEBRACKET', 'age_brackets');
define('APPRATES', 'app_rates');

define('BRANDS', 'brands');
define('CAMP_BEHAVIOUR', 'camp_behaviour');
define('BRANDASSETS', 'brand_assets');
define('CAMPAIGNS', 'campaigns');
define('CAMPAIGNBANNERS', 'campaign_banners');

define('CAMPAIGN_BRAND_ASSETS', 'campaign_brand_assets');
define('CAMPAIGN_SAMPLES', 'campaign_samples');
define('CAMPAIGN_VENDS', 'campaign_vends');
define('CONTACTS', 'contacts');

define('INTEREST_MASTER', 'interest_masters');
define('INTEREST_OPTIONS', 'interest_options');
define('REVIEW', 'reviews');
define('TARGET_AUDIENCE', 'target_audience');

define('REVIEW_ANSWER_OPTIONS', 'review_answer_options');
define('REVIEW_QUESTIONS', 'review_questions');
define('USERS', 'users');
define('USERS_DEVICE_DTL', 'user_device_details');
define('USER_CAMP_INTERESTS', 'user_campaign_interests');
define('USER_INTERESTS', 'user_interests');
define('USER_INTEREST_OPTIONS', 'user_interest_options');
define('USER_PROMOCODES', '	user_promocodes');
define('USER_REVIEW', 'user_reviews');
define('USER_REVIEW_ANS', 'user_review_answers');
define('USER_SAMPLES', 'user_samples');

define('VENDING_MACHINES', 'vending_machines');
define('WALL_COMMENTS', 'wall_comments');
define('WALL_LIKES', 'wall_likes');
define('WALL_POSTS', 'wall_posts');

define('POST_BEHAVIOUR', 'post_behaviour');
define('NOTIFICATIONS', 'notifications');

define('AUDIENCE_INTEREST', 'audience_interest_options');
define('CITY', 'cities');
define('STATE', 'states');
define('COUNTRY', 'countries');
define('BUYPOST', 'buy_posts');