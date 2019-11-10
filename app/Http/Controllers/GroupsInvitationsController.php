<?php

namespace App\Http\Controllers;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Symfony;
use ApiHandler;
use Auth;
use Illuminate\Support\Facades\View;

use App\Traits\FileTraits;
use App\Models\OrganizationsInvitations;
use App\Models\Members;
use App\Models\MembersOrganizations;
use App\Models\Organizations;
use App\Traits\EmailTraits;
use App\Traits\StringTraits;

use Illuminate\Database\Eloquent\Builder;

class OrganizationsInvitationsController extends Controller
{
    
    use StringTraits;
    use EmailTraits;

    /**
     * Make sure that the requesting member is either an admin of this organization OR a sitewide superadmin
     * @param int The organization ID
     */
    public function abort_unless_admin($id){

        // $user = Auth::user();

        // if ($user->role != 'super' && NOT_ORG_ADMIN) {
        //     abort(404, 'You are not an administrator of this organization.');
        // }

    }

    /**
     * If this member already exists in the organization, return an appropriate email, otherwise send the invite whether the member exists or not, because we'll handle that at a later state (new account creation vs not)
     * @param string Email address of potential new team member
     * @param mixed Organization
     */  
    public function abort_if_member_already_in_organization($email, $organization){
        $existing_member = Members::where('email',$email)->whereHas('organizations', function (Builder $query) use($organization) {
            $query->where('organizations.id', $organization->id);
        })->first();
        if($existing_member){
            return ['error' => $email. ' is already a member of ' . $organization->name];            
        }
    }

    /**
     * Gets all pending invitations for this organization
     * @param Request The laravel HTTP request object.
     * @param int The organization ID to return invitations for.
     * @return mixed
     */
    public function find(Request $request, $id)
    {

        //$this->abort_unless_admin($id);

        //get default results
        $organizations_invitations= OrganizationsInvitations::where('organizations_id', $id);
      
        //filter based on QS
        $organizations_invitations = ApiHandler::parseMultiple($organizations_invitations);
      
        return $organizations_invitations->getResult();
    } 

    /**
     * Gets a single pending invitationorganization
     * @param Request The laravel HTTP request object.
     * @return mixed
     */
    public function get(Request $request, $id)
    {        

        //get default results
        $organizations_invitation = OrganizationsInvitations::find($id);

        if(!$organizations_invitation) {
            abort(404, 'Organization Invitation not found.');
            return null;
        }	
      
        //filter based on QS
        $organizations_invitation = ApiHandler::parseSingle($organizations_invitation, $id);
      
        return $organizations_invitation->getResult();
    } 


    /**
     * Creates an Invitation
     * @param Request The laravel HTTP request object.
     * @return mixed
     */
    public function create(Request $request)
    {       
        //validation
        try{
            $this->validate($request, [
                'email' => 'required|email|max:255',                
                'organizations_id' => 'required|exists:organizations,id',                
                'organization_role' => 'sometimes|required|in:admin,member,viewer'     
            ]);
        }catch( \Illuminate\Validation\ValidationException $e ){
            return $e->getResponse();
        }                              

        $request['email'] = strtolower($request['email']);

        $inviter = Auth::user();
        $organization = Organizations::find($request['organizations_id']);
        $request['inviter_members_id'] = $inviter->id;
       
        $this->abort_if_member_already_in_organization($request['email'], $organization);        

        //If this invitation already exists, don't send it again. We don't want to be spamming people. Or at least add a 'last_emailed' field so we don't send two emails in quick succession.        
        $existing_invitation = OrganizationsInvitations::where('email', $request['email'])->where('organizations_id', $request['organizations_id'])->first();
        if($existing_invitation){
            return ['error' => 'An invitation to ' . $organization->name . ' has already been sent to ' . $request['email']];
        }

        $request['token'] = $this->generateRandomString(40);

        //Create the invitation in the database.
        $invitation = OrganizationsInvitations::create($request->all());

        //Send the invitation via email
        $message = View::make('emails.inviteMemberToTeam')->with(array( 'invitation' => $invitation, 'inviter' => $inviter ))->render();
        $this->emailSend($inviter->username. ' has invited you to their team!', $invitation->email, $message);
        

        return $invitation;

    } 

     /**
     * Accepts and Invitation
     * @param Request The laravel HTTP request object.
     * @param int The ID of the invitation.
     * @param string The authentication token.
     * @return mixed
     */
    public function accept(Request $request, $id, $token)
    {
        $invitation = OrganizationsInvitations::find($id)->where('token', $token)->where('status','pending')->first();
        if(!$invitation){
            abort(404, 'Invitation not found.');
            return null;
        }        

        $this->abort_if_member_already_in_organization($invitation->email, $invitation->organization);

        $member = Members::where('email', $invitation->email)->first();

        if(!$member){
            //Need to create the member.
            $member = Members::create([
                'username' => '',
                'email' => $invitation->email,
                'password' => ''
            ]);
        }

        MembersOrganizations::create([
            'members_id' => $member->id,
            'organizations_id' => $invitation->organizations_id,
            'role' => $invitation->role
        ]);

        $invitation->status = 'accepted';
        $invitation->save();

        $inviter = Members::find($invitation->inviter_members_id);

        //Send an email to the inviter to let her know that her invitation was a success.
        $message = View::make('emails.invitationAccepted')->with(array( 'invitation' => $invitation ))->render();
        $this->emailSend($invitation->email. ' has accepted your invite.', $inviter->email, $message);

        return ['success' => 'Invitation accepted.'];

    }


    /**
         * Updates an organization.
         *
         * @param Request The laravel HTTP request object.
         * @param int The organization ID.
         * @return mixed
    */
    public function update(Request $request, $id)
    {
        //validation
        try{
            $this->validate($request, [
                'name' => 'sometimes|required|max:255',
                'address' => 'sometimes|required|max:255',
                'city' => 'sometimes|required|max:255',
                'states_id' => 'sometimes|required|exists:states,id',
                'is_publisher' => 'sometimes|required|boolean',
                'logo' => 'sometimes|required|file|mimes:jpeg,png'
            ]);
        }catch( \Illuminate\Validation\ValidationException $e ){
            return $e->getResponse();
        }        
        
        $organizations_invitation = OrganizationsInvitations::find($id);
        if (!$organizations_invitation) {
            abort(404, 'Organization Invitation not found.');
            return null;
        }

        if($request->hasFile('logo') ) {
            $request['image_logo_files_id'] = $this->fileSave( $request->file('logo'), 'organization_logos' );
        }                  
        
        $organizations_invitation->update($request->except('logo'));
        return $organizations_invitation;
    }

    /**
     * Deletes an organization.
     *
     * @param Request The laravel HTTP request object.
     * @param int The organization ID.
     * @return mixed
     */
    public function delete(Request $request, $id)
    {
        $organizations_invitation = OrganizationsInvitations::find($id);
        if (!$organizations_invitation) {
            abort(404, 'Organization Invitation not found.');
            return null;
        }

        $organizations_invitation->delete();
        
        return null;
    }

    /**
     * Restores a deleted organization.
     *
     * @param Request The laravel HTTP request object.
     * @param int The organization ID.
     * @return mixed
     */
    public function restore(Request $request, $id)
    {
        $organizations_invitation = OrganizationsInvitations::onlyTrashed()->where('id',$id)->first();
        if (!$organizations_invitation) {
            abort(404, 'Organization Invitation not found.');
            return null;
        }

        $organizations_invitation->restore();
        
        return null;
    }
    

}