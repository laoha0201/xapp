<?php

namespace App\Admin\Forms\Xappsets;

use Encore\Admin\Widgets\Form;
use Illuminate\Support\Facades\Schema;
use App\Models\Xapp;

class Base extends Setbase
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '基础';
	
    /**
     * Build a form here.
     */
    public function form()
    {
		$xapp = Xapp::findOrFail(request('id'));
		$xapp_fields = Schema::getColumnListing($xapp->table);
		$this->hidden('id');
		foreach(config('xapp.xappset') as $name=>$set){		
			if( empty($set['field']) ){
				$this->switch($name, '启用'.$set['title'])->help('是否启用'.$set['title'].'功能');
			}elseif(in_array($set['field'],$xapp_fields)){
				$this->switch($name, '启用'.$set['title'])->help('是否启用'.$set['title'].'功能,本功能依赖'.$set['field'].'字段');
			}
		}
    }
}
