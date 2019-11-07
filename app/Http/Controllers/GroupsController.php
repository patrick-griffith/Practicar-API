<?php

namespace App\Http\Controllers;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Symfony;
use ApiHandler;

use App\Traits\FileTraits;
use App\Models\Organizations;

class OrganizationsController extends Controller
{
    use FileTraits;

    //TODO: Create ability to update an image. Right now we can upload an image with initial organization create method, but we have no ability to change it.

    /**
     * Gets all organizations
     * @param Request The laravel HTTP request object.
     * @return mixed
     */
    public function find(Request $request)
    {
        //get default results
        $organizations= Organizations::where('id','>',0);
      
        //filter based on QS
        $organizations = ApiHandler::parseMultiple($organizations);
      
        return $organizations->getResult();
    } 

    /**
     * Gets a single organization
     * @param Request The laravel HTTP request object.
     * @parem int the id of the organization
     * @return mixed
     */
    public function get(Request $request, $id)
    {
        //get default results
        $organization = Organizations::find($id);

        if(!$organization) {
            abort(404, 'Organization not found.');
            return null;
        }	
      
        //filter based on QS
        $organization = ApiHandler::parseSingle($organization, $id);
      
        return $organization->getResult();
    } 


    /**
     * Creates an Organization
     * @param Request The laravel HTTP request object.
     * @return mixed
     */
    public function create(Request $request)
    {       
        //validation
        try{
            $this->validate($request, [
                'name' => 'required|max:255',
                'address' => 'required|max:255',
                'city' => 'required|max:255',
                'states_id' => 'required|exists:states,id',
                'is_publisher' => 'sometimes|required|boolean',
                'logo' => 'sometimes|required|file|mimes:jpeg,png'
            ]);
        }catch( \Illuminate\Validation\ValidationException $e ){
            return $e->getResponse();
        }                              

        if($request->hasFile('logo') ) {
            $request['image_logo_files_id'] = $this->fileSave( $request->file('logo'), 'organization_logos' );
        } 

        $organization = Organizations::create($request->except('logo'));
        return $organization;

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
        
        $organization = Organizations::find($id);
        if (!$organization) {
            abort(404, 'Organization not found.');
            return null;
        }

        if($request->hasFile('logo') ) {
            $request['image_logo_files_id'] = $this->fileSave( $request->file('logo'), 'organization_logos' );
        }                  
        
        $organization->update($request->except('logo'));
        return $organization;
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
        $organization = Organizations::find($id);
        if (!$organization) {
            abort(404, 'Organization not found.');
            return null;
        }

        $organization->delete();
        
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
        $organization = Organizations::onlyTrashed()->where('id',$id)->first();
        if (!$organization) {
            abort(404, 'Organization not found.');
            return null;
        }

        $organization->restore();
        
        return null;
    }
    

}