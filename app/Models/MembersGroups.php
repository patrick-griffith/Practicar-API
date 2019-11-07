<?php namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\SoftDeletes;

class MembersOrganizations extends Model
{
    use SoftDeletes;
    
    protected $hidden = ['updated_at','created_at', 'deleted_at', 'pivot'];
    
    protected $fillable = ['members_id', 'organizations_id', 'role'];

    protected $casts = [
        'members_id' => 'integer',
        'organizations_id' => 'integer',
        'is_admin' => 'boolean'
    ];

    
  
}