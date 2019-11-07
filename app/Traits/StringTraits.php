<?php namespace App\Traits;

use Dingo\Api\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\RangesTiers;

trait StringTraits
{

    public function generateRandomString($length = 10) 
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
  
    public function percentToStars($percent) 
    {
        $percent = $percent/100;
        
        $stars_raw = 5*$percent;
        $stars = .5*ceil( $stars_raw/.5);

        return $stars;
    }    
    
    public function percentToGrade($percent) 
    {
        $tier = RangesTiers::where( 'ranges_id', 1004 )        
        ->where('notch', '>=', $percent )
        ->orderBy('max')
        ->limit(1)            
        ->first();
        
        return $tier->label;
    }
    
}