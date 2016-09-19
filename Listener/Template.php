<?php
/**
 * Class SolidMean_ForumBackup_Listener_Template
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2014 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */
class SolidMean_ForumBackup_Listener_Template
{
    /**
     * Currently, the purpose of this method is to organize admin options into tabs.
     *
     * @param $templateName
     * @param $content
     * @param array $containerData
     * @param XenForo_Template_Abstract $template
     */
    public static function templatePostRender($templateName, &$content, array &$containerData, XenForo_Template_Abstract $template)
    {
        // Check to see if this the option_list template.
        if ($templateName == 'option_list')
        {
           // Check to see if this is the addon options we want to change.
            if ($containerData['title'] == 'Options: ForumBackup')
            {
                /* Change the default options list template to our new one */
                $content = $template->create('SolidMeanForumBackup_option_list', $template->getParams());
            }
        }
    }
}