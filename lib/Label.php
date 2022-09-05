<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

use Typecho\Widget\Helper\Form\Element;
use Typecho\Widget\Helper\Layout;

class Label_Plugin extends Element
{

    public function label(string $value): Element
    {
        /** ��������Ԫ�� */
        if (empty($this->label)) {
            $this->label = new Typecho_Widget_Helper_Layout('label', array('class' => 'typecho-label', 'style'=>'font-size: 1em;border-bottom: 1px #ddd solid;padding-top:2em;'));
            $this->container($this->label);
        }

        $this->label->html($value);
        return $this;
    }

    public function input(?string $name = null, ?array $options = null): Layout
    {
        $input = new Typecho_Widget_Helper_Layout('p', array());
        $this->container($input);
        $this->inputs[] = $input;
        return $input;
    }

    protected function inputValue($value) {}

}