<?php
namespace App\Admin\Forms\Xappsets;

use Encore\Admin\Widgets\Form;

class Attachment extends Setbase
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title  = '附件';
 
    /**
     * Build a form here.
     */
    public function form()
    {
		$this->hidden('id');
		$this->text('title','附件名称')->help('附件类型名称，如视频、文档、图书等，默认为图片或文件');
		$this->select('type', '附件类型')->options(['image'=>'单图','images'=>'多图','file'=>'单文件','files'=>'多文件','url'=>'外链资源'])->help('允许添加的附件类型');
		$this->text('ext', '文件后缀')->help('单、多文件上传时限止的文件后缀,不填则不限');
		$this->number('maxsize', '文件上传大小限止')->default(0)->help('单位K,0为默认2048K');		
		$this->switch('cover','开启封面')->help('开启附件封面功能');
		$this->switch('note','开启说明')->help('开启附件说明功能');
    }

}
