<?php
/**
 * Class SolidMean_ForumBackup_Option_BoardInactiveMessage
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2014 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */
class SolidMean_ForumBackup_Option_BoardInactiveMessage
{
    /**
     * This method does some sanitation of the message to show users that backups are running.
     * This should probably be done with a filterInput but I an idiot as to how to implement
     * it here.
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

        // Removed this at the request of users that would like the text box to function
        // just like the boardInactiveMessage
        //$optionValue = strip_tags($optionValue, '<p><a><i><b><u>');

        return true;
    }
}