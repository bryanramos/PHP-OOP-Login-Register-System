<?php 
class Config {
    public static function get($path = null) {
        if ($path) { // make sure path has been passed to this method
            $config = $GLOBALS['config']; // define where config is coming from
            $path = explode('/', $path);

            // loop through pieces broken up
            foreach($path as $bit) {
                if (isset($config[$bit])) {
                    $config = $config[$bit];
                }
            }

            return $config;
        }

        return false; // does not exist
    }
}