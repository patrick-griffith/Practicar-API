<?php namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\SoftDeletes;

class Conjugations extends Model
{
    use SoftDeletes;
    
    protected $hidden = ['updated_at','created_at', 'deleted_at'];
    
    protected $fillable = ['verbs_id', 'persons_id', 'tenses_id', 'spanish', 'english', 'is_irregular'];

    protected $casts = [
        'verbs_id' => 'integer',
        'persons_id' => 'integer',
        'tenses_id' => 'integer',
    ];

    /**
    * @Relation
    */	  
    public function verb()
    {
        return $this->belongsTo('App\Models\Verbs', 'verbs_id', 'id');
    }
 
    /**
    * @Relation
    */	  
    public function tense()
    {
        return $this->belongsTo('App\Models\Tenses', 'tenses_id', 'id');
    }

    /**
    * @Relation
    */	  
    public function person()
    {
        return $this->belongsTo('App\Models\Persons', 'persons_id', 'id');
    }
}