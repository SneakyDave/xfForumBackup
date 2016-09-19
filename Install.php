<?php
/**
 * Class SolidMean_ForumBackup_Install
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2016 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */
class SolidMean_ForumBackup_Install
{
    /**
     * @var
     */
    private static $_instance;

    protected $_db;

    /**
     * @var array - List of forums that shouldn't be allowed to install this addon.
     */
    private static $forumban = array(
        array(
            'name'    => 'Socially Uncensored',
            'message' => 'Pending Litigation Circumstances prevent this install',
            ),
        array(
            'name'    => 'The Admin Zone',
            'message' => 'Reading private conversations prevents this install from happening',
        ),
        array(
            'name'    => 'CODForums',
            'message' => 'Being a rage machine prevents this install from happening.',
        ),
        array(
            'name'    => 'Unreal Tournament Forums',
            'message' => 'Being rude to people trying to help you prevents this install from happening',
        ),
    );

    /**
     * @return SolidMean_ForumBackup_Install
     */
    public static final function getInstance()
    {
        if (!self::$_instance)
        {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     *  Get the database handler.
     * @return mixed
     * @throws Zend_Exception
     */
    protected function _getDb()
    {
        if ($this->_db === null)
        {
            $this->_db = XenForo_Application::get('db');
        }

        return $this->_db;
    }

    /**
     * Do some initial versioning setup
     *
     * @param $existingAddOn
     * @param $addOnData
     * @throws XenForo_Exception
     */
    public static function build($existingAddOn, $addOnData)
    {
        if (XenForo_Application::$versionId < 1020070) {
            throw new XenForo_Exception('ForumBackup requires XenForo 1.2.0 or newer to proceed.', true);
        }

        // Ignore installations to forums that are more trouble than they are worth.
        foreach (self::$forumban as $site){
            if (XenForo_Application::get('options')->boardTitle == $site['name'])
            {
                throw new XenForo_Exception($site['message'], true);
            }
        }

        $startVersion = 1;
        $endVersion = $addOnData['version_id'];

        if ($existingAddOn)
        {
            $startVersion = $existingAddOn['version_id'] + 1;
        }

        $install = self::getInstance();

        for ($i = $startVersion; $i <= $endVersion; $i++)
        {
            $method = '_installVersion' . $i;

            if (method_exists($install, $method) === false)
            {
                continue;
            }

            $install->$method();
        }
    }

    /**
     * Establish some requirements before installing this add-on
     *
     * @throws XenForo_Exception
     */
    protected function _installVersion1()
    {
        // Try to see if the exec function exists, and actually works.
        if(! exec('echo SolidMeanForumBackup') == 'SolidMeanForumBackup')
        {
            throw new XenForo_Exception('The PHP exec() function is required for this add-on.', true);
        }

     }

    /**
     * Remove the original addons cron tasks, replace them with new cron tasks not
     * assigned to an addon. This way, future updates won't overwrite cron task time changes.
     * Version 1.2.01
     */
    protected function _installVersion17()
    {
        $db = $this->_getDb();

        // Establish default values for the cron tasks to run every day.
        $dbnextrun = strtotime("0:00", time() + 86400); // 00:00 of the next day
        $dbrunrules = 'a:4:{s:8:"day_type";s:3:"dom";s:3:"dom";a:1:{i:0;s:2:"-1";}s:5:"hours";a:1:{i:0;s:1:"0";}s:7:"minutes";a:1:{i:0;s:1:"0";}}';

        $codenextrun = strtotime("0:30", time() + 86400); // 00:30 of the next day
        $coderunrules = 'a:4:{s:8:"day_type";s:3:"dom";s:3:"dom";a:1:{i:0;s:2:"-1";}s:5:"hours";a:1:{i:0;s:1:"0";}s:7:"minutes";a:1:{i:0;s:2:"30";}}';

        // See if there are already cron task times established for this addon.
        if ($dbcrondata = $db->fetchRow("
              SELECT `run_rules`, `next_run` FROM xf_cron_entry
                WHERE entry_id = 'forumbackupExec';")) {
            $dbnextrun = $dbcrondata['next_run'];
            $dbrunrules = $dbcrondata['run_rules'];
        }

        if($codecrondata = $db->fetchRow("
              SELECT `run_rules`, `next_run` FROM xf_cron_entry
                WHERE entry_id = 'forumbackupCodeExec';")) {
            $codenextrun = $codecrondata['next_run'];
            $coderunrules = $codecrondata['run_rules'];
        }

        // Unnecessary on first installs, but I'm lazy.
        $db->delete('xf_cron_entry', "addon_id = 'SolidMean_ForumBackup'");

        // Manually create cron entries without an addon id so that subsequent updates won't negate any user change.
        $db->query("
            INSERT INTO `xf_cron_entry`
                (`entry_id`, `cron_class`, `cron_method`, `run_rules`, `active`, `next_run`, `addon_id`)
            VALUES
                ('forumbackupCodeExec', 'SolidMean_ForumBackup_CronEntry_Backup', 'saveCode', '$coderunrules', 1, '$codenextrun', ''),
                ('forumbackupExec', 'SolidMean_ForumBackup_CronEntry_Backup', 'saveDatabase', '$dbrunrules', 1, '$dbnextrun', '')
    	");

    }

}