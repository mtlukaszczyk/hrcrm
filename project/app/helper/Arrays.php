<?php
namespace App\Helper;

class Arrays {

    /**
     * Extracts DB one row object to simple assoc array
     * @param object/array $data
     * @return array output array
     */
    public static function getArray($data) {
        return collect($data)->map(function($x) {
                    return (array) $x;
                })->toArray();
    }

    /**
     * Checks if given array is assoc
     * @param array $array
     * @return bool
     */
    public static function isAssoc($array) {
        return is_array($array) && array_diff_key($array, array_keys(array_keys($array)));
    }

    /**
     * Extract Eloquent object to simple assoc array
     * @param object/array $array input array/object
     * @param array $attributes fields to get
     * @return output array
     */
    public static function extract($array, $attributes) {
        $data = [];

        if (self::isAssoc($array)) {
            foreach ($attributes as $attribute) {
                $data[$attribute] = $array[$attribute];
            }
        } else {
            foreach ($array as $key => $values) {
                $data[$key] = [];

                foreach ($attributes as $attribute) {
                    $data[$key][$attribute] = $values[$attribute];
                }
            }
        }

        return $data;
    }

    /**
     * Transpone array to new in order: $array[$literal] = $value
     * @param array $a array to transpone
     * @param string $literal field name for key
     * @param string $value field value
     * @return array $b transponed array
     */
    public static function transpone($a, $literal, $value) {
        $b = [];

        if (is_array($a)) {
            for ($i = 0, $cA = count($a); $i < $cA; $i++) {
                $b[$a[$i][$literal]] = $a[$i][$value];
            }
        }

        if (!empty($b)) {
            return $b;
        } else {
            return null;
        }
    }
}
