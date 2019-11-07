<?php namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\SoftDeletes;

class Files extends Model
{
    use SoftDeletes;
    
	protected $table = 'files';
    protected $guarded = ['id']; 
    protected $hidden = ['updated_at','created_at', 'deleted_at'];
    protected $casts = [
        'size' => 'integer'
    ];
    protected $appends = array('url');
    
    public function getUrlAttribute(){
        return env('GOOGLE_CLOUD_STORAGE_PUBLIC_URI', null).$this->attributes['path'].'/'.$this->attributes['id'].'.'.$this->attributes['extension'];
    }    
    
}