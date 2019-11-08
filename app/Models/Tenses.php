<?php namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\SoftDeletes;

class Tenses extends Model
{
    use SoftDeletes;
    
    protected $hidden = ['updated_at','created_at', 'deleted_at'];
    
    protected $fillable = ['name','moods_id'];

    protected $casts = [
        'moods_id' => 'integer',
    ];
  
    /**
    * @Relation
    */	  
    public function mood()
    {
        return $this->belongsTo('App\Models\Moods', 'moods_id', 'id');
    }

}