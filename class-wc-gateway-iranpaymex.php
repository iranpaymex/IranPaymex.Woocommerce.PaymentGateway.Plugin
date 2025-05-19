<?php

if (!defined('ABSPATH'))
    exit;

function Load_Iranpaymex_Gateway()
{

    if (class_exists('WC_Payment_Gateway') && !class_exists('WC_Iranpaymex') && !function_exists('Woocommerce_Add_Iranpaymex_Gateway')) {


        add_filter('woocommerce_payment_gateways', 'Woocommerce_Add_Iranpaymex_Gateway');

        function Woocommerce_Add_Iranpaymex_Gateway($methods)
        {
            $methods[] = 'WC_Iranpaymex';
            return $methods;
        }


        class WC_Iranpaymex extends WC_Payment_Gateway
        {

            public function __construct()
            {

                $this->id = 'WC_Iranpaymex';
                $this->method_title = __('پرداخت ایران پی مکس', 'woocommerce');
                $this->method_description = __('تنظیمات درگاه پرداخت ایران پی مکس برای افزونه فروشگاه ساز ووکامرس', 'woocommerce');
                $this->icon = apply_filters('WC_Iranpaymex_logo', WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) . '/assets/images/logo.png');
                $this->has_fields = false;

                $this->init_form_fields();
                $this->init_settings();

                $this->title = $this->settings['title'];
                $this->description = $this->settings['description'];

                $this->signature = $this->settings['signature'];

                $this->success_massage = $this->settings['success_massage'];
                $this->failed_massage = $this->settings['failed_massage'];

                if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>='))
                    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
                else
                    add_action('woocommerce_update_options_payment_gateways', array($this, 'process_admin_options'));

                add_action('woocommerce_receipt_' . $this->id . '', array($this, 'Send_to_Iranpaymex_Gateway'));
                add_action('woocommerce_api_' . strtolower(get_class($this)) . '', array($this, 'Return_from_Iranpaymex_Gateway'));
            }


            public function admin_options()
            {


                parent::admin_options();
            }

            public function init_form_fields()
            {
                $this->form_fields = apply_filters(
                    'WC_Iranpaymex_Config',
                    array(
                        'base_confing' => array(
                            'title' => __('تنظیمات پایه ای', 'woocommerce'),
                            'type' => 'title',
                            'description' => '',
                        ),
                        'enabled' => array(
                            'title' => __('فعالسازی/غیرفعالسازی', 'woocommerce'),
                            'type' => 'checkbox',
                            'label' => __('فعالسازی درگاه ایران پی مکس', 'woocommerce'),
                            'description' => __('برای فعالسازی درگاه پرداخت ایران پی مکس باید چک باکس را تیک بزنید', 'woocommerce'),
                            'default' => 'yes',
                            'desc_tip' => true,
                        ),
                        'title' => array(
                            'title' => __('عنوان درگاه', 'woocommerce'),
                            'type' => 'text',
                            'description' => __('عنوان درگاه که در طی خرید به مشتری نمایش داده میشود', 'woocommerce'),
                            'default' => __('پرداخت امن ایران پی مکس', 'woocommerce'),
                            'desc_tip' => true,
                        ),
                        'description' => array(
                            'title' => __('توضیحات درگاه', 'woocommerce'),
                            'type' => 'text',
                            'desc_tip' => true,
                            'description' => __('توضیحاتی که در طی عملیات پرداخت برای درگاه نمایش داده خواهد شد', 'woocommerce'),
                            'default' => __('پرداخت امن به وسیله کلیه کارت های عضو شتاب از طریق درگاه ایران پی مکس', 'woocommerce')
                        ),
                        'account_confing' => array(
                            'title' => __('تنظیمات حساب ایران پی مکس', 'woocommerce'),
                            'type' => 'title',
                            'description' => '',
                        ),
                        'signature' => array(
                            'title' => __('کد امضای', 'woocommerce'),
                            'type' => 'text',
                            'description' => __('کد امضای ایران پی مکس', 'woocommerce'),
                            'default' => 'Iranpaymex',
                            'desc_tip' => true
                        ),
                        'payment_confing' => array(
                            'title' => __('تنظیمات عملیات پرداخت', 'woocommerce'),
                            'type' => 'title',
                            'description' => '',
                        ),
                        'success_massage' => array(
                            'title' => __('پیام پرداخت موفق', 'woocommerce'),
                            'type' => 'textarea',
                            'description' => __('متن پیامی که میخواهید بعد از پرداخت موفق به کاربر نمایش دهید را وارد نمایید . همچنین می توانید از شورت کد {transaction_id} برای نمایش کد رهگیری (توکن) ایران پی مکس استفاده نمایید .', 'woocommerce'),
                            'default' => __('با تشکر از شما . سفارش شما با موفقیت پرداخت شد .', 'woocommerce'),
                        ),
                        'failed_massage' => array(
                            'title' => __('پیام پرداخت ناموفق', 'woocommerce'),
                            'type' => 'textarea',
                            'description' => __('متن پیامی که میخواهید بعد از پرداخت ناموفق به کاربر نمایش دهید را وارد نمایید . همچنین می توانید از شورت کد {fault} برای نمایش دلیل خطای رخ داده استفاده نمایید . این دلیل خطا از سایت ایران پی مکس ارسال میگردد .', 'woocommerce'),
                            'default' => __('پرداخت شما ناموفق بوده است . لطفا مجددا تلاش نمایید یا در صورت بروز اشکال با مدیر سایت تماس بگیرید .', 'woocommerce'),
                        ),
                    )
                );
            }

            public function process_payment($order_id)
            {
                $order = new WC_Order($order_id);
                return array(
                    'result' => 'success',
                    'redirect' => $order->get_checkout_payment_url(true)
                );
            }

            /**
             * @param $action (PaymentRequest, )
             * @param $params string
             *
             * @return mixed
             */
            public function SendRequestToIranpaymex($action, $params)
            {
                try {

                    $number_of_connection_tries = 3;
                    $response = null;
                    while ($number_of_connection_tries > 0) {
                        $response = wp_safe_remote_post('https://ipg.iranpaymex.com/api/v1.0-rc/trasnactions/' . $action, array(
                            'body' => $params,
                            'headers' => array(
                                'Content-Type' => 'application/json'
                            )
                        ));
                        if (is_wp_error($response)) {
                            $number_of_connection_tries--;
                            continue;
                        } else {
                            break;
                        }
                    }

                    $body = wp_remote_retrieve_body($response);
                    return json_decode($body, true);
                } catch (Exception $ex) {
                    return false;
                }
            }

            public function Send_to_Iranpaymex_Gateway($order_id)
            {


                global $woocommerce;
                $woocommerce->session->order_id_Iranpaymex = $order_id;
                $order = new WC_Order($order_id);
                $currency = $order->get_currency();
                $currency = apply_filters('WC_Iranpaymex_Currency', $currency, $order_id);


                $form = '<form action="" method="POST" class="Iranpaymex-checkout-form" id="Iranpaymex-checkout-form">
						<input type="submit" name="Iranpaymex_submit" class="button alt" id="Iranpaymex-payment-button" value="' . __('پرداخت', 'woocommerce') . '"/>
						<a class="button cancel" href="' . $woocommerce->cart->get_checkout_url() . '">' . __('بازگشت', 'woocommerce') . '</a>
					 </form><br/>';
                $form = apply_filters('WC_Iranpaymex_Form', $form, $order_id, $woocommerce);

                do_action('WC_Iranpaymex_Gateway_Before_Form', $order_id, $woocommerce);
                echo $form;
                do_action('WC_Iranpaymex_Gateway_After_Form', $order_id, $woocommerce);


                $Amount = intval($order->order_total);
                $Amount = apply_filters('woocommerce_order_amount_total_IRANIAN_gateways_before_check_currency', $Amount, $currency);
                if (
                    strtolower($currency) == strtolower('IRT') || strtolower($currency) == strtolower('TOMAN') || strtolower($currency) == strtolower('Iran TOMAN') || strtolower($currency) == strtolower('Iranian TOMAN') || strtolower($currency) == strtolower('Iran-TOMAN') || strtolower($currency) == strtolower('Iranian-TOMAN') || strtolower($currency) == strtolower('Iran_TOMAN') || strtolower($currency) == strtolower('Iranian_TOMAN') || strtolower($currency) == strtolower('تومان') || strtolower($currency) == strtolower('تومان ایران')
                )
                    $Amount = $Amount * 10;
                else if (strtolower($currency) == strtolower('IRHT'))
                    $Amount = $Amount * 10000;
                else if (strtolower($currency) == strtolower('IRHR'))
                    $Amount = $Amount * 1000;
                else if (strtolower($currency) == strtolower('IRR'))
                    $Amount = $Amount;


                $Amount = apply_filters('woocommerce_order_amount_total_IRANIAN_gateways_after_check_currency', $Amount, $currency);
                $Amount = apply_filters('woocommerce_order_amount_total_IRANIAN_gateways_irt', $Amount, $currency);
                $Amount = apply_filters('woocommerce_order_amount_total_Iranpaymex_gateway', $Amount, $currency);

                $CallbackUrl = add_query_arg('wc_order', $order_id, WC()->api_request_url('WC_Iranpaymex'));

                // Iranpaymex Hash Secure Code
                $hash = md5($order_id . $Amount . $this->signature);
                $CallbackUrl = add_query_arg('secure', $hash, $CallbackUrl);

                $products = array();
                $order_items = $order->get_items();
                foreach ((array)$order_items as $product) {
                    $products[] = $product['name'] . ' (' . $product['qty'] . ') ';
                }
                $products = implode(' - ', $products);

                $Description = 'خریدار : ' . $order->billing_first_name . ' ' . $order->billing_last_name . ' | محصولات : ' . $products;

                $Mobile = get_post_meta($order_id, '_billing_phone', true) ? get_post_meta($order_id, '_billing_phone', true) : '-';
                $Email = $order->billing_email;
                $Description = apply_filters('WC_Iranpaymex_Description', $Description, $order_id);
                $Mobile = apply_filters('WC_Iranpaymex_Mobile', $Mobile, $order_id);
                $Email = apply_filters('WC_Iranpaymex_Email', $Email, $order_id);                
                do_action('WC_Iranpaymex_Gateway_Payment', $order_id, $Description, $Mobile);
                $Email = !filter_var($Email, FILTER_VALIDATE_EMAIL) === false ? $Email : '';
                $Mobile = preg_match('/^09[0-9]{9}/i', $Mobile) ? $Mobile : '';

                $Iranpaymexdirect = "https://ipg.iranpaymex.com/pay/%s";

                $data = array(
                    'signature' => $this->signature,
                    'amount' => $Amount,
                    'invoiceId' => $order->get_order_number(),
                    'callbackUrl' => $CallbackUrl,
                    'description' => $Description,
                    // 'payer' => array(
                    //     'mobileNumber' => $Mobile,
                    //     'email' => $Email,  
                    // )
                );

                $result = $this->SendRequestToIranpaymex('create', json_encode($data));

                if ($result === false) {
                    echo "cURL Error";
                } else {
                    if ($result['data']['authority']) {
                        wp_redirect(sprintf($Iranpaymexdirect, $result['data']['authority']));
                        exit;
                    } else {
                        $Message = '<pre>' . $result["error"]["code"] . $result["error"]["fa_message"] . '</pre>';
                        $Fault = '';
                    }
                }

                if (!empty($Message) && $Message) {

                    $Note = sprintf(__('خطا در هنگام ارسال به بانک : %s', 'woocommerce'), $Message);
                    $Note = apply_filters('WC_Iranpaymex_Send_to_Gateway_Failed_Note', $Note, $order_id, $Fault);
                    $order->add_order_note($Note);


                    $Notice = sprintf(__('در هنگام اتصال به بانک خطای زیر رخ داده است : <br/>%s', 'woocommerce'), $Message);
                    $Notice = apply_filters('WC_Iranpaymex_Send_to_Gateway_Failed_Notice', $Notice, $order_id, $Fault);
                    if ($Notice)
                        wc_add_notice($Notice, 'error');

                    do_action('WC_Iranpaymex_Send_to_Gateway_Failed', $order_id, $Fault);
                }
            }

            public function Return_from_Iranpaymex_Gateway()
            {

                $InvoiceNumber = isset($_GET['invoiceId']) ? sanitize_text_field($_GET['invoiceId']) : '';
                $Status = sanitize_text_field($_GET['status']);
                $Authority = sanitize_text_field($_GET['authority']);

                global $woocommerce;


                if (isset($_GET['wc_order']))
                    $order_id = sanitize_text_field($_GET['wc_order']);
                else if ($InvoiceNumber) {
                    $order_id = $InvoiceNumber;
                } else {
                    $order_id = $woocommerce->session->order_id_Iranpaymex;
                    unset($woocommerce->session->order_id_Iranpaymex);
                }

                if ($order_id) {

                    $order = new WC_Order($order_id);
                    $currency = $order->get_currency();
                    $currency = apply_filters('WC_Iranpaymex_Currency', $currency, $order_id);


                    $Amount = intval($order->order_total);
                    $Amount = apply_filters('woocommerce_order_amount_total_IRANIAN_gateways_before_check_currency', $Amount, $currency);
                    if (
                        strtolower($currency) == strtolower('IRT') || strtolower($currency) == strtolower('TOMAN') || strtolower($currency) == strtolower('Iran TOMAN') || strtolower($currency) == strtolower('Iranian TOMAN') || strtolower($currency) == strtolower('Iran-TOMAN') || strtolower($currency) == strtolower('Iranian-TOMAN') || strtolower($currency) == strtolower('Iran_TOMAN') || strtolower($currency) == strtolower('Iranian_TOMAN') || strtolower($currency) == strtolower('تومان') || strtolower($currency) == strtolower('تومان ایران')
                    )
                        $Amount = $Amount * 10;
                    else if (strtolower($currency) == strtolower('IRHT'))
                        $Amount = $Amount * 10000;
                    else if (strtolower($currency) == strtolower('IRHR'))
                        $Amount = $Amount * 1000;
                    else if (strtolower($currency) == strtolower('IRR'))
                        $Amount = $Amount;


                    $hash = md5($order_id . $Amount . $this->signature);

                    if ($_GET['secure'] == $hash) {

                        if ($order->status != 'completed') {

                            if ($Status == 'OK') {
                                $Signature = $this->signature;
                                $data = array('signature' => $Signature, 'authority' => $Authority , 'invoiceId' => $InvoiceNumber , 'amount' => $Amount);
                                $result = $this->SendRequestToIranpaymex('verify', json_encode($data));

                                if ($result['data']["status"] == "success" && $result["data"]['amount'] == $Amount) {
                                    $Status = 'completed';
                                    $Transaction_ID = $InvoiceNumber;
                                    $verify_card_no = $result['data']['maskedCardNumber'];
                                    $verify_ref_num = $result['data']['rrn'];
                                    $Fault = '';
                                    $Message = '';
                                } elseif ($result['error']['code'] == 405) {
                                    $Message = 'این تراکنش قبلا تایید شده است';
                                    $Notice = wpautop(wptexturize($Message));
                                    wp_redirect(add_query_arg('wc_status', 'success', $this->get_return_url($order)));
                                    exit;
                                } else {
                                    $Status = 'failed';
                                    $Fault = $result['result'];
                                    $Message = 'تراکنش ناموفق بود';
                                }
                            } else {
                                $Status = 'failed';
                                $Fault = '';
                                $Message = 'تراکنش انجام نشد .';
                            }

                            if ($Status == 'completed' && isset($Transaction_ID) && $Transaction_ID != 0) {

                                update_post_meta($order_id, '_transaction_id', $Transaction_ID);
                                update_post_meta($order_id, 'Iranpaymex_payment_card_number', $verify_card_no);
                                update_post_meta($order_id, 'Iranpaymex_payment_ref_number', $verify_ref_num);

                                $order->payment_complete($Transaction_ID);
                                $woocommerce->cart->empty_cart();

                                $Note = sprintf(__('پرداخت موفقیت آمیز بود .<br/> کد رهگیری : %s', 'woocommerce'), $Transaction_ID);
                                $Note .= sprintf(__('<br/> شماره کارت پرداخت کننده : %s', 'woocommerce'), $verify_card_no);
                                $Note .= sprintf(__('<br/> شماره مرجع : %s', 'woocommerce'), $verify_ref_num);
                                $Note = apply_filters('WC_Iranpaymex_Return_from_Gateway_Success_Note', $Note, $order_id, $Transaction_ID, $verify_card_no, $verify_ref_num);
                                if ($Note)
                                    $order->add_order_note($Note, 1);


                                $Notice = wpautop(wptexturize($this->success_massage));

                                $Notice = str_replace("{transaction_id}", $Transaction_ID, $Notice);

                                $Notice = apply_filters('WC_Iranpaymex_Return_from_Gateway_Success_Notice', $Notice, $order_id, $Transaction_ID);
                                if ($Notice)
                                    wc_add_notice($Notice, 'success');

                                do_action('WC_Iranpaymex_Return_from_Gateway_Success', $order_id, $Transaction_ID);

                                wp_redirect(add_query_arg('wc_status', 'success', $this->get_return_url($order)));
                                exit;
                            } else {


                                $tr_id = ($Transaction_ID && $Transaction_ID != 0) ? ('<br/>توکن : ' . $Transaction_ID) : '';

                                $Note = sprintf(__('خطا در هنگام بازگشت از بانک : %s %s', 'woocommerce'), $Message, $tr_id);

                                $Note = apply_filters('WC_Iranpaymex_Return_from_Gateway_Failed_Note', $Note, $order_id, $Transaction_ID, $Fault);
                                if ($Note)
                                    $order->add_order_note($Note, 1);

                                $Notice = '<pre>' . print_r($result, true) . '</pre>';

                                $Notice = str_replace("{transaction_id}", $Transaction_ID, $Notice);

                                $Notice = str_replace("{fault}", $Message, $Notice);
                                $Notice = apply_filters('WC_Iranpaymex_Return_from_Gateway_Failed_Notice', $Notice, $order_id, $Transaction_ID, $Fault);
                                if ($Notice)
                                    wc_add_notice($Notice, 'error');

                                do_action('WC_Iranpaymex_Return_from_Gateway_Failed', $order_id, $Transaction_ID, $Fault);

                                wp_redirect($woocommerce->cart->get_checkout_url());
                                exit;
                            }
                        } else {

                            $Transaction_ID = get_post_meta($order_id, '_transaction_id', true);

                            $Notice = wpautop(wptexturize($this->success_massage));

                            $Notice = str_replace("{transaction_id}", $Transaction_ID, $Notice);

                            $Notice = apply_filters('WC_Iranpaymex_Return_from_Gateway_ReSuccess_Notice', $Notice, $order_id, $Transaction_ID);
                            if ($Notice)
                                wc_add_notice($Notice, 'success');


                            do_action('WC_Iranpaymex_Return_from_Gateway_ReSuccess', $order_id, $Transaction_ID);

                            wp_redirect(add_query_arg('wc_status', 'success', $this->get_return_url($order)));
                            exit;
                        }
                    } else {
                        echo 'شما اجازه دسترسی به این قسمت را ندارید.';
                        die();
                    }
                } else {


                    $Fault = __('شماره سفارش وجود ندارد .', 'woocommerce');
                    $Notice = wpautop(wptexturize($this->failed_massage));
                    $Notice = str_replace("{fault}", $Fault, $Notice);
                    $Notice = apply_filters('WC_Iranpaymex_Return_from_Gateway_No_Order_ID_Notice', $Notice, $order_id, $Fault);
                    if ($Notice)
                        wc_add_notice($Notice, 'error');

                    do_action('WC_Iranpaymex_Return_from_Gateway_No_Order_ID', $order_id, $Transaction_ID, $Fault);

                    wp_redirect($woocommerce->cart->get_checkout_url());
                    exit;
                }
            }
        }
    }
}

add_action('plugins_loaded', 'Load_Iranpaymex_Gateway', 0);
