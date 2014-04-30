<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Http;

use Everon\Helper;
use Everon\Interfaces;

class Request extends \Everon\Request 
{
    /**
     * http://stackoverflow.com/questions/6038236/http-accept-language
     * @return string
     */
    protected function getPreferredLanguage()
    {
        $acceptedLanguages = $this->getServerCollection()->get('HTTP_ACCEPT_LANGUAGE');

        // regex inspired from @GabrielAnderson on http://stackoverflow.com/questions/6038236/http-accept-language
        preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})*)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $acceptedLanguages, $lang_parse);
        $langs = $lang_parse[1];
        $ranks = $lang_parse[4];

        // (create an associative array 'language' => 'preference')
        $lang2pref = array();
        for($i=0; $i<count($langs); $i++) {
            $lang2pref[$langs[$i]] = (float) (!empty($ranks[$i]) ? $ranks[$i] : 1);
        }

        // (comparison function for uksort)
        $cmpLangs = function ($a, $b) use ($lang2pref) {
            if ($lang2pref[$a] > $lang2pref[$b])
                return -1;
            elseif ($lang2pref[$a] < $lang2pref[$b])
                return 1;
            elseif (strlen($a) > strlen($b))
                return -1;
            elseif (strlen($a) < strlen($b))
                return 1;
            else
                return 0;
        };

        // sort the languages by prefered language and by the most specific region
        uksort($lang2pref, $cmpLangs);

        $tokens = array_keys($lang2pref);
        $current = array_shift($tokens);
        
        return $current;
    }
}