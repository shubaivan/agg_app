<?php

namespace App\Services\Models;

abstract class AbstractModel
{
    /**
     * @param string $matchData
     * @return string
     */
    public function prepareDataForGINSearch(string $matchData)
    {
        $resultData = '';
        if (preg_match_all('/[^\d]+/',$matchData,$matches)) {
            $matchData = array_shift($matches);
            if (is_array($matchData) && count($matchData)) {

                $arrayFilter = array_filter($matchData, function ($v) {
                    if (strlen($v) > 3 && mb_check_encoding($v, "UTF-8")) {
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