<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Xapp extends Model
{
	use SoftDeletes;

	public $casts = ['sets'=>'json','ctrl'=>'json'];  //设置字段格式 
	protected $fillable = ['order'];

	/**
	 * 为路由模型获取键名。
	 *
	 * @return string
	 */
	public function getRouteKeyName()
	{
		return 'name';
	}

    protected static function boot() {    
        parent::boot();
        static::deleting(function($model) {
			if($model->forceDeleting){
				$model->cates()->get(['id'])->each->delete();
				/*
				try {
					DB::connection()->pdo->beginTransaction();
					foreach(config('laoha.models') as $tb=>$model){
						$photos = Photo::where('user_id', '=', $user_id)->delete(); // Delete all photos for user					
					}
					$user = Geofence::where('id', '=', $user_id)->delete(); // Delete users
					DB::connection()->pdo->commit();

				}catch(\Laravel\Database\Exception $e) {
					DB::connection()->pdo->rollBack();
					Log::exception($e);
				}*/
			}
        });

        static::saved(function($model) {
			if( isset($model->syncChanges()->changes['parent_id']) || isset($model->syncChanges()->changes['create_at']) ){
				$data = $model->set_order();
				foreach($data as $id=>$order){
					$model->where('id', $id)->update(['order' => $order]);
				}
			}
        });
    }



	public function cates()
	{
		return $this->hasMany(Cate::class);
	}

	public function childs()
	{
		return $this->hasMany($this,'parent_id');
	}

	protected function set_order()
	{
		$lists = $this->where('parent_id',0)->with('childs')->get()->toArray();
		$rs = [];
		$order = 0;
		foreach($lists as $app){
			$order ++;
			//$rs[] = ['id'=>$app['id'],'order'=>$order];		
			$rs[$app['id']] = $order;
			if($app['childs']){
				foreach($app['childs'] as $sub){
					$order ++;
					$rs[$sub['id']] = $order;
				}		
			}
		}
		return $rs;
	}
}
