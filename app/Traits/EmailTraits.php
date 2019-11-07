<?php namespace App\Traits;

use Dingo\Api\Http\Request;

trait EmailTraits
{
    //Sending Email through SendGrid API
    public function emailSend($subject, $to=null, $message, $delay=0,$from=array(),$attach=null)
    {

      if( $to == null ) $to = env('EMAIL_FROM_ADDRESS');
      
      if( count($from) == 0 ){
          $from['email'] = env('EMAIL_FROM_ADDRESS');
          $from['name'] = env('EMAIL_FROM_NAME');
          $from['bcc'] = env('EMAIL_FROM_ADDRESS');
      }

      $sendgrid_apikey = env('SENDGRID_API_KEY');
      $url = 'https://api.sendgrid.com/';

        $params = array(
          'to'        => $to,
          'toname'    => $to,
          'from'      => $from['email'],
          'fromname'  => $from['name'],
          'subject'   => $subject,
          'bcc'   	  => $from['bcc'],
          'html'      => $message
        );
      
        if( $delay > 0 ){
            $params['x-smtpapi'] = json_encode(array('send_at'=>strtotime( '+'.$delay.' minutes UTC')));
        }
     
      $request =  $url.'api/mail.send.json';
      
      if( $attach != null && is_array( $attach ) ){

          if ( isset( $attach['contents'] ) ){
            
              $temp_file = tempnam( sys_get_temp_dir(), $attach['name'] );
              
              $handle = fopen( $temp_file, 'w' );
              fwrite( $handle,  $attach['contents'] );
              $path = stream_get_meta_data( $handle )['uri'];
              fclose( $handle );
            
              $params['files['.$attach['name'].']']  = curl_file_create( $path, $attach['name']);
          }
          else{
              $params['files['.$attach['name'].']']  = curl_file_create( $attach['path'], $attach['name'] );
          }

      }
      
      // Generate curl request
      $session = curl_init($request);
      // Tell PHP not to use SSLv3 (instead opting for TLS)
      curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
      curl_setopt($session, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $sendgrid_apikey));
      // Tell curl to use HTTP POST
      curl_setopt ($session, CURLOPT_POST, true);
      // Tell curl that this is the body of the POST
      curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
      // Tell curl not to return headers, but do return the response
      curl_setopt($session, CURLOPT_HEADER, false);
      curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

      // obtain response
      $response = curl_exec($session);
      
      curl_close($session);
      
      if( isset( $temp_file ) ){
         unlink( $temp_file );
      }
      
      // print everything out
      // print_r($response);

      return;

    }
  
}