<?php namespace App\Traits;

use Dingo\Api\Http\Request;

trait URLTraits
{
    
    public function parseDomain($url) 
	{  
        $components = parse_url($this->prependHTTPPrefix($url));
        if (isset($components['host'])) {
            $host = strtolower($components['host']);
            $pattern = '/([a-z0-9-]+\.[a-z0-9]{2,3}(\.?[a-z0-9]{2,3})?(?=\/|$))|([a-z0-9-]+\.[a-z0-9]+(?=\/|$))/';
            preg_match($pattern, $host, $matches);
            return $matches[0];
        }
        return '';
	}

    public function ensureHTTPPrefix(Request $request, $fields) 
	{
        $mergers = array();
        foreach($fields as $field) {
            if ($request->has($field)) {
                $mergers[$field] = $this->prependHTTPPrefix($request->input($field));
            }
        }
        $request->merge($mergers);
    }
    
    public function prependHTTPPrefix($url) 
	{
        if (strpos($url, '.') && !preg_match("~^(?:f|ht)tps?://~i", $url)) {
            return 'http://' . $url;
	    }
        return $url;
    }
    
}