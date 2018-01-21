<?php
/**
 * Class SolidMean_ForumBackup_Option_CompressionType
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2014 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */
class SolidMean_ForumBackup_Option_CompressionType
{
    /**
     *  This method checks  the validity of the gzip, pigz, and bzip2 binary.
     *
     * @param $optionValue
     * @param XenForo_DataWriter $dw
     * @param $fieldName
     * @return bool
     */
    public static function verifyCompressionType(&$optionValue, XenForo_DataWriter $dw, $fieldName)
    {

        if ($dw->isInsert() || empty($optionValue)) {
            return true;
        }

        // TODO don't know how to check the presence of pigz/bzip2 yet in php, so just returning true for now.
        return true;
    }

}
