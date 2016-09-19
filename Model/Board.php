<?php
/**
 * Class SolidMean_ForumBackup_Model_Board
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2014 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */
class SolidMean_ForumBackup_Model_Board extends XenForo_Model
{

    /**
     * This method is used to turn the forum on and off via the activeBoard option
     *
     * @param $status - 1 to turn the board on, 0 to turn the board off.
     * @param $message - What message to display to users while board is unavailable.
     */
    public function setStatus ($status, $message)
	{
        $db = XenForo_Application::getDb();

        // Update the boardInactiveMessage to what we want to say
        $db->update('xf_option', array('option_value' => $message), 'option_id = "boardInactiveMessage"');

        //Set the status of the board to active or inactive.
        $db->update('xf_option', array('option_value' => $status), 'option_id = "boardActive"');

        // Is this necessary?
        XenForo_Model::create('XenForo_Model_Option')->rebuildOptionCache();
	}

}