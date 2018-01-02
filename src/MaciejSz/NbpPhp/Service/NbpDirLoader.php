<?php
namespace MaciejSz\NbpPhp\Service;
 
use MaciejSz\NbpPhp\Exc\ECouldNotLoadDir;

class NbpDirLoader
{
    const DIR_URL = 'http://www.nbp.pl/kursy/xml/dir.txt';

    /**
     * @param int $year
     * @return array
     */
    public function load($year)
    {
        $url = self::DIR_URL;
        $dir = [];
        set_error_handler($this->_getLoadStreamErrorHandler($url));
        $txt = file_get_contents($url);
        restore_error_handler();
        $dates = explode("\n", $txt);
        foreach ( $dates as $date ) {
            $date = ltrim(trim($date), "\xef\xbb\xbf");
            if ( empty($date) ) {
                continue;
            }
            $start = strlen($date) - 6;
            $index = substr($date, $start, 6);
            $ext_type = substr($date, 0, 1);
            if ( ! isset($dir[$index]) ) {
                $dir[$index] = array();
            }
            $dir[$index][$ext_type] = $date;
        }
        ksort($dir);
        return $dir;
    }

    /**
     * @param string $url
     * @return callable
     */
    private function _getLoadStreamErrorHandler($url)
    {
        return function() use ($url) {
            throw new ECouldNotLoadDir($url);
        };
    }

    /**
     * @return string
     */
    private static function _getCurrentYear()
    {
        $Date = new \DateTime();
        $current_year = $Date->format("Y");
        return $current_year;
    }
}
 