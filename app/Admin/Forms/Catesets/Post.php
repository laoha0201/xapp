<?php
namespace App\Admin\Forms\Catesets;

use Encore\Admin\Widgets\Form;

class Post extends Setbase
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title  = '表单';
 
    /**
     * Build a form here.
     */
    public function form()
    {
		$this->hidden('id');
		$editor = [];
		foreach(config('xapp.editor') as $v){
			if(!empty(config("admin.extensions.{$v}.enable"))) $editor[$v]=$v;
		}
		$this->select('editor','选择编辑器')->options($editor);
		$this->switch('disable_img','关闭图集字段');
		$this->number('max_img', '图片大小限止(K)')->default(2048)->help('默认2048K');
    }

}
