<?php

namespace Modules\Common\UI\Form;

use Modules\Common\UI\Form\Component;
use Modules\Common\UI\Form\Composite;
use Modules\Common\UI\Form;
use Modules\Common\UI\Tools;

/**
 * Class Row
 * @package Modules\Common\UI\Table
 */
class Row extends Composite implements Component
{
    public function __construct()
    {
        $this->layout = false;
    }

    /**
     * 设置列
     * @param callable $callback
     * @param int $width
     * @return $this
     */
    public function column(callable $callback, int $width = 0): self
    {
        $form = new Form();
        $callback($form);
        $this->column[] = [
            'width' => $width,
            'object' => $form,
        ];
        return $this;
    }

    /**
     * 渲染组件
     * @param $value
     * @return string
     */
    public function render($value): string
    {
        $this->class('grid lg:grid-flow-col gap-x-4');
        $inner = [];
        foreach ($this->column as $vo) {
            $width = $vo['width'] ? "lg:row-span-{$vo['width']}" : '';
            $form = $vo['object']->renderForm($value);
            $inner[] = <<<HTML
                <div class="$width">
                    $form
                </div>
            HTML;
        }
        $innerHtml = implode('', $inner);
        return <<<HTML
            <div {$this->toElement()}>
                $innerHtml
            </div>
        HTML;
    }

}