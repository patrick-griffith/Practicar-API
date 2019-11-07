<?php namespace App\Traits;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Symfony;
use Validator;

use Illuminate\Support\Facades\View;

use App\Models\MembersBusinesses;
use App\Models\Members;
use App\Models\Businesses;

use App\Traits\StringTraits;
use App\Traits\EmailTraits;

trait MemberTraits
{
	
    use StringTraits;	
    use EmailTraits;
	
    public function memberAdd( $profile, $emailSend=false )
    {

        $member = Members::where('email', $profile['email'] )->first();
        if ($member) {
						$member->update($profile);
            return $member;
        }
        
        //if new member, send email with info
        $passwordRaw = $this->generateRandomString();
        $profile['password'] = app('hash')->make($passwordRaw);

        $member = Members::firstOrCreate($profile);
        $member->passwordRaw = $passwordRaw;
      
        if( $emailSend ) {
          $message = View::make('emails.memberWelcome')->with(array( 'member'=> $member ))->render();
          $this->emailSend('Welcome to the Portal', $member->email, $message);
        }
           
        return $member;
    }

	
    public function memberAddToBusiness($membersId, $businessesId)
    {
        MembersBusinesses::firstOrCreate([
            'businesses_id' => $businessesId,
            'members_id' => $membersId
        ]);
    }

}