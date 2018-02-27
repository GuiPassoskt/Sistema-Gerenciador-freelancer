<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
    
    function display_money($value, $currency = false, $decimal = 2)
    {
        $instance =& get_instance();
        $settings = Setting::first();
        switch ($settings->money_format) {
            case 1:
                $value = number_format($value, $decimal, '.', ',');
                break;
            case 2:
                $value = number_format($value, $decimal, ',', '.');
                break;
            case 3:
                $value = number_format($value, $decimal, '.', '');
                break;
            case 4:
                $value = number_format($value, $decimal, ',', '');
                break;
            default:
                $value = number_format($value, $decimal, '.', ',');
                break;
        }
        switch ($settings->money_currency_position) {
            case 1:
                $return = $currency.' '.$value;
                break;
            case 2:
                $return = $value.' '.$currency;
                break; 
            case false:
                $return = $value;
                break;          
            default:
                $return = $currency.' '.$value;
                break;
        }

        return $return;
    }
