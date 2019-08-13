<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\AbstractTool;

class Br extends AbstractTool
{
    public function render()
    {
        return '<hr />';
    }
}
