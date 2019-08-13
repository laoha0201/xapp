<?php

namespace App\Admin\Extensions\Tools;
use Encore\Admin\Admin;

class RestoreAct
{
    protected $id;
    public function getToken()
    {
        return csrf_token();
    }


    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
		$path = app('request')->getPathInfo();
		return <<<SCRIPT
$('.grid-restore-row').on('click', function () {
	id = $(this).data('id');
    $.ajax({
        method: 'post',
        url: "{$path}/restore",
        data: {
            _token:'{$this->getToken()}',
            ids: id
        },
        success: function () {
            $.pjax.reload('#pjax-container');
            toastr.success('操作成功');
        }
    });

});

SCRIPT;
    }


    protected function render()
    {
        Admin::script($this->script());
        return " <a class='grid-restore-row' data-id='{$this->id}' title='恢复'><i class='fa fa-undo'></i></a> ";
    }

    public function __toString()
    {
        return $this->render();
    }
}
