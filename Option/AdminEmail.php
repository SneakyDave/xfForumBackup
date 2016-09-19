<?php
/**
 * Class SolidMean_ForumBackup_Option_AdminEmail
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2014 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */
class SolidMean_ForumBackup_Option_AdminEmail
{
    /**
     * This method checks the validity of the admin email entered in the options.
     *
     * @param $optionValue
     * @param XenForo_DataWriter $dw
     * @param $fieldName
     * @return bool
     * @throws XenForo_Exception - Invalid email (using the XenForo Helper)
     */
    public static function verifyOption(&$optionValue, XenForo_DataWriter $dw, $fieldName)
    {
        // Ignore validation on install.
        if ($dw->isInsert())
        {
            return true;
        }

        if(! empty($optionValue) && ! XenForo_Helper_Email::isEmailValid($optionValue))
        {
            $dw->error(new XenForo_Phrase('solidmean_forumbackup_invalid_email'), $fieldName);
            return false;
        }

         return true;
    }
}
