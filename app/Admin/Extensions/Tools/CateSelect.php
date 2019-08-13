<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\AbstractTool;
use App\Models\Xapp;
use App\Models\Cate;

class CateSelect extends AbstractTool
{
    public function __construct(Xapp $xapp)
    {
        $this->xapp = $xapp;
    } 
	public function rooturl()
    {
		return $this->grid->resource();
	}
	public function render()
    {
        
		if( empty($this->xapp['sets']['base']['cate']) ) return '';
		$xapp = $this->xapp;
		$cate_id = request('cate_id',0);
		$cates = Cate::selectOptions(function ($query) use($xapp){
			return $query->where('xapp_id', $xapp->id);
		}, '全部',$xapp['sets']['cate']['dir'] ?? false);
		$sel = '<div class="input-group input-group-sm">
						<div class="input-group-addon"><i class="fa fa-list"></i></div>';
		$sel .= "<select name='cate_id' onchange=\"window.location.href='{$this->rooturl()}?cate_id='+this.options[this.options.selectedIndex].value\" class='form-control'>";
		foreach($cates as $key => $title){
				$selected =  ($key==$cate_id) ? "selected" : "";
				$sel .= "<option value='{$key}' {$selected}>{$title}</option>";
		}
		$sel .= '</select><span class="input-group-btn">
                     <button class="btn btn-primary">分类选择</button>
           </span></div>';	
		return $sel;
    }
}