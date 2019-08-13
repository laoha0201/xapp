<?php

namespace App\Admin\Extensions\Tools;
use Encore\Admin\Grid\Tools\AbstractTool;
use Encore\Admin\Admin;

class BatchChange extends AbstractTool
{
	protected $xapp;

	public function __construct($xapp)
    {
        $this->xapp = $xapp;
    }   

    public function getToken()
    {
        return csrf_token();
    }
	public function rooturl()
    {
		return $this->grid->resource();
	}
	public function cate_groups()
    {
		return route('admin.api.get_cate_groups');
	}
	
    public function script()
    {	        
		$trans = [
            'check_confirm' => '确认更改',
            'confirm'        => trans('admin.confirm'),
            'cancel'         => trans('admin.cancel'),
        ];

        return <<<EOT
$('.grid-change-btn').on('click', function() {
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
	/*
	if($('#change-pos').length && $('#change-pos').val()){
		data.pos = $('#change-pos').val();	
	}*/
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
                    url: '{$this->rooturl()}/change',
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

	public function render(){
		Admin::script($this->script());
		$xapp = $this->xapp;
		$cate_sel = '';
		$pos_sel  = '';
		
		if(!empty($xapp->sets['base']['cate'])){
				$sel = \App\Models\Cate::selectOptions(function ($query) use($xapp){
						return $query->where('xapp_id', $xapp->id);
					},'选择新的分类',$xapp->sets['cate']['dir'] ?? false);
				$cate_sel .= '<div class="form-group"><label class="col-sm-3 control-label">新的分类</label><div class="col-sm-7"><select id="change-cateid" class="form-control" name="cate_id">';
				foreach($sel as $k=>$v){
					$cate_sel .= "<option value='{$k}'>{$v}</option>";
				}
				$cate_sel .= '</select></div></div>';
			if(!empty($xapp->sets['cate']['group'])){
				$cate_sel .= '<div class="form-group"><label class="col-sm-3 control-label">新的分组</label><div class="col-sm-7"><select id="change-group" class="form-control" name="group">';
				$cate_sel .= '<option value="">选择分组</option></select></div></div>';
			}
		}
		

		return <<<EOT
<div class="btn-group grid-change-btn" data-toggle="buttons">
	<label class="btn btn-sm btn-dropbox">
        <span class="hidden-xs">批改分类</span>
	</label>
</div>
<div class="modal" id="grid-modal-change" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">批改分类</h4>
      </div>
      <div class="modal-body">
		<div class="form-horizontal">
			<div class="form-group"><label class="col-sm-3 control-label">IDs</label><div class="col-sm-7"><input  class="form-control" id="change-ids" name="ids" value="" readonly="readonly"></div></div>
			{$cate_sel}
			{$pos_sel}
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-7">
				  <button class="btn btn-default grid-change-submit">确定</button>
				</div>
			</div>
		</div>				
      </div>
    </div>
  </div>
</div>
EOT;
    }	

}