<?php namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\SoftDeletes;

class Groups extends Model
{
    use SoftDeletes;
    
    protected $hidden = ['updated_at','created_at', 'deleted_at', 'pivot'];
    
    protected $fillable = ['name'];

    protected $casts = [
        'states_id' => 'integer',
        'is_publisher' => 'boolean'
    ];

    
    /**
    * @Relation
    */
    public function members()
    {
        return $this->belongsToMany('App\Models\Members');
    }

    /**
    * @Relation
    */	  
    public function file()
    {
        return $this->hasOne('App\Models\Files', 'id', 'image_logo_file_id');
    }

    //TODO: belongs to many stories (many to many)
    
  
}