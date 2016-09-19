<?php
/**
 * Class SolidMean_ForumBackup_Option_BackupDirectory
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2014 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */
class SolidMean_ForumBackup_Option_BackupDirectory
{
    /**
     * This method checks the validity of the backup directory.
     *
     * @param $optionValue
     * @param XenForo_DataWriter $dw
     * @param $fieldName
     * @return bool
     */
    public static function verifyOption(&$optionValue, XenForo_DataWriter $dw, $fieldName)
    {
        // Ignore validation on install.
        if ($dw->isInsert())
        {
            return true;
        }

        if (!file_exists($optionValue) || !is_dir($optionValue) || empty($optionValue))
        {
            $dw->error(new XenForo_Phrase('solidmean_forumbackup_forumbackup_path_doesnt_exist'), $fieldName);
            return false;
        }

        if (!is_writable($optionValue))
        {
            $dw->error(new XenForo_Phrase('solidmean_forumbackup_forumbackup_path_not_writable'), $fieldName);
            return false;
        }

        return true;
    }
}
