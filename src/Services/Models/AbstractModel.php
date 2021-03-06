<?php

namespace App\Services\Models;

use App\EventListener\SlugApproach;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractModel extends SlugApproach
{
    /**
     * @param ParamFetcher $paramFetcher
     * @param $key
     * @param $data
     *
     * @return ParamFetcher
     */
    protected function setParamFetcherData(RequestStack $requestStack, ParamFetcher $paramFetcher, $key, $data)
    {
        $request = $requestStack->getCurrentRequest();
        $request->query->set($key, $data);
        $param = new QueryParam();
        $param->name = $key;
        $paramFetcher->addParam($param);

        return $paramFetcher;
    }
    
    /**
     * @param string $matchData
     * @param int $limitations
     * @return string
     */
    public function prepareDataForGINSearch(string $matchData, int $limitations = 4, bool $relation = false)
    {
        if ($relation) {
            $pattern = '[a-zA-Z ¤æøĂéëäöåÉÄÖÅ™®«»©]+';
        } else {
            if (preg_match_all('/\b\w*\&+\w*\b/', $matchData, $m)) {
                $resultMatchAmpersand = array_shift($m);
                foreach ($resultMatchAmpersand as $ampersand) {
                    $matchData = preg_replace("/$ampersand/", str_replace('&', '-', $ampersand), $matchData);
                }
            }
            $pattern = '[a-zA-Z ¤æøĂÃãéëäöåÉÄÖÅ™®«»©\-]+';
        }

        $resultData = '';
        if (preg_match_all("/$pattern/",$matchData,$matches)) {
            $matchData = array_shift($matches);
            if (is_array($matchData) && count($matchData)) {

                $arrayFilter = array_filter($matchData, function ($v) use ($limitations) {
                    if (mb_strlen($v) >= $limitations && mb_check_encoding($v, "UTF-8")) {
                        return true;
                    }
                });
                $arrayUniqueFilter = array_unique($arrayFilter);
                $arrayUniqueMap = array_map(function ($v) {
                    $trim = trim($v);
                    $trim = trim($trim, '-');
                    return $trim;
                }, $arrayUniqueFilter);
                $secondFilter = array_filter($arrayUniqueMap, function ($v) use ($limitations) {
                    if (mb_strlen($v) >= $limitations) {
                        return true;
                    }
                });
                $resultData = implode(',', $secondFilter);
            }
        }

        return $resultData;
    }
}