<?php
/*
    Plugin Name: افزونه پرداخت امن و پایدار ایران‌پی‌مکس برای ووکامرس
    Version: 5.0.18
    Description: افزونه درگاه پرداخت امن و سریع ایران‌پی‌مکس برای فروشگاه ساز ووکامرس
    Plugin URI: https://iranpaymex.com/
    Author: iranpaymex
    Text Domain: wc-irpmx
    Author URI: http://iranpaymex.com/
    WC tested up to: 6.7
    Tested up to: 6.7
    License: GPLv3 or later
    License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
*/
if (!defined('ABSPATH'))
    exit;

define('WOO_GAPIRDIRIRANPAYMEX', plugin_dir_path(__FILE__));
define('WOO_GAPIRDUIRANPAYMEX', plugin_dir_url(__FILE__));
require_once(WOO_GAPIRDIRIRANPAYMEX . 'gateway-iranpaymex.php');
