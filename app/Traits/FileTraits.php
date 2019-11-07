<?php namespace App\Traits;

use Dingo\Api\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Models\Files;

trait FileTraits
{
    
    public function fileSave($fileRequest, $path='/')
    {
        $fileInfo = array(
            'path' => $path,
            'extension' => $fileRequest->getClientOriginalExtension(),
            'size' => $fileRequest->getSize(),
            'mime' => $fileRequest->getMimeType(),
            'checksum' => md5_file($fileRequest->getRealPath()),
        );
        
        //check for existing
        $fileCheck = Files::where( 'checksum', '=', $fileInfo['checksum'] )->first();
      
        if( $fileCheck ){
            return $fileCheck->id;
        }
        
        //create new record        
        $fileNew=Files::Create( $fileInfo );  
        
        //upload
        $disk = Storage::disk('local'); //TODO, change to 'gcs'        
      
        $filePath = rtrim( $fileInfo['path'], '/').'/'.$fileNew->id.'.'.$fileInfo['extension'];
      
        //delete file if already there (should'nt be due to checksum)
        Storage::delete( $filePath );
        
        //create file
        $disk->write( $filePath, file_get_contents($fileRequest->getRealPath()), array( 'visibility'=>'public') );
        
        return $fileNew->id;

    }
    
    

  
  
}