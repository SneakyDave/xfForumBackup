<?php
/**
 * Class SolidMean_ForumBackup_Model_Database
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2014 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */
class SolidMean_ForumBackup_Model_Database extends XenForo_Model
{

    /**
     * This method uses a PHP system command to execute the backup
     *
     * @param $command - The command line to execute.
     * @param $log - Our friendly log file.
     * @return bool - always true
     */
    public function backup($command)
    {

        // Run the mysqldump command, try to get the output of it, and the return value.
        exec($command);

        return true;
    }

    /**
     * Gets the database configuration parameters.
     *
     * @return array - Contains user/db/password, etc.
     */
    public function getConfig()
    {
        $db = $this->_getDb();

        // Get the database configuration information
        return $this->_db->getConfig();

    }

}