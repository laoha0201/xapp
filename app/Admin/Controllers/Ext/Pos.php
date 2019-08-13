<?php
namespace App\Admin\Controllers\Ext;
use App\Admin\Extensions\Tools\BatchCate;

class Pos
{
    /**
     * add a grid here.
     */
    public function grid($grid,$xapp)
    {
		if( !empty($xapp['sets']['pos']['poses']) ){
			$filter_arr = str2arr($xapp['sets']['pos']['poses']);
			$grid->column('pos', '位置')->filter(array_combine($filter_arr,$filter_arr));		
		}
	}

    /**
     * add a form here.
     */
    public function form($form,$xapp,$id=0)
    {
		if( !empty($xapp['sets']['pos']['poses']) ){
			$form->tags('pos', '位置')->options(explode(",",$xapp['sets']['pos']['poses']));
		}
    }

    public function show($show,$xapp,$id=0)
    {
		$show->field('pos','位置');
	}
}
