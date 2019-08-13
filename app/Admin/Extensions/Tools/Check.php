<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class Check extends AbstractTool
{
	
    public function script()
    {
        $url = Request::fullUrlWithQuery(['checked' => '_checked_']);

        return <<<EOT

$('input:radio.grid-checked').change(function () {

    var url = "$url".replace('_checked_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;
    }

    public function render()
    {
        Admin::script($this->script());

        $options = [
            '1' => ['icon'=>'list','title'=>'已审'],
            '0' => ['icon'=>'trash','title'=>'待审'],
        ];

        return view('admin.tools.check', compact('options'));
    }
}