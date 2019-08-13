<?php
namespace App\Admin\Controllers\Ext;

class Attachment
{
    /**
     * add a grid here.
     */
    public function grid($grid,$xapp)
    {
			$grid->model()->withCount('attachments');
			$grid->column('attachments_count', '附件');
	}

    /**
     * add a form here.
     */
    public function form($form,$xapp,$rs=false)
    {
			$form->hasMany('attachments', '添加附件列表', function (\Encore\Admin\Form\NestedForm $form) use($xapp){
				$type=$xapp['sets']['attachment']['type'] ?? 'file';
				$form->hidden('type')->default($type);
				$form->text('title','标题');
				$options['maxFileSize']=empty($xapp['sets']['attachment']['maxsize']) ? 2048 : $xapp['sets']['attachment']['maxsize'];
				if(!empty($xapp['sets']['attachment']['cover'])) $form->image('image','封面');
				switch ($type) {
					case 'image':
						$form->image('attach',!empty($xapp['sets']['attachment']['title']) ? $xapp['sets']['attachment']['title'] : '图片')->options($options);
						break;
					case 'images':
						$form->multipleImage('attaches',!empty($xapp['sets']['attachment']['title']) ? $xapp['sets']['attachment']['title'] : '多图片')->options($options)->removable()->sortable();
						break;
					case 'files':
						if(!empty($xapp['sets']['attachment']['ext'])){
							$options['allowedFileExtensions'] = str2arr($xapp['sets']['attachment']['ext']);
						}
						$form->multipleFile('attaches',!empty($xapp['sets']['attachment']['title']) ? $xapp['sets']['attachment']['title'] : '多文件')->options($options)->removable()->sortable();
						break;
					case 'url':
						$form->url('url',!empty($xapp['sets']['attachment']['title']) ? $xapp['sets']['attachment']['title'] : '链接');
						break;		
					default:
						if(!empty($xapp['sets']['attachment']['ext'])){
							$options['allowedFileExtensions'] = str2arr($xapp['sets']['attachment']['ext']);
						}
						$form->file('attach',!empty($xapp['sets']['attachment']['title']) ? $xapp['sets']['attachment']['title'] : '文件')->options($options);
						break;
				}
				if(!empty($xapp['sets']['attachment']['note'])){
					$form->textarea('note','附件说明');
				}
			});
    }

    public function show($show,$xapp,$rs=false)
    {
			$show->field('attachments','附件')->unescape()->as(function ($attachments) {
				$str = "<div>";
				foreach($attachments as $attach){
					if( !empty($attach['title']) ){
						$str .= "<h3>".$attach['title']."</h3>";
					}
					if( !empty($attach['cover']) ){
						$str .= "<p><img src='/upload/small/".$attach['cover']."' /></p>";
					}
					if( !empty($attach['attach']) ){
						if($attach['type'] == 'image'){
							$str .= "<p><img src='/upload/small/".$attach['attach']."' /></p>";
						}else{
							$str .= "<p><a href='/upload/{$attach['attach']}'>{$attach['attach']}</a></p>";
						}
					}	
					if( !empty($attach['attaches']) ){
						foreach($attach['attaches'] as $k=>$att){
							if($attach['type'] == 'images'){
								$str .= "<img src='/upload/small/{$att}' />";
							}else{
								$str .= "<p>文件: <a href='/upload/{$att}'>{$att}</a></p>";
							}
						}
						$str .= "<hr />";
					}				
					if(!empty($attach['url']) ){
						$str .= "链接: <p><a href='/upload/{$attach['url']}'>{$attach['title']}</a></p>";
					}
					if( !empty($attach['note']) ){
						$str .= "<p>".$attach['note']."</p>";
					}
				}				
				$str .= "</div>";
				return $str;
			});
	}
}