<?php

namespace com\cminds\seokeywords\plugin\helpers;

use com\cminds\seokeywords\App;

class HTMLHelper {

    public static function input($name, $value = '', $arr = array()) {
        $arr = array_merge(array(
            'type' => 'text',
            'size' => '40',
            'aria-required' => 'false',
            'class' => 'regular-text',
            'id' => $name,
            'name' => $name,
            'value' => esc_attr($value)
                ), $arr);
        array_walk($arr, function(&$v, $k) {
            $v = sprintf('%s="%s"', $k, $v);
        });
        return sprintf('<input %s />', implode(' ', $arr));
    }

    public static function select($name, $value = '', $items = array(), $arr = array()) {
        $arr = array_merge(array(
            'id' => $name,
            'name' => $name
                ), $arr);
        array_walk($arr, function(&$v, $k) {
            $v = sprintf('%s="%s"', $k, $v);
        });
        $s = sprintf('<select %s>', implode(' ', $arr));
        foreach ($items as $k => $v) {
            $s .= sprintf('<option value="%1$s" %3$s>%2$s</option>', esc_attr($k), esc_html($v), ($value == $k) ? 'selected' : '');
        }
        $s .= '</select>';
        return $s;
    }

    public static function inputColor($name, $value = '#FFFFFF', $arr = array()) {
        if (isset($arr['class'])) {
            $arr['class'] = $arr['class'] . sprintf(' %s-input-color', App::PREFIX);
        } else {
            $arr['class'] = sprintf(' %s-input-color', App::PREFIX);
        }
        return static::input($name, $value, $arr);
    }

}
