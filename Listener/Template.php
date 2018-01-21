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
            $pos = strpos($containerData['title'], ': ForumBackup');
            if ($pos !== false)
            {
                $params = $template->getParams();
                $params['orphanedConfigFilesMessage'] = '';
                if(self::_orphanConfigsExist() > 0)
                {
                    $params['orphanedConfigFilesMessage'] = new XenForo_Phrase('solidmean_forumbackup_orphan_configs');
                }
                /* Change the default options list template to our new one */
                $content = $template->create('SolidMeanForumBackup_option_list', $params);
            }
        }
    }
    private static function _orphanConfigsExist()
    {
        $opt = new SolidMean_ForumBackup_Options('Database');
        $count = 0;
        if(!empty($opt->getDirectory()))
        {
            $pattern = $opt->getDirectory() . ".ForumBackup_*.cnf";
            foreach (glob($pattern) as $item) {
                $count++;
            }
        }

        return $count;

    }

}
