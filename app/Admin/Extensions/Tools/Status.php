<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class Status extends AbstractTool
{
	
    public function script()
    {
        $url = Request::fullUrlWithQuery(['status' => '_status_']);

        return <<<EOT

$('input:radio.grid-status').change(function () {

    var url = "$url".replace('_status_', $(this).val());

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

        return view('admin.tools.status', compact('options'));
    }
}