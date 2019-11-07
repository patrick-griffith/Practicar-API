<?php namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\SoftDeletes;

class GroupsInvitations extends Model
{
    use SoftDeletes;
    
    protected $hidden = ['updated_at','created_at', 'deleted_at', 'pivot'];
    
    protected $fillable = ['groups_id', 'inviter_members_id', 'email', 'group_role', 'token'];

    protected $casts = [
        'groups_id' => 'integer',
        'inviter_members_id' => 'integer'        
    ];

    /**
    * @Relation
    */	  
    public function group()
    {
        return $this->hasOne('App\Models\Groups', 'id', 'groups_id');
    }
    
  
}