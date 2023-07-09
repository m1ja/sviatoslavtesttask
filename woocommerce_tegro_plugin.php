<?php
/**
 * Plugin Name: WooCommerce Tegro Plugin
 * Description: Плагин для интеграции Tegro с WooCommerce.
 * Version: 1.0
 * Author: Ваше имя
 */

// Регистрация метода оплаты в WooCommerce
add_filter('woocommerce_payment_gateways', 'add_tegro_payment_gateway');
function add_tegro_payment_gateway($gateways)
{
    $gateways[] = 'WC_Tegro_Payment_Gateway';
    return $gateways;
}

// Класс для обработки оплаты через Tegro
require_once( ABSPATH . 'wp-content/plugins/woocommerce/includes/abstracts/abstract-wc-payment-gateway.php' );
require_once( ABSPATH . 'wp-content/plugins/woocommerce/includes/abstracts/abstract-wc-settings-api.php');////

class WC_Tegro_Payment_Gateway extends WC_Payment_Gateway
{

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
    
    public function process_payment($order_id) {

        // Получите значения параметров
        $secret = 'NxZ5unqL';

        // Формирование данных для передачи в Tegro API
        $data = array(
            'shop_id' => '701957D56DF375A68261AB2387849DD6',
            'amount' => 100,
            'currency' => 'RUB',
            'order_id' => 123,
        );

        // Сортировка параметров по алфавитному порядку ключей
        ksort($data);

        // Формирование строки параметров
        $query_string = http_build_query($data);

        // Формирование подписи (sign)
        $sign = md5($query_string . $secret);

        // Формирование ссылки для оплаты
        $payment_url = "https://tegro.money/pay?{$query_string}&sign={$sign}";

        // Перенаправление пользователя на страницу оплаты
        return array(
            'result' => 'success',
            'redirect' => $payment_url
        );
    }
}
