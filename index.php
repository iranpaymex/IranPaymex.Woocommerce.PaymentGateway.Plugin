<?php
/*
    Plugin Name: افزونه پرداخت امن و پایدار ایران پی‌مکس برای ووکامرس Iranpaymex
    Version: 5.0.19
    Description: افزونه درگاه پرداخت امن و سریع ایران پی‌مکس برای فروشگاه ساز ووکامرس
    Plugin URI: https://iranpaymex.com/
    Author: iranpaymex
    Author URI: http://iranpaymex.com/
    WC tested up to: 9.4.1
    Tested up to: 6.8
    License: GPLv3 or later
    License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
*/
if (!defined('ABSPATH'))
    exit;

define('WOO_GAPIRDIRIRANPAYMEX', plugin_dir_path(__FILE__));
define('WOO_GAPIRDUIRANPAYMEX', plugin_dir_url(__FILE__));
require_once(WOO_GAPIRDIRIRANPAYMEX . 'gateway-iranpaymex-for-woocommerce.php');
