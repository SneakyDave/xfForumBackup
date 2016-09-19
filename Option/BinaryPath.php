<?php
/**
 * Class SolidMean_ForumBackup_Option_BinaryPath
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2014 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */
class SolidMean_ForumBackup_Option_BinaryPath
{
    /**
     *  This method checks  the validity of the mysqldump utility.
     *
     * @param $optionValue
     * @param XenForo_DataWriter $dw
     * @param $fieldName
     * @return bool
     */
    public static function verifyMySQLDumpOption(&$optionValue, XenForo_DataWriter $dw, $fieldName)
    {

        if ($dw->isInsert() || empty($optionValue))
        {
            return true;
        }

        if (!file_exists($optionValue))
        {
            $dw->error(new XenForo_Phrase('solidmean_forumbackup_mysqldump_binary_doesnt_exist'), $fieldName);
            return false;
        }

        return true;
    }

    /**
     *  This method checks  the validity of the tar utility.
     *
     * @param $optionValue
     * @param XenForo_DataWriter $dw
     * @param $fieldName
     * @return bool
     * @throws XenForo_Exception
     */
    public static function verifyTarOption(&$optionValue, XenForo_DataWriter $dw, $fieldName)
    {

        if ($dw->isInsert() || empty($optionValue))
        {
            return true;
        }

        if (!file_exists($optionValue))
        {
            $dw->error(new XenForo_Phrase('solidmean_forumbackup_tar_binary_doesnt_exist'), $fieldName);
            return false;
        }

        return true;
    }

    /**
     *  This method checks  the validity of the gzip utility.
     *
     * @param $optionValue
     * @param XenForo_DataWriter $dw
     * @param $fieldName
     * @return bool
     * @throws XenForo_Exception
     */
    public static function verifyGzipOption(&$optionValue, XenForo_DataWriter $dw, $fieldName)
    {

        if ($dw->isInsert() || empty($optionValue))
        {
            return true;
        }

        if (!file_exists($optionValue))
        {
            $dw->error(new XenForo_Phrase('solidmean_forumbackup_gzip_binary_doesnt_exist'), $fieldName);
            return false;
        }

        return true;
    }
}
