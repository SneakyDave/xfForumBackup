<?php
/**
 * Class SolidMean_ForumBackup_Helper_Log
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2014 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */
class SolidMean_ForumBackup_Helper_Log
{
    private $path;

    private $debug;

    /**
     * Instantiate a log file with a path and the debug flag from the options.
     * @param $debug
     * @param $path
     */
    public function __construct($debug, $path)
    {
        $this->path = $path;
        $this->debug = $debug;
    }

    /**
     * Method to write a log file if debug is on.
     *
     * @param $logEntry - What to save in the log file
     * @param string $status - Status of the log entry (INFO, WARN, etc.)
     * @return bool - true
     */
    public function write($logEntry, $status = "INFO")
    {
        if( !$this->debug )
        {
            return true;
        }

        if ($fp = @fopen($this->path, 'a'))
        {
            fwrite($fp, date('Y-m-d H:i:s') . ' | ' . $status . ' | ' . $logEntry . "\n");
            fclose($fp);

            return true;
        }

        return false;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @return mixed
     */
    public function getDebug()
    {
        return $this->debug;
    }



}
