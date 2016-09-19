<?php

class SolidMean_ForumBackup_Option_SFTPHost
{
    public static function renderOption(XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit)
    {
        $preparedOption['noPHPSSH2'] = !function_exists('ssh2_connect');

        return XenForo_ViewAdmin_Helper_Option::renderOptionTemplateInternal(
            'SolidMeanForumBackup_option_sftp_hostname',
            $view, $fieldPrefix, $preparedOption, $canEdit
        );
    }

    public static function verifyOption(&$optionValue, XenForo_DataWriter $dw, $fieldName)
    {
        if(!function_exists('ssh2_connect'))
        {
            $optionValue = '';
        }

        return true;
    }
}