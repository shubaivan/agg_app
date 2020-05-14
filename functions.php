<?php
function walk($val, $key, &$newArray)
{
    $nums = explode('=>', $val);
    $newArray[$nums[0]] = $nums[1];
}