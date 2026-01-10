<?php

namespace DrSAS\BladeComponents\View\Composers;

use Illuminate\View\View;

class DatatableComposer
{
    private static $used = false;
    public $uid;

    /**
     * Si je passe par ce component, alors je dois flagger son utilisation pour charger ses dépendances JS.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $this->uid = crc32(uniqid('', true));
        self::$used = true;

        $view->with('uid', $this->uid);
    }

    public static function isUsed()
    {
        return self::$used;
    }
}
