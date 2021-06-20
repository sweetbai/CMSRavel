<?php

namespace Modules\Common\UI\Form;

/**
 * Class Ip
 * @package Modules\Common\UI\Form
 */
class Ip extends Element implements Component
{
    protected Text $object;

    /**
     * Text constructor.
     * @param  string  $name
     * @param  string  $field
     * @param  string  $has
     */
    public function __construct(string $name, string $field, string $has = '')
    {
        $this->name = $name;
        $this->field = $field;
        $this->has = $has;
        $this->object = new Text($this->name, $this->field, $this->has);
        $this->object->type('text');
        $this->object->attr('data-js', 'form-mask');
        $this->object->attr('data-inputmask-alias' , "ip");
        $this->object->afterIcon('<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="5" width="18" height="14" rx="2" /><path d="M7 15v-4a2 2 0 0 1 4 0v4" /><line x1="7" y1="13" x2="11" y2="13" /><path d="M17 9v6h-1.5a1.5 1.5 0 1 1 1.5 -1.5" /></svg>');
    }

    /**
     * 渲染组件
     * @param $value
     * @return string
     */
    public function render($value): string
    {
        return $this->object->render($this->getValue($value));
    }

}