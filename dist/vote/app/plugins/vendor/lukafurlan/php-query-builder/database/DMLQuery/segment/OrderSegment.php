<?php
namespace lukafurlan\database\DMLQuery\segment;

/**
 * @author Luka Furlan <Luka.furlan9@gmail.com>
 * @copyright 2018 Luka Furlan
 */

class OrderSegment implements Segment {

    public static function build($columns) {
        if (!is_array($columns)) {
            return "";
        }
    
        return "ORDER BY " . implode(', ', $columns) . " ";
    }

    public static function validate($param) {

        if($param == null) {
            return false;
        }

        return true;

    }
}