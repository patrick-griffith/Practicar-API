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
        //$this->call('FetchEnglish');
        $this->call('FetchConjugations');

        Model::reguard();
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

        $verbs = Verbs::where('id', '>=', 100)->where('id','<', 250)->get();
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
            sleep(5);
        }
                
    }
}

class FetchEnglish extends Seeder{
    public function run(){

        $client = new Client();
        $guzzleClient = new GuzzleClient(array(
            'timeout' => 60,
        ));
        $client->setClient($guzzleClient);

        $crawler = $client->request('GET', 'https://translate1.spanishdict.com/api/v1/verb?q=to%20go&source=en');

        print_r($crawler);

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
            sleep(4);
        }
            


        return;
    }


}