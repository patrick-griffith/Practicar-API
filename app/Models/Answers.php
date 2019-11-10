<?php namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\SoftDeletes;

class Answers extends Model
{
    use SoftDeletes;
    
    protected $hidden = ['updated_at','created_at', 'deleted_at'];
    
    protected $fillable = ['rounds_id', 'members_id' ,'conjugations_id','answer','score','seconds_elapsed'];

    protected $casts = [
        'rounds_id' => 'integer',
        'conjugations_id' => 'integer',
        'seconds_elapsed' => 'double'
    ];

    /**
    * @Relation
    */	  
    public function conjugation()
    {
        return $this->hasOne('App\Models\Conjugations', 'id', 'conjugations_id');
    }
    
  
}