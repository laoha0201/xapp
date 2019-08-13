<?php

namespace App\Admin\Forms;

use Encore\Admin\Grid as BaseGrid;

class Grid extends BaseGrid
{
	public $cates=[];

    /**
	 * 重写CreateButton
     * Render create button for grid.
     *
     * @return string
     */
    public function renderCreateButton()
    {
		$cate_id = request('cate_id',0);
		if(empty($this->cates)){
			return (new \Encore\Admin\Grid\Tools\CreateButton($this))->render();
		}elseif($cate_id){
			return (new \App\Admin\Extensions\Tools\CateCreateButton($this))->render($cate_id);
		}else{
			return (new \App\Admin\Extensions\Tools\CateCreateButton($this))->render($this->cates);
		}
    }

    public function setXapp($xapp)
    {
        $this->xapp=$xapp;
    }

    public function setCates($cates)
    {
        $this->cates=$cates;
    }
}