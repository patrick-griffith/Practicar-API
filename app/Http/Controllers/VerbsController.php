<?php

namespace App\Http\Controllers;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Symfony;
use ApiHandler;
use Auth;

use App\Traits\FileTraits;
use App\Models\Verbs;
use App\Models\Conjugations;
use App\Models\Moods;
use App\Models\Persons;
use App\Models\Answers;

class VerbsController extends Controller
{
    use FileTraits;

    public function find(Request $request)
    {
        //get default results
        $verbs= Verbs::where('id','>',0);
      
        //filter based on QS
        $verbs = ApiHandler::parseMultiple($verbs);
      
        return $verbs->getResult();
    } 

    public function my_answers(Request $request)
    {
        //get default results
        $answers = Answers::where('members_id', Auth::user()->id);
        $answers = ApiHandler::parseMultiple($answers);
        return $answers->getResult();
    } 

    public function get(Request $request, $spanish)
    {
        //get default results
        $verb = verbs::where('spanish', $spanish)->first();

        if(!$verb) {
            abort(404, 'Verb not found.');
            return null;
        }	
      
        //filter based on QS
        $verb = ApiHandler::parseSingle($verb, $verb->id);
      
        return $verb->getResult();
    } 

    public function get_questions(Request $request)
    {
        //get default results
        $conjugations= Conjugations::where('id', '>', 0)->where('verbs_id', '<', 205)->whereNotNull('english')->inRandomOrder()->limit(20);
      
        //filter based on QS
        $conjugations = ApiHandler::parseMultiple($conjugations);
      
        return $conjugations->getResult();
    } 

    public function get_moods(Request $request)
    {
        //get default results
        $moods= Moods::where('id', '>', 0);
      
        //filter based on QS
        $moods = ApiHandler::parseMultiple($moods);
      
        return $moods->getResult();
    } 

    public function get_persons(Request $request)
    {
        //get default results
        $persons= Persons::where('id', '>', 0);
      
        //filter based on QS
        $persons = ApiHandler::parseMultiple($persons);
      
        return $persons->getResult();
    } 

    public function create_answer(Request $request)
    {       
        //validation
        try{
            $this->validate($request, [
                'answer' => 'required|max:255',
                'seconds_elapsed' => 'required',
                'conjugations_id' => 'required|exists:conjugations,id',
            ]);
        }catch( \Illuminate\Validation\ValidationException $e ){
            return $e->getResponse();
        }                          
        
        $request['members_id'] = Auth::user()->id;

        $answer = Answers::create($request->all());
        return $answer;

    } 
    

}