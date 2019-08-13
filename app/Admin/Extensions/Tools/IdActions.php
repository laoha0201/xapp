<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\Actions;

class IdActions extends Actions
{

    /**
     * Get route key name of current row.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return $this->row->{$this->row->getKeyName()};
    }

}
