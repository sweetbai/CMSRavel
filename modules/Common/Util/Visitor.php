<?php

namespace Modules\Common\Util;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * 访问量工具
 */
class Visitor
{

    /**
     * 记录访问量
     * @param $type
     * @param $id
     * @param $driver
     * @throws \Throwable
     */
    public static function increment($type, $id, $driver): void
    {
        $date = date('Ymd');
        DB::beginTransaction();
        try {
            $view = \Modules\Common\Model\VisitorViews::firstOrCreate([
                'has_type' => $type, 'has_id' => $id
            ]);
            $view->increment('pv');

            $viewData = \Modules\Common\Model\VisitorViewsData::firstOrCreate([
                'has_type' => $type, 'has_id' => $id, 'driver' => $driver, 'date' => $date
            ]);
            $viewData->increment('pv');
            $keys = [
                'type' => $type,
                'id' => $id,
                'driver' => $driver,
                'ip' => request()->ip(),
                'ua' => request()->userAgent(),
            ];
            $key = 'app::views::' . sha1(implode(':', $keys));
            if (!Redis::get($key)) {
                Redis::setex($key, 86400 - (time() + 8 * 3600) % 86400, 1);
                $view->increment('uv');
                $viewData->increment('uv');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

}
