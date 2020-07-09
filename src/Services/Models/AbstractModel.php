<?php

namespace App\Services\Models;

abstract class AbstractModel
{
    /**
     * @param string $matchData
     * @param int $limitations
     * @return string
     */
    public function prepareDataForGINSearch(string $matchData, int $limitations = 3)
    {
        $resultData = '';
        if (preg_match_all('/[a-zA-Z ¤æøĂéëäöåÉÄÖÅ™]+/',$matchData,$matches)) {
            $matchData = array_shift($matches);
            if (is_array($matchData) && count($matchData)) {

                $arrayFilter = array_filter($matchData, function ($v) use ($limitations) {
                    if (strlen($v) > $limitations && mb_check_encoding($v, "UTF-8")) {
                        return true;
                    }
                });
                $arrayUniqueFilter = array_unique($arrayFilter);
                $resultData = implode(',', $arrayUniqueFilter);
            }
        }

        return $resultData;
    }
}