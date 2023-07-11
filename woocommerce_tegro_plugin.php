<?php
/**
 * Plugin Name: WooCommerce Tegro Plugin
 * Description: Плагин для интеграции Tegro с WooCommerce.
 * Version: 1.0
 * Author: Коваленко Михаил
 */

// Регистрация метода оплаты в WooCommerce
add_filter('woocommerce_payment_gateways', 'add_tegro_payment_gateway');
function add_tegro_payment_gateway($gateways)
{
    $gateways[] = 'WC_Tegro_Payment_Gateway';
    return $gateways;
}

// Класс для обработки оплаты через Tegro
require_once(ABSPATH . 'wp-content/plugins/woocommerce/includes/abstracts/abstract-wc-payment-gateway.php');
require_once(ABSPATH . 'wp-content/plugins/woocommerce/includes/abstracts/abstract-wc-settings-api.php');

class WC_Tegro_Payment_Gateway extends WC_Payment_Gateway
{

    public function __construct()
    {
        $this->id = 'tegro_payment_gateway';
        $this->method_title = 'Tegro Payment Gateway';
        $this->method_description = 'Оплата через Tegro API';
        $this->has_fields = false;
        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    }
    
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => 'Включить/Выключить',
                'type' => 'checkbox',
                'label' => 'Включить оплату через Tegro',
                'default' => 'yes'
            ),
            'title' => array(
                'title' => 'Название',
                'type' => 'text',
                'description' => 'Название метода оплаты, отображаемое для покупателя',
                'default' => 'Tegro Payment',
                'desc_tip' => true
            ),
            'description' => array(
                'title' => 'Описание',
                'type' => 'textarea',
                'description' => 'Описание метода оплаты, отображаемое для покупателя',
                'default' => 'Оплата через Tegro API'
            )
        );
    }

    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);
        $shop_id = '701957D56DF375A68261AB2387849DD6'; //Замените на свой Shop_id
        $amount = $order->get_total();
        $currency = get_woocommerce_currency();
        $order_id = $order->get_id();

        // Формируем данные для создания подписи
        $data = array(
            'shop_id' => $shop_id,
            'amount' => $amount,
            'currency' => $currency,
            'order_id' => $order_id,
            'test'=> 1,
        );
        ksort($data);
        $str = http_build_query($data);
        $sign = md5($str . $secret);

        // Выполняем запрос к Python-скрипту, чтобы получить ссылку
        $script_path = '/home/c/cd09136/wordpress_0gnxo/public_html/wp-content/plugins/tegro-plugin.py'; // Замените на путь к вашему Python-скрипту
        $command = escapeshellcmd("python3 {$script_path} {$str}");
        $payment_url = shell_exec($command);
        // Проверяем, удалось ли получить ссылку
        if ($payment_url) {
            // Перенаправляем пользователя на страницу оплаты
            WC()->cart->empty_cart();
            return array(
                'result' => 'success',
                'redirect' => $payment_url,
            );
        } else {
            // Обработка ошибки, если не удалось получить ссылку
            wc_add_notice('Произошла ошибка при создании ссылки для оплаты. Пожалуйста, попробуйте еще раз.', 'error');
            return;
        }
        
    }
}

