<?php

namespace App\Admin\Forms\Xappsets;

use Encore\Admin\Widgets\Form;

class Cate extends Setbase
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '分类';

    /**
     * Build a form here.
     */
    public function form()
    {
		$this->hidden('id');
		//$this->switch('open', '启用分类')->help('是否启用分类功能');
		$this->switch('group','启用分组')->help('是否启用分类内分组功能');
		$this->switch('dir','分类目录')->help('上级分类仅作目录,不允许添加内容');
		$this->switch('set','允许分类独立设置')->help('允许分类使用自定义设置');
    }
}
