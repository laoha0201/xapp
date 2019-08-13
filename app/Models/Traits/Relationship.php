<?php
namespace App\Models\Traits;

trait Relationship
{
	public function cate()  //分类
    {
        return $this->belongsTo(\App\Models\Cate::class, 'cate_id');
    }

    public function user()  //用户
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

	public function attachments()  //附件
    {
        return $this->hasMany(\App\Models\Attachment::class, 'root_id','id');
    }


    public function scopePos($query,$pos='')  //位置过滤
    {
        if($pos){		
			return $query->whereRaw('FIND_IN_SET(?,pos)', [$pos]);
		}
    }

    public function scopeCateId($query,$cate_id=0,$xapp_id=0) //分类过滤，包括子节点
    {
		if($cate_id){
			if(empty($xapp_id)){
				$rs = \App\Models\Cate::findOrFail($cate_id);
				$xapp_id = $rs->xapp_id;
			}
			$cate = new \App\Models\Cate();
			$ids = $cate->getnodeids($cate_id,$xapp_id);
			return $query->whereIn('cate_id', $ids);
		} 
    }


    /*
    public function scopeGroup($query,$cate_id='',$group='')  //分类组过滤
    {
        if($cate_id && $group){		
			return $query->where('cate_id',$cate_id)->where('group',$group);
		}
    }
    
    public function scopeUserId($query,$user_id)  //用户过滤
    {
        return $query->where('user_id',$user_id);
    }    

    public function scopeChecked($query,$checked)  //审核过滤
    {
        return $query->where('checked',$checked);
    }    
    */
}
