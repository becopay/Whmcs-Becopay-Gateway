<?php
/**
 * User: Becopay Team
 * Version 1.0.0
 * Date: 10/13/18
 * Time: 11:13 AM
 */

namespace Tests;


/**
 * Class LoadConfig env configuration data
 *
 * @package Tests
 */
class LoadConfig
{

    /**
     * @var array
     */
    private $config = array();

    /**
     * LoadConfig constructor.
     */
    public function __construct()
    {
        // Check .env file is exist
        if (file_exists(__DIR__ . '/../.env')) {
            // open .env file
            $file = fopen(__DIR__ . '/../.env', "r");

            //check can open the file
            if ($file) {
                //read the file line by line
                while (!feof($file)) {
                    // read line data
                    $config = fgets($file);

                    if (!empty($config) && substr($config,0,1) !== '#') {
                        // explode file and set on config variable
                        list($key, $value) = explode('=', $config);
                        $this->config[trim($key)] = trim($value);
                    }
                }
                fclose($file);
            }
        }


    }

    /**
     * Get the Config value
     * @param $name
     * @return bool|String
     */
    public function __get($name)
    {
        // TODO: Implement __get() method.
        if (isset($this->config[$name]))
            return $this->config[$name];

        return false;
    }
}