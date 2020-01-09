<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use App\Models\Verbs;
use App\Models\Tenses;
use App\Models\Conjugations;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        //$this->call('PopulateVerbs');        
        //$this->call('FetchConjugations');
        //$this->call('FetchEnglish');
        //$this->call('FillMissingTenses');
        //$this->call('FillImperative');

        Model::reguard();
    }
}

class FillImperative extends Seeder{
    public function run(){
        $conjugations = Conjugations::whereIn('tenses_id',[10,11])->where('persons_id',2)->whereNotNull('english')->get();
        foreach($conjugations as $c){
            Conjugations::where('verbs_id',$c->verbs_id)->where('tenses_id',$c->tenses_id)->whereNull('english')->update(['english' => $c->english]);
        }

    }
}

class FillMissingTenses extends Seeder{

    public function run(){

        $replace = [
            '3' => [
                'tenses_id' => 5,
                'find' => ' will ',
                'replace' => ' used to '
            ],
            '4' => [
                'tenses_id' => 5,
                'find' => ' will ',
                'replace' => ' would '
            ],
            '14' => [
                'tenses_id' => 16,
                'find' => ' will ',
                'replace' => ' used to '
            ],
            '15' => [
                'tenses_id' => 16,
                'find' => ' will ',
                'replace' => ' would '
            ],
            '18' => [
                'tenses_id' => 19,
                'find' => '',
                'replace' => ''
            ],
            '20' => [
                'tenses_id' => 21,
                'find' => ' will ',
                'replace' => ' would '
            ]
        ];

        $verbs = Verbs::where('id','>=',1100)->where('id','<',1400)->get();
        foreach($verbs as $verb){
            

            if($empties = $verb->conjugations->where('english',NULL)){

                foreach($empties as $empty){
                    if(array_key_exists($empty->tenses_id, $replace)){

                        //get the conjugation that should replace this one
                        if($replacement = $verb->conjugations->where('persons_id', $empty->persons_id)->where('tenses_id', $replace[$empty->tenses_id]['tenses_id'])->first()){                            
                            $empty->english = str_replace($replace[$empty->tenses_id]['find'], $replace[$empty->tenses_id]['replace'], $replacement->english);

                            $empty->save();
                        }
                        
                    }
                }

            }
        }

    }
}

class FetchConjugations extends Seeder{
    public function run(){

        $tenses = [
            'presentIndicative' => 0,
            'preteritIndicative' => 1,
            'imperfectIndicative' => 2,
            'conditionalIndicative' => 3,
            'futureIndicative' => 4,
            'presentSubjunctive' => 5,
            'imperfectSubjunctive' => 6,
            'imperfectSubjunctive2' => 7,
            'futureSubjunctive' => 8,
            'imperative' => 9,
            'negativeImperative' => 10,
            'presentContinuous' => 11,
            'preteritContinuous' => 12,
            'imperfectContinuous' => 13,
            'conditionalContinuous' => 14,
            'futureContinuous' => 15,
            'presentPerfect' => 16,
            'preteritPerfect' => 17,
            'pastPerfect' => 18,
            'conditionalPerfect' => 19,
            'futurePerfect' => 20,
            'presentPerfectSubjunctive' => 21,
            'pastPerfectSubjunctive' => 22,
            'futurePerfectSubjunctive' => 23,
        ];

        $client = new Client();
        $guzzleClient = new GuzzleClient(array(
            'timeout' => 60,
        ));
        $client->setClient($guzzleClient);
        
        $verbs = Verbs::where('id', '>', '140')        
            ->where(function ($query) {
                $query->where('english', 'like', '%,%')
                    ->orWhere('english', 'like', '%/%');
            })      
            ->limit(30)
            ->get();
        $verbs = Verbs::where('id', '>=', 500)->where('id','<', 1500)->get();
        foreach($verbs as $verb){            

            $crawler = $client->request('GET', 'https://www.spanishdict.com/conjugate/' . $verb->spanish);

            $crawler->filter('.vtable-word-text')->each(function ($word, $i) use (&$tenses, &$verb){ 
                
                $spanish = $word->text();
                $persons_id = intval($word->attr('data-person')) + 1;
                $tenses_id = $tenses[$word->attr('data-tense')] + 1;

                $is_irregular = 0;
                $word->filter('.conj-irregular')->each(function ($irreg, $i) use (&$is_irregular) {
                    $is_irregular = 1;
                });

                Conjugations::create([
                    'verbs_id' => $verb->id,
                    'tenses_id' => $tenses_id,
                    'persons_id' => $persons_id,
                    'spanish' => $spanish,
                    'is_irregular' => $is_irregular
                ]);
                
            });
            sleep(1);
        }
                
    }
}

