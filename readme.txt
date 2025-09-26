=== iranpaymex Payment Gateway for Woocommerce ===
Contributors: iranpaymex
Tags: woocommerce,iran paymex,gateway,payment,ایران پی‌مکس
Requires at least: 4.5
Donate link: https://iranpaymex.com
Requires at least: 4.5
Version: 5.0.19
Tested up to: 6.7.1
Stable tag: 5.0.19
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

با نصب این پلاگین می توانید از خدمات درگاه پرداخت مستقیم و یا اختصاصی ایران پی‌مکس برروی فروشگاه ساز ووکامرس استفاده کنید

== Description ==
 افزونه iran paymex Payment Gateway for Woocommerce امکان فروش اینترنتی و آنلاین از طریق درگاه پرداخت ایران پی‌مکس به ووکامرس اضافه می کند. 

== Installation ==
1. پوشه `IranPaymex.PaymentGateway.Plugin` را در مسیر `/wp-content/plugins/` آپلود کنید
2. افزونه را از طریق منوی 'افزونه ها' در وردپرس فعال کنید
3. تنظیمات افزونه را می توانید از طریق قسمت تنظیمات ووکامرس / تسویه حساب انجام دهید


== External services ==

This plugin connects to the IranPayMex API to handle online payments. It is used to navigate clients to the payment service provider (PSP) where they can securely complete their transaction.

The plugin sends transaction details (such as order ID, amount, and callback URL) to the API whenever a new payment is initiated. After the payment is completed, the API returns the transaction status, which is then verified by the plugin.

API Base URLs:

Production: https://ipg.iranpaymex.com/api/v1.0-rc/trasnactions/

we use this https://ipg.iranpaymex.com/api/v1.0-rc/trasnactions/create to create the transaction and send user to payment page and https://ipg.iranpaymex.com/api/v1.0-rc/trasnactions/verify to verify the transaction

This service is provided by IranPayMex: http://iranpaymex.com/terms .

== Changelog ==
= 5.0.19 =
رفع مشکل ارسال قیمت تمام شده

= 5.0.18 =
رفع مشکل توضیحات

= 5.0.16 =
رفع باگ توضیحات

= 4.9.01 =
هماهنگ سازی بخش واحد مالی با واحد مالی در ایران پی‌مکس
سازگاری با نسخه جدید ووکامرس و وردپرس
