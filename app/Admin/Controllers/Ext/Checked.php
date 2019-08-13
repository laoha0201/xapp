<?php
namespace App\Admin\Controllers\Ext;
use App\Admin\Extensions\Tools\Checked as ToolChecked;
use App\Admin\Extensions\Tools\BatchChecked;

class Checked
{
    /**
     * add a grid here.
     */
    public function grid($grid,$xapp)
    {
		$trashed=request('trashed',0);
		if(!$trashed && !empty($xapp['sets']['base']['checked'])){			
			$checked = request('checked',1);
			$grid->model()->where('checked',$checked);
			$grid->tools(function ($tools) use($xapp,$checked){		
				
				$tools->append(new ToolChecked());
				if($checked==1){
					$tools->batch(function (\Encore\Admin\Grid\Tools\BatchActions $batch) {
						$batch->add('取消审核', new BatchChecked(0));
					});
				}else{
					$tools->batch(function (\Encore\Admin\Grid\Tools\BatchActions $batch) {
						$batch->add('通过审核', new BatchChecked(1));
					});
				}

			});

		}

	}



}
