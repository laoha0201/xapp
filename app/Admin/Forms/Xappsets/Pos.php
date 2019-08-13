<?php
namespace App\Admin\Forms\Xappsets;

use Encore\Admin\Widgets\Form;

class Pos extends Setbase
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title  = '位置';
 
    /**
     * Build a form here.
     */
    public function form()
    {
		$this->hidden('id');
		//$this->switch('open', '启用位置')->help('是否启用位置功能');
        $this->tags('poses', '设置位置')->help('设置位置，如 焦点,精华,固顶等');
    }

}
