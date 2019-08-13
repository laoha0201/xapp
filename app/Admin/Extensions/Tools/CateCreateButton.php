<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid;
use Encore\Admin\Grid\Tools\AbstractTool;

class CateCreateButton extends AbstractTool
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Create a new CreateButton instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render($cates=[])
    {
        if (!$this->grid->showCreateBtn()) {
            return '';
        }

        $new = trans('admin.new');
		if(!is_array($cates)){
			return $this->one($cates);
		}else{
			$catelist = '';
			foreach($cates as $k=>$v){
				$catelist .= "<li><a href='{$this->grid->getCreateUrl()}?cate_id={$k}'>{$v}</a></li>";
			}
			return <<<EOT
<div class="btn-group pull-right grid-create-btn" style="margin-right: 10px">
  <button type="button" class="btn btn-sm btn-success dropdown-toggle"  title="{$new}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    {$new} <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
    {$catelist}
  </ul>
</div>
EOT;
  		}
    }

	public function one($cate_id='')
	{
		if(empty($cate_id)){
			$url = $this->grid->getCreateUrl();
		}else{
			$url = $this->grid->getCreateUrl().'?cate_id='.$cate_id;
		}
		$new = trans('admin.new');
		return <<<EOT
<div class="btn-group pull-right grid-create-btn" style="margin-right: 10px">
	<a href="{$url}" class="btn btn-sm btn-success" title="{$new}">
		<i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;{$new}</span>
	</a>
</div>
EOT;
	}		
}