<?php namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\SoftDeletes;

class Members extends Model implements Authenticatable
{
    use SoftDeletes;
    
    use \Illuminate\Auth\Authenticatable;
  
    protected $table = 'members';
    protected $guarded = ['id'];
    protected $hidden = ['password', 'pivot', 'created_at', 'updated_at', 'forget_token', 'remember_token', 'deleted_at'];
    protected $fillable = ['first_name', 'last_name', 'email'];
    protected $appends = array('email_obfuscate');

    protected $casts = [
        'is_admin' => 'boolean',
    ];
    
    /**
    * @Relation
    */
    public function groups()
    {
        return $this->belongsToMany('App\Models\Groups');
    }

    /**
    * @Relation
    */	  
    public function comments()
    {
        return $this->hasMany('App\Models\Comments');
    }


    /**
    * @Relation
    */	  
    public function image()
    {
        return $this->hasOne('App\Models\Files', 'id', 'image_files_id');
    }
  


    
    public function getEmailObfuscateAttribute(){
        if( isset( $this->attributes['email'] ) )
            return preg_replace('/(?<=.).(?=.*@)/u','*',$this->attributes['email']);
    }       
    
    
    
}