class FetchEnglish extends Seeder{
    public function run(){

        $tenses = [
            'presentIndicative' => 0,
            'preteritIndicative' => 1,
            'imperfectIndicative' => 2,
            'conditionalIndicative' => 3,
            'futureIndicative' => 4,
            'presentSubjunctive' => 5,
            'imperfectSubjunctive' => 6,
            'imperfectSubjunctive2' => 7,
            'futureSubjunctive' => 8,
            'imperative' => 9,
            'negativeImperative' => 10,
            'presentContinuous' => 11,
            'pastContinuous' => 12, //12 && 13
            //'preteritContinuous' => 12,
            //'imperfectContinuous' => 13,
            'conditionalContinuous' => 14,
            'futureContinuous' => 15,
            'presentPerfect' => 16,
            'preteritPerfect' => 17,
            'pastPerfect' => 18,
            'conditionalPerfect' => 19,
            'futurePerfect' => 20,
            'presentPerfectSubjunctive' => 21,
            'pastPerfectSubjunctive' => 22,
            'futurePerfectSubjunctive' => 23,
        ];

        $verbs = Verbs::where('id', '>=', 500)->where('id','<', 1500)->get();
        foreach($verbs as $verb){

            $json_string = file_get_contents('https://translate1.spanishdict.com/api/v1/verb?q=' . urlencode($verb->translate ? $verb->translate : $verb->english) . '&source=en');
            //$json_string = '{"params":{"q":"to go","source":"en"},"data":{"id":22713,"infinitive":"go","gerund":"going","pastParticiple":"gone","matchedConjugation":"go","reason":"exact match after removing \"to\" or subject pronoun","isReflexiveVariation":false,"presentParticiple":"going","paradigms":{"presentIndicative":[{"word":"I go"},{"word":"you go"},{"word":"he/she goes"},{"word":"we go"},{"word":"you go"},{"word":"they go"}],"preteritIndicative":[{"word":"I went"},{"word":"you went"},{"word":"he/she went"},{"word":"we went"},{"word":"you went"},{"word":"they went"}],"imperfectIndicative":null,"conditionalIndicative":null,"futureIndicative":[{"word":"I will go"},{"word":"you will go"},{"word":"he/she will go"},{"word":"we will go"},{"word":"you will go"},{"word":"they will go"}],"presentContinuous":[{"word":"I am going"},{"word":"you are going"},{"word":"he/she is going"},{"word":"we are going"},{"word":"you are going"},{"word":"they are going"}],"pastContinuous":[{"word":"I was going"},{"word":"you were going"},{"word":"he/she was going"},{"word":"we were going"},{"word":"you were going"},{"word":"they were going"}],"futureContinuous":[{"word":"I will be going"},{"word":"you will be going"},{"word":"he/she will be going"},{"word":"we will be going"},{"word":"you will be going"},{"word":"they will be going"}],"presentSubjunctive":null,"futureSubjunctive":null,"imperfectSubjunctive":null,"imperfectSubjunctive2":null,"presentPerfect":[{"word":"I have gone"},{"word":"you have gone"},{"word":"he/she has gone"},{"word":"we have gone"},{"word":"you have gone"},{"word":"they have gone"}],"pastPerfect":[{"word":"I had gone"},{"word":"you had gone"},{"word":"he/she had gone"},{"word":"we had gone"},{"word":"you had gone"},{"word":"they had gone"}],"preteritPerfect":null,"futurePerfect":[{"word":"I will have gone"},{"word":"you will have gone"},{"word":"he/she will have gone"},{"word":"we will have gone"},{"word":"you will have gone"},{"word":"they will have gone"}],"conditionalPerfect":null,"presentPerfectContinuous":[{"word":"I have been going"},{"word":"you have been going"},{"word":"he/she has been going"},{"word":"we have been going"},{"word":"you have been going"},{"word":"they have been going"}],"futurePerfectContinuous":[{"word":"I will have been going"},{"word":"you will have been going"},{"word":"he/she will have been going"},{"word":"we will have been going"},{"word":"you will have been going"},{"word":"they will have been going"}],"pastPerfectContinuous":[{"word":"I had been going"},{"word":"you had been going"},{"word":"he/she had been going"},{"word":"we had been going"},{"word":"you had been going"},{"word":"they had been going"}],"presentPerfectSubjunctive":null,"pastPerfectSubjunctive":null,"futurePerfectSubjunctive":null,"imperative":[null,{"word":"go"},null,{"word":"let's go"},{"word":"go"},null],"negativeImperative":[null,{"word":"don't go"},null,{"word":"let's not go"},{"word":"don't go"},null]}}}';

            $json = json_decode($json_string);            

            if($json && $json->data){
                foreach($json->data->paradigms as $key => $words){
                    //echo $key."\n";
                    if(array_key_exists($key, $tenses)){
                        $tenses_id = $tenses[$key] + 1;
                        for($i = 0; $i <= 5; $i++){
                            if(isset($words) && isset($words[$i]) && isset($words[$i]->word)){
                                //echo $words[$i]->word."\n";
                                $persons_id = $i + 1;
                                
                                DB::table('conjugations')
                                    ->whereNull('english')
                                    ->where('verbs_id', $verb->id)
                                    ->where('persons_id', $persons_id)
                                    ->where('tenses_id', $tenses_id)
                                    ->update(['english' => $words[$i]->word]);
                            }
                        }
                    }
                    //echo "\n";
                }
            }
            sleep(1);
        }

    }
}

/**
 * Class PopulateVerbs
 */
class PopulateVerbs extends Seeder
{
    public function run()
    {

        $client = new Client();
        $guzzleClient = new GuzzleClient(array(
            'timeout' => 60,
        ));
        $client->setClient($guzzleClient);

        for($page = 19; $page <= 61; $page ++){
            $crawler = $client->request('GET', 'https://lingolex.com/verbs/popular_verbs.php?page=' . $page);
            
            // Get the latest post in this category and display the titles
            $crawler->filter('tr')->each(function ($tr, $i) {            
                if($i > 2 && $i <= 22){
                    $is_irregular = 0;
                    $tr->filter('.irreg')->each(function ($irreg, $i) use (&$is_irregular) {
                        $is_irregular = 1;
                    });

                    $td = $tr->filter('td');

                    Verbs::create([
                        'spanish' => $td->eq(1)->text(),
                        'english' => $td->eq(2)->text(),
                        'usage' => intval(str_replace(',', '', $td->eq(0)->text())),
                        'is_irregular' => $is_irregular
                    ]);

                }            

            });
            sleep(1);
        }
            


        return;
    }


}