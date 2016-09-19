<?php

/**
 * Class SolidMean_ForumBackup_Uninstall
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2016 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */
class SolidMean_ForumBackup_Uninstall
{
    private static $_instance;

    protected $_db;

    public static final function getInstance()
    {
        if (!self::$_instance)
        {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    protected function _getDb()
    {
        if ($this->_db === null)
        {
            $this->_db = XenForo_Application::get('db');
        }

        return $this->_db;
    }

    /**
     * Run through all the uninstall steps to remove the addon.
     */
    public static function destroy()
    {
        $lastUninstallStep = 17;

        $uninstall = self::getInstance();

        for ($i = 1; $i <= $lastUninstallStep; $i++)
        {
            $method = '_uninstallStep' . $i;

            if (method_exists($uninstall, $method) === false)
            {
                continue;
            }

            $uninstall->$method();
        }
    }

    /**
     * Remove any cron entries that are tagged with the ForumBackup addon.
     * Remove any manually created entries installed with version 1.2.01
     * Version: 1.2.01
     */
    protected function _uninstallStep17()
    {
        $db = $this->_getDb();

        // Make sure the original cron tasks are removed.
        $db->delete('xf_cron_entry', "addon_id = 'ForumBackup'");

        // Remove the manually created cron jobs
        $db->delete('xf_cron_entry', "cron_class = 'SolidMean_ForumBackup_CronEntry_Backup'");


    }

}