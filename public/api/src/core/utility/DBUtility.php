<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 13-Nov-16
 * Time: 1:19 PM
 */

namespace core\utility;


class DBUtility
{
    /**
     * @param $query
     * @return string
     */
    public static function parseUpdateQuery($query)
    {
        $ret = array();

        foreach ($query as $key => $value) {
            $ret[] = "{$key}=:{$key}";
        }

        return join(', ', $ret);
    }

    /**
     * @param $filter
     * @param $acceptList
     * @return string
     */
    public static function parseFilter($filter, $acceptList)
    {
        $ret = array();
        foreach ($filter as $value) {
            $temp = preg_split('/(>=|<=|<>|[<>=])/', $value, 3, PREG_SPLIT_DELIM_CAPTURE);
            if (isset($acceptList[$temp[0]])) {
                switch ($acceptList[$temp[0]]) {
                    case 'int':
                        if(strtolower($temp[2]) == 'null'){
                            $ret[] = self::parseNullValue($temp);
                        } else {
                            $ret[] = $temp[0] . $temp[1] . intval($temp[2]);
                        }
                        break;
                    case 'bool':
                        if(strtolower($temp[2]) == 'null'){
                            $ret[] = self::parseNullValue($temp);
                        } else {
                            $ret[] = $temp[0] . $temp[1] . +boolval($temp[2]);
                        }
                        break;
                    case 'double':
                        if(strtolower($temp[2]) == 'null'){
                            $ret[] = self::parseNullValue($temp);
                        } else {
                            $ret[] = $temp[0] . $temp[1] . doubleval($temp[2]);
                        }
                        break;
                    default:
                        if(strtolower($temp[2]) == 'null'){
                            $ret[] = self::parseNullValue($temp);
                        } else {
                            $ret[] = $temp[0] . $temp[1] . '\'' . $temp[2] . '\'';
                        }
                }
            }
        }

        if (count($ret) != 0) {
            return join(' AND ', $ret);
        } else {
            return 'true';
        }
    }

    private function parseNullValue($filter){
        $ret = '';
        if($filter[1] == '<>'){
            $ret = $filter[0] . " IS NOT NULL";
        } elseif ($filter[1] == '=') {
            $ret = $filter[0] . " IS NULL";
        }

        return $ret;
    }
}