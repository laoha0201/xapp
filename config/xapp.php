<?php
return [
    'xappset' => [
        'cate' => [      //采用独立表管理
			'title'=>'分类',
			'field'=>'cate_id', //应用实例表中必须包含的字段
			'set'=>'App\Admin\Forms\Xappsets\Cate',  //设置表单 admin/laoha/xapp/{xapp_id}/set?active=cate_set
			'manager'=>'cate',  //应用管理路由 admin/laoha/xapp/{xapp_id}/cate
			'model' => 'App/Models/Cate',
			'ctrl'  =>  [                                      //控制基类
						'admin' => 'App\Admin\Controllers\Ext\Cate', 
					],
		],
		'pos'=> [        //采用应用管理表设置
		    'title'=>'位置',
			'field'=>'pos', //应用实例表中必须包含的字段
			'set'=>'App\Admin\Forms\Xappsets\Pos',  
			'ctrl'  =>  [                                      //控制基类
						'admin' => 'App\Admin\Controllers\Ext\Pos', 
					],
		],
		'attachment'=> [        //采用应用管理表设置
		    'title'=>'附件',
			'set'=>'App\Admin\Forms\Xappsets\Attachment',  
			'cate_set'=>'App\Admin\Forms\Catesets\Attachment',   //允许分类中单独设置
			'model' => 'App/Models/Attachment',
			'ctrl'  =>  [                                      //控制基类
						'admin' => 'App\Admin\Controllers\Ext\Attachment', 
					],
		],
		'checked'=> [        
		    'title'=>'审核',
			'field'=>'checked', //应用实例表中必须包含的字段		
			'ctrl'  =>  [                                      //控制基类
						'admin' => 'App\Admin\Controllers\Ext\Checked', 
					],
		],
		'comment'=> [        
		    'title'=>'评论',
			'field'=>'comments', //本地评论
		],
		'view'=> [        
		    'title'=>'计数',
			'field'=>'views', //计数功能
		],
	],
	'table' => [
		'posts' => [
			'model' => 'App\Models\Post',
			'ctrl'  =>  [                                      //控制基类
					'admin' => 'App\Admin\Controllers\Xapps\Posts', 
					'web'   => 'App\Http\Controllers\Xapps\Posts',
					'api'   => 'App\Http\Api\Controllers\Xapps\Posts',
				],
			'set'       => 'App\Admin\Forms\Xappsets\Post',  //应用中设置，主体表单等
			'cate_set'  => 'App\Admin\Forms\Catesets\Post',  //允许分类中单独设置
		],
		'pages' => [
			'model' => 'App/Models/Page',
			'ctrl'  => [
				'admin' => 'App/Admin/Controllers/PagesController',
				'web'   => 'App/Http/Controllers/PagesController',
				'api'   => 'App/Http/Api/Controllers/PagesController',
			],
		],		
	],
	'editor' =>['summernote','ueditor','editormd','json-editor','wang-editor','php-editor','simditor','kindeditor','ckeditor'],
];