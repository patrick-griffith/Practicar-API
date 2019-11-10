<?php

namespace App\Http\Controllers;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\DB;
use Validator;
use Symfony;
use JWTAuth;
use Session;
use Auth;
use ApiHandler;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;

use App\Models\Members;

use App\Traits\StringTraits;
use App\Traits\EmailTraits;
use App\Traits\FileTraits;
use App\Traits\MemberTraits;

class MembersController extends Controller
{
	
    use StringTraits{
        StringTraits::generateRandomString insteadof MemberTraits;
        StringTraits::percentToStars insteadof MemberTraits;
        StringTraits::percentToGrade insteadof MemberTraits;
    }
    use EmailTraits{
        EmailTraits::emailSend insteadof MemberTraits;
    }
    use MemberTraits, FileTraits;
	
    /**
     * Returns all members for a given member search
     *
     * @param Request $request
     * @returns mixed
     */
    public function find()
    {
        //get default results        
        $members = Members::where('id','>',0);
      
        //filter based on QS
        $members = ApiHandler::parseMultiple( $members, array('name'));
      
        return $members->getResult();
    }
	
    /**
     * Returns a single member
     *
     * @param Request $request
     * @param int The Member ID.
     * @return mixed
     */
    public function get(Request $request, $id)
    {
        //get default results
        $member = Members::find($id);

        if (!$member) {
            abort(404, 'member not found.');
            return null;
        }	

        //filter based on QS
        $items = ApiHandler::parseSingle($member, $id);

        return $items->getResult();
    }

    /**
     * Returns the authenticated member
     *
     * @param Request $request
     * @return mixed
     */
    public function me(Request $request)
    {        
        $id = Auth::user()->id;        
        $member = Members::find($id);

        if (!$member) {
            abort(404, 'member not found.');
            return null;
        }	

        //filter based on QS
        $items = ApiHandler::parseSingle($member, $id);

        return $items->getResult();
    }

    /**
     * Returns member's password
     * businesses
     *
     * @param $id int
     * @param $value string
     * @return json
     */
    public function checkCurrentPassword($id, $value)
    {
        $member = DB::select( DB::raw("SELECT password FROM members WHERE id = :id limit 1"), ['id' => $id ]);

        if (!$member) {
            abort(404, 'member not found.');
            return null;
        }
        $valid = (!Hash::check($value, $member[0]->password)) ? false : true;

        return response()->json([
            'member' => [
                'password' => $valid
            ]
        ]);
    }

    /**
    * Creates a new blog.
    *
    * @param Request The laravel HTTP request object.
    */
    public function create(Request $request)
    {
        //validation
        try{
            $this->validate($request, [
                'email' => 'required|email|max:255|unique:members',
                'email_updates' => 'required|sometimes|boolean'
            ]);
        }catch( \Illuminate\Validation\ValidationException $e ){
            return $e->getResponse();
        }                                        

        $member = Members::create($request->all());                        

        //Update these separately as these are not publicly fillable
        $passwordRaw = $this->generateRandomString();
        $member->password = app('hash')->make($passwordRaw);
        $member->save();
        
        $member->passwordRaw = $passwordRaw;
        
        //$message = View::make('emails.confirmEmail')->with(array( 'member'=> $member ))->render();
        //$this->emailSend('Please confirm your email address.', $member->email, $message);

        $message = View::make('emails.memberWelcome')->with(array( 'member'=> $member ))->render();
        $this->emailSend('bienvenidos a PRACTICAR', $member->email, $message);
        
        return $member;
    }

  
    /*
    * Logout User
    */
    public function logout()
    {
        Auth::logout();
        return ['success' => 'Successfully logged out.'];
    }

    /**
	 * This should only work once, to claim the account
     * @param Request $request
     * @return mixed
     */
    public function setCustomPassword(Request $request, $id)
    {
        if(Auth::user()->id != $id){                        
            return response()->json(['error' => 'Your account does not have sufficient permissions to perform this action.'], 403);
        }   

         //validation
         try{
            $this->validate($request, [
                'password' => 'required|min:3'
            ]);
        }catch( \Illuminate\Validation\ValidationException $e ){
            return $e->getResponse();
        }     

        $member = Members::find($id);
        if (!$member) {
            abort(404, 'member not found.');
            return null;
        }

        if($member->first_name){
            abort(404, 'An initial password has already been set for this member.');
            return null;
        }else{
            $member->password = app('hash')->make($request->input('password'));
            $member->save();
        }

        return $member;

    }

    /**
	
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()->id != $id && Auth::user()->role != 'super'){                        
            return response()->json(['error' => 'Your account does not have sufficient permissions to perform this action.'], 403);
        }        

        //validation
        try{
            $this->validate($request, [
                'first_name' => 'sometimes|required|max:255',
                'last_name' => 'sometimes|required|max:255',	
                'email' => 'sometimes|required|email|max:255|unique:members,email,'.$id,
            ]);
        }catch( \Illuminate\Validation\ValidationException $e ){
            return $e->getResponse();
        }        
        
        $member = Members::find($id);
        if (!$member) {
            abort(404, 'member not found.');
            return null;
        }
           
        $member->update($request->all());        

        return $member;
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function passwordReset(Request $request)
    {
        //validation
        try{
            $this->validate($request, [
                'email' => 'required|min:3|email',
                'resetURL'=>'required|max:255'
            ]);
        }catch( \Illuminate\Validation\ValidationException $e ){
            return $e->getResponse();
        }           
        
        //check and send link to reset password
        $email = $request->input('email');
        $member = Members::where('email',$email)->first();

        if ($member) {
            $token = $this->generateRandomString();
            Members::where('email', $email)->update(['forget_token' => $token]);

            $member['resetURL'] = $request->input('resetURL').$token;
            $message = View::make('emails.memberForgotPassword')->with(array( 'member'=> $member ))->render();
            $this->emailSend('Password Reset', $member->email, $message);

          	return ['success'=>'Your password reset link has been emailed to you.'];
        }
        return ['error'=>"This email address doesn't exist."];

    }

    public function passwordChange(Request $request)
    {
        //validation
        $this->validate($request, [
            'token' => 'required|min:3',
            'password'=>'required|min:3|max:20'
        ]);

        //verify and update password, and expire token
        $member = Members::where('forget_token', $request->input('token'))->first();
        if ($member) {
            $member->password = app('hash')->make($request->input('password'));
            $member->forget_token = '';
            $member->save();
          	return ['success'=>'Your password has been updated.'];
        }
        return ['error'=>"Reset token is invalid or expired."];
    }

    /**
     * Deletes a Member.
     *
     * @param Request The laravel HTTP request object.
     * @param int The Member ID.
     * @return mixed
     */
    public function delete(Request $request, $id)
    {

        if(Auth::user()->id != $id && Auth::user()->role != 'super'){            
            return response()->json(['error' => 'Your account does not have sufficient permissions to perform this action.'], 403);
        }  

        $member = Members::find($id);
        if (!$member) {
            abort(404, 'Member not found.');
            return null;
        }

        $member->delete();
        
        return null;
    }


    /**
     * Restores a deleted member.
     *
     * @param Request The laravel HTTP request object.
     * @param int The member ID.
     * @return mixed
     */
    public function restore(Request $request, $id)
    {
        $member = Members::onlyTrashed()->where('id',$id)->first();
        if (!$member) {
            abort(404, 'member not found.');
            return null;
        }

        $member->restore();
        
        return null;
    }

}