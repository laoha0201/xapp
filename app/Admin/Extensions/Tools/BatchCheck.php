<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class BatchCheck extends BatchAction
{
    protected $action;

    public function __construct($action = 1)
    {
        $this->action = $action;
    }
	
	/**
     * Script of batch delete action.
     */
    public function script()
    {
        $trans = [
            'check_confirm' => $this->action?'确认通过审核':'确认取消审核',
            'confirm'        => trans('admin.confirm'),
            'cancel'         => trans('admin.cancel'),
        ];

        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {

    swal({
        title: "{$trans['check_confirm']}",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "{$trans['confirm']}",
        showLoaderOnConfirm: true,
        cancelButtonText: "{$trans['cancel']}",
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    method: 'post',
                    url: '{$this->resource}/change',
                    data: {
                        _token:'{$this->getToken()}',
						ids: $.admin.grid.selected().join(),
						checked: {$this->action}
                    },
                    success: function (data) {
                        $.pjax.reload('#pjax-container');
                        resolve(data);
                    }
                });
            });
        }
    }).then(function(result) {
        var data = result.value;
        if (typeof data === 'object') {
            if (data.status) {
                swal(data.message, '', 'success');
            } else {
                swal(data.message, '', 'error');
            }
        }
    });
});

EOT;
    }
}