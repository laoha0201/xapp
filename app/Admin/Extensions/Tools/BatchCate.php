<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class BatchCate extends BatchAction
{
	public function cate_groups()
    {
		return route('admin.api.get_cate_groups');
	}	

	/**
     * Script of batch delete action.
     */
    public function script()
    {
        $trans = [
            'check_confirm' => '确认更改?',
            'confirm'        => trans('admin.confirm'),
            'cancel'         => trans('admin.cancel'),
        ];

        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {
		ids = $.admin.grid.selected().join();
		if(ids=='') return false;
		$('#change-ids').val(ids);
		$('#grid-modal-change').modal("show");
});

$('.grid-change-submit').on('click', function(){
	data = {};
	if($('#change-cateid').length && $('#change-cateid').val()>0){
		data.cate_id = $('#change-cateid').val();	
		if($('#change-group').length && $('#change-group').val()){
			data.group = $('#change-group').val();	
		}
	}

	if ($.isEmptyObject(data)){
		return false;
	}

	data._token = '{$this->getToken()}';
	data.ids = $.admin.grid.selected().join();
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
                    data: data,
                    success: function (data) {
						$('#grid-modal-change').modal("hide");
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

$('#change-cateid').on('change', function(){
	if($('#change-group').length){ 
		var target = $('#change-group');
		$.get("{$this->cate_groups()}",{q : this.value}, function (data) {
			target.find("option").remove();
			options = '<option value="">选择分组</option>';
			for(var key in data){  
				options += '<option value="'+key+'">'+key+'</option>';
			} 
			target.html(options);
		});
	}
})
EOT;

    }
}