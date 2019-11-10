<?php namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\SoftDeletes;

class Verbs extends Model
{
    use SoftDeletes;
    
    protected $hidden = ['updated_at','created_at', 'deleted_at'];
    
    protected $fillable = ['spanish','usage', 'is_irregular'];

    protected $casts = [
        'is_irregular' => 'boolean'
    ];

    /**
    * @Relation
    */	  
    public function conjugations()
    {
        return $this->hasMany('App\Models\Conjugations');
    }
    
  
}