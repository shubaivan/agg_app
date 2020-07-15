<?php

namespace App\Services\Models;

abstract class AbstractModel
{
    /**
     * @param string $matchData
     * @param int $limitations
     * @return string
     */
    public function prepareDataForGINSearch(string $matchData, int $limitations = 4, bool $relation = false)
    {
        if ($relation) {
            $pattern = '[a-zA-Z ¤æøĂéëäöåÉÄÖÅ™®]+';
        } else {
            $pattern = '[a-zA-Z ¤æøĂéëäöåÉÄÖÅ™®\-]+';
        }

        $resultData = '';
        if (preg_match_all("/$pattern/",$matchData,$matches)) {
            $matchData = array_shift($matches);
            if (is_array($matchData) && count($matchData)) {

                $arrayFilter = array_filter($matchData, function ($v) use ($limitations) {
                    if (strlen($v) > $limitations && mb_check_encoding($v, "UTF-8")) {
                        return true;
                    }
                });
                $arrayUniqueFilter = array_unique($arrayFilter);
                $arrayUniqueMap = array_map(function ($v) {
                    return trim($v, '-');
                }, $arrayUniqueFilter);
                $resultData = implode(',', $arrayUniqueMap);
            }
        }

        return $resultData;
    }
}