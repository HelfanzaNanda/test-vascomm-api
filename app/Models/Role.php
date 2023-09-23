<?php

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\CreatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends ModelsRole
{
    use HasFactory, SoftDeletes, Blameable, CreatedBy;


	public function user_ids()
	{
		return $this->belongsToMany(User::class,
			'model_has_roles',  'model_id', 'role_id')
			// ->withPivot(['id', 'regional_id', 'active_date', 'inactive_date'])
			->using(UserRole::class);
	}
}
