<?php
/**
 * Class SolidMean_ForumBackup_Option_FileName
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2014 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */
class SolidMean_ForumBackup_Option_FileName
{
    /**
     * This method checks the validity of the filename.
     *
     * @param $optionValue
     * @param XenForo_DataWriter $dw
     * @param $fieldName
     * @return bool
     */
    public static function verifyOption(&$optionValue, XenForo_DataWriter $dw, $fieldName)
    {
        if ($dw->isInsert())
        {
            return true;
        }

        if(empty($optionValue))
        {
            $optionValue = 'forumbackup';
            return true;
        }

        if (! strpbrk($optionValue, "\\/?%*:|\"<>") === FALSE)
        {
            $dw->error(new XenForo_Phrase('solidmean_forumbackup_filename_must_be_alphanumeric'), $fieldName);
            return false;
        }

        return true;
    }
}
