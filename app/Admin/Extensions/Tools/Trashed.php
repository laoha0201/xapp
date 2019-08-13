<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class Trashed extends AbstractTool
{
	
    public function script()
    {
        $url = Request::fullUrlWithQuery(['trashed' => '_trashed_']);

        return <<<EOT

$('input:radio.grid-trashed').change(function () {

    var url = "$url".replace('_trashed_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;
    }

    public function render()
    {
        Admin::script($this->script());

        $options = [
            '0' => ['icon'=>'list','title'=>'列表'],
            '1' => ['icon'=>'trash','title'=>'回收站'],
        ];

        return view('admin.tools.trashed', compact('options'));
    }
}