<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 13-Nov-16
 * Time: 10:03 AM
 */

namespace core\utility;


class Paging
{
    /**
     * @param $option
     * @param int $step
     * @return string
     */
    public static function buildQuery($option, $step = 0)
    {
        $opt = array();
        if (isset($option['page'])) $opt['page'] = $option['page'] + $step;
        if (isset($option['limit'])) $opt['limit'] = $option['limit'];
        if (isset($option['order'])) $opt['order'] = $option['order'];
        if (isset($option['direction'])) $opt['direction'] = $option['direction'];
        if (isset($option['filter'])) $opt['filter'] = implode(',', $option['filter']);

        if (count($opt) == 0) {
            return null;
        } else {
            return http_build_query($opt);
        }
    }

    /**
     * @param $input
     * @return array
     */
    public static function normalizeOption($input)
    {
        $option = array();
        $option['page'] = isset($input['page']) ? intval($input['page']) : 1;

        if(isset($input['limit'])){
            $option['limit'] = intval($input['limit']);
            $option['offset'] = ($option['page'] - 1) * $option['limit'];
        } else {
            $option['limit'] = 0;
            $option['offset'] = -1;
        }

        $option['order'] = isset($input['order']) ? $input['order'] : 'id';
        $option['order'] = preg_replace('/[^a-z0-9]/i', '', $option['order']);
        $option['direction'] = isset($input['direction']) && $input['direction'] == 'DESC' ? 'DESC' : 'ASC';

        $rawFilter = isset($input['filter']) ? $input['filter'] : null;
        if ($rawFilter != null) {
            $rawFilter = preg_replace('/[^a-z0-9><=, ]/i', '', $rawFilter);
            $option['filter'] = explode(',', $rawFilter);
        } else {
            $option['filter'] = array();
        }

        return $option;
    }

    /**
     * @param $option
     * @param $result
     * @return mixed
     */
    public static function genNextPrev($option, $result)
    {
        $httpPath = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . strtolower(strtok($_SERVER['REQUEST_URI'], '?'));

        $result['prev'] = $option['page'] > 1 ?
            $httpPath . (($query = self::buildQuery($option, -1)) != null ? '?' . $query : '')
            : null;

        $result['next'] = ($option['limit'] != 0 && $result['count'] > $option['page'] * $option['limit']) ?
            $httpPath . (($query = self::buildQuery($option, 1)) != null ? '?' . $query : '')
            : null;
        return $result;
    }
}
