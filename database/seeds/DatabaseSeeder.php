<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use App\Models\Verbs;

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

        $this->call('PopulateVerbs');

        Model::reguard();
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