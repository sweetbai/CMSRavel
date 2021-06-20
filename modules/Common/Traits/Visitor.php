<?php

namespace Modules\Common\Traits;

/**
 * Class Visitor
 * @package Modules\Common\Model
 */
trait Visitor
{

    /**
     * 增加访客
     * @param string $driver
     * @return bool
     * @throws \Throwable
     */
    public function viewsInc(string $driver = 'web'): bool
    {
        $id = $this->{$this->primaryKey};
        if (!$id) {
            return false;
        }
        \Modules\Common\Util\Visitor::increment(get_called_class(), $id, $driver);
        return true;
    }


    /**
     * 删除关联内容
     * @return bool
     */
    public function viewsDel(): bool
    {
        $this->views()->delete();
        $this->viewsData(0)->delete();
        return true;
    }

    /**
     * 访问量
     */
    public function views(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne('\Modules\Common\Model\VisitorViews', 'has', 'has_type');
    }

    /**
     * 访问数据
     */
    public function viewsData($day = 7)
    {
        $data = $this->morphMany('\Modules\Common\Model\VisitorViewsData', 'has', 'has_type');
        if ($day) {
            $data = $data->where('date', '>=', date('Y-m-d', strtotime('-' . $day . ' day')));
        }
        return $data->orderBy('date');
    }

}
