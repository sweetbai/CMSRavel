<?php

namespace Modules\Common\UI\Widget;

use Modules\Common\UI\Tools;

/**
 * 行布局组件
 * Class Row
 * @package Modules\Common\UI\Widget
 */
class Row extends Widget
{

    private array $column = [];

    /**
     * Row constructor.
     * @param callable|null $callback
     */
    public function __construct(callable $callback = NULL)
    {
        $this->callback = $callback;
    }

    /**
     * 设置列
     * @param callable $callback
     * @param int $width
     * @return $this
     */
    public function column(callable $callback, int $width = 0): self
    {
        $this->column[] = [
            'width'  => $width,
            'callback' => $callback,
        ];
        return $this;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $this->class('grid grid-flow-col gap-x-4');
        $inner = [];
        foreach ($this->column as $vo) {
            $width = $vo['width'] ? "row-span-{$vo['width']}" : '';
            $callback = call_user_func($vo['callback']);
            $inner[] = <<<HTML
                <div class="$width">$callback</div>
            HTML;
        }
        return <<<HTML
            <div {$this->toElement()}>
                {$this->mergeArray($inner)}
            </div>
        HTML;

    }

}
