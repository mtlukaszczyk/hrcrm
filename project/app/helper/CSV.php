<?php

namespace App\Helper;

class CSV {

    /**
     * Returns array as csv
     * @param array $array data
     * @param string $delimiter
     * @param mixed $headers array with headerso or string == 'keys' - then get headers from $array
     * @return redner csv data
     */
    public static function array2csv(array &$array, $delimiter = ',', $headers = 'keys') {
        if (count($array) == 0) {
            return null;
        }

        ob_start();

        $df = fopen("php://output", 'w');

        if ($headers == 'keys') {
            fputcsv($df, array_keys(reset($array)), $delimiter);
        } else if (is_array($headers)) {
            fputcsv($df, $headers, $delimiter);
        }

        foreach ($array as $row) {
            fputcsv($df, $row, $delimiter);
        }

        fclose($df);
        return ob_get_clean();
    }

    /**
     * Creates headers for csv file download
     * @param string $filename
     */
    public static function downloadSendHeaders($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

}
