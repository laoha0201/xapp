<?php
namespace App\Models\Traits;

use Ramsey\Uuid\Uuid as Uuuid;

trait Uuid
{
	public static function boot()
	{
		parent::boot();
		self::creating(function ($model) {     //创建时生成uuid
			$model->{$model->getKeyName()} = Uuuid::uuid4()->toString();
		});
	} 
}
