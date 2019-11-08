<?php namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\SoftDeletes;

class Moods extends Model
{
    use SoftDeletes;
    
    protected $hidden = ['updated_at','created_at', 'deleted_at'];
    
    protected $fillable = ['name'];

    /**
    * @Relation
    */	  
    public function tenses()
    {
        return $this->hasMany('App\Models\Tenses', 'moods_id', 'id');
    }
  
}