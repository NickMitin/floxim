<?php

namespace Floxim\Floxim\Admin;

class Ui
{
    const STORE_ADMIN = 0;
    const STORE_FRONT = 1;

    public function input($name, $label = '', $value = null)
    {
        $fl = array('name' => $name);
        if ($label) {
            $fl['label'] = $label;
        }
        if ($value !== null) {
            $fl['value'] = $value;
        }
        return $fl;
    }

    public function text($name, $label = '', $value = null)
    {
        $fl = array('name' => $name, 'type' => 'text');
        if ($label) {
            $fl['label'] = $label;
        }
        if ($value !== null) {
            $fl['value'] = $value;
        }
        return $fl;
    }

    public function password($name, $label = '', $value = null)
    {
        $fl = array('name' => $name);
        if ($label) {
            $fl['label'] = $label;
        }
        if ($value !== null) {
            $fl['value'] = $value;
        }
        $fl['password'] = 1;
        return $fl;
    }

    public function hidden($name, $value = 1)
    {
        $ret = array('name' => $name, 'value' => $value, 'type' => 'hidden');
        return $ret;
    }

    public function select($name, $label = '', $values = array(), $value = null, $multiple = false)
    {
        $fl = array('name' => $name, 'type' => 'select');
        if ($label) {
            $fl['label'] = $label;
        }
        if ($multiple) {
            $fl['multiple'] = 1;
        }
        $fl['values'] = is_array($values) ? $values : array();
        if ($value !== null) {
            $fl['value'] = $value;
        }
        return $fl;
    }


    public function radio($name, $label = '', $values = array(), $value = null)
    {
        $fl = array('name' => $name, 'type' => 'radio');
        if ($label) {
            $fl['label'] = $label;
        }
        $fl['values'] = is_array($values) ? $values : array();
        if ($value !== null) {
            $fl['value'] = $value;
        }
        return $fl;
    }

    public function checkbox($name, $label = '', $values = array(), $value = null)
    {
        $fl = array('name' => $name, 'type' => 'checkbox');
        if ($label) {
            $fl['label'] = $label;
        }
        if ($values) {
            $fl['values'] = is_array($values) ? $values : array();
        }
        if ($value !== null) {
            $fl['value'] = ($values && !is_array($value)) ? array($value) : $value;
        }
        return $fl;
    }

    public function label($label)
    {
        return array('label' => $label, 'type' => 'label');
    }

    public function html($html)
    {
        return array('html' => $html, 'type' => 'html');
    }

    public function error($label)
    {
        return array('label' => $label, 'type' => 'label', 'status' => 'error');
    }

    public function file($name, $label = '', $value = null)
    {
        $fl = array('name' => $name, 'type' => 'file');
        if ($label) {
            $fl['label'] = $label;
        }
        if ($value !== null) {
            $fl['value'] = $value;
        }
        return $fl;
    }

    public function color($name, $label = '', $value = null)
    {
        $fl = array('name' => $name, 'type' => 'color');
        if ($label) {
            $fl['label'] = $label;
        }
        if ($value !== null) {
            $fl['value'] = $value;
        }
        return $fl;
    }

    public function codeEditor($name, $label, $value = '', $code = 'html')
    {
        return array('label' => $label, 'name' => $name, 'value' => $value, 'type' => 'text', 'code' => $code);
    }

    public function store($type, $filter = array(), $reason = 'first', $position = 0)
    {
        $store = new Store();
        $response = $store->getItems($type, $filter, $reason, $position);
        if ($response === false) {
            $result = $this->error(fx::alang('Unable to connect to server', 'system'));
        } else {
            if ($response['error']) {
                $result = $this->html($response['error']);
            } else {
                $result['type'] = 'store';
                $result['entity_type'] = $type;
                $result['items'] = $response['items'];
                $result['count'] = $response['count'];
                $result['filter_form'] = $response['filter_form'];
            }
        }

        return $result;
    }

    public function tree($values, $name = 'tree')
    {
        return array('type' => 'tree', 'values' => $values, 'filter' => true, 'name' => $name);
    }

    public function buttonPost($label, $post = array())
    {
        return array('label' => $label, 'type' => 'button', 'postdata' => $post);
    }

    public function buttonDialog($label, $post = array())
    {
        return array('label' => $label, 'type' => 'button', 'dialog' => $post);
    }

    public function buttonFunc($label, $func)
    {
        return array('label' => $label, 'type' => 'button', 'func' => $func);
    }

}