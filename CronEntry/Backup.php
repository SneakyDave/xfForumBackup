<?php
/**
 * Class SolidMean_ForumBackup_CronEntry_Backup
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2014 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */

class SolidMean_ForumBackup_CronEntry_Backup
{
    /**
     * Save the database as a backup. Then delete old copies.
     *
     ** @throws XenForo_Exception Error in file generation or size.
     */
    public static function saveDatabase()
    {

        self::_assertExecfunction();

        // Get the options for the addon.
        $opt = new SolidMean_ForumBackup_Options('Database');

        // Create a new log object for debugging.
        $log = new SolidMean_ForumBackup_Helper_Log($opt->getDebug(),
            $opt->getDirectory() . $opt->getFilename() . '.log');

        // Don't do any backup if there is no directory specified,
        if ($opt->getDirectory() && $opt->getIncludedatabase())
        {
            $log->write("START " . $opt->getType() . " Backup");

            $originalInactiveMessage = '';
            // boardWasActive is used to tell if the forum was in enabled when the backup started. If it
            // wasn't, then we dont' want to have the database process turn the forum off and back on.
            $boardWasActive = XenForo_Application::getOptions()->boardActive;

            // Disable the board
            if ($opt->getMaintenancedatabase())
            {
                $originalInactiveMessage = self::_disableBoard($boardWasActive, $opt, $log);
            }

            // Create a temporary config file to use with mysqldump
            $dbconfig = self::_writeTempConfig($opt->getDirectory(), $log);
            $opt->setDbconfigfile($dbconfig['file']);
            $opt->setDbname($dbconfig['dbname']);

            // Do the database backup
            self::_backupDatabase($opt, $log);

            // Enable the board
            if ($opt->getMaintenancedatabase())
            {
                self::_enableBoard($originalInactiveMessage, $boardWasActive, $log);
            }

            // Delete the temporary config file
            @unlink($opt->getDbconfigfile());

            // Remove old versions of the database backups.
            self::_deleteOldSQLVersions($opt->getDirectory(), $opt->getDatabasecopies(), $log, $opt->getFileExtension());

            // Make sure the new database backup exists and is not 0 length.
            if (is_readable($opt->getBackuppath())) {
                $log->write("Verified that database backup exists... ");
                if (filesize($opt->getBackuppath()) == 0) {
                    $log->write("Database backup exists but has 0 length. ", "ERROR");
                    throw new XenForo_Exception("Database backup exists but has 0 length. Run again with debug enabled");
                } else {
                    $log->write("Verified that database backup has data... ");
                }
            } else {
                $log->write("Database backup wasn't created.", "ERROR");
                throw new XenForo_Exception("Database backup wasn't created. Run again with debug enabled");
            }

            if (class_exists('SolidMean_ForumBackup_PHPSSH2_CronEntry_Backup')) {

                $sftp = new SolidMean_ForumBackup_PHPSSH2_CronEntry_Backup('database');
                $sftp->upload($opt, $log);
            }

            if (class_exists('SolidMean_ForumBackup_DropBox_CronEntry_Backup')) {
                $dropbox = new SolidMean_ForumBackup_DropBox_CronEntry_Backup('database');
                $dropbox->upload($opt, $log);
            }

            if (class_exists('SolidMean_ForumBackup_GoogleDrive_CronEntry_Backup')) {
                $gdrive = new SolidMean_ForumBackup_GoogleDrive_CronEntry_Backup('database');
                $gdrive->upload($opt, $log);
            }


            $opt->setStoptime(time());

            // Generate an email with information if the admin has chosen to receive one.
            if ($opt->getEmail()) {
                self::_sendEmail($opt);
            }

            $log->write("END " . $opt->getType() . " backup");
        }

    }

    /**
     * Save the forum root as a tar/gzipped file. Then delete old copies.
     *
     * @throws XenForo_Exception Error in file generation or size.
     */
    public static function saveCode()
    {

        self::_assertExecfunction();

        // Get the options for the addon.
        $opt = new SolidMean_ForumBackup_Options('Code');

        // Create a new log object for debugging.
        $log = new SolidMean_ForumBackup_Helper_Log($opt->getDebug(),
            $opt->getDirectory() . $opt->getFilename() . '.log');

        // Don't do any backup if there is no directory specified, or
        // the admin hasn't chosen to backup the code.
        if ($opt->getDirectory() && $opt->getIncludecode())
        {
            $log->write("START " . $opt->getType() . " Backup");

            $originalInactiveMessage = '';

            // boardWasActive is used to tell if the forum was in enabled when the backup started. If it
            // wasn't, then we dont' want to have the database process turn the forum off and back on.
            $boardWasActive = XenForo_Application::getOptions()->boardActive;

            // Disable the board.
            if ($opt->getMaintenancecode())
            {
                $originalInactiveMessage = self::_disableBoard($boardWasActive, $opt, $log);
            }

            // Backup the xenforo root.
            self::_backupCode($opt, $log);

            // Re-enable the forum.
            if ($opt->getMaintenancecode())
            {
                self::_enableBoard($originalInactiveMessage, $boardWasActive, $log);
            }

            // Remove old versions of the code backups.
            self::_deleteOldCodeVersions($opt->getDirectory(), $opt->getCodecopies(), $log, $opt->getFileExtension());

            // Make sure the new code backup exists and is not 0 length.
            if (is_readable($opt->getBackuppath())) {
                $log->write("Verified that code backup exists... ");
                if (filesize($opt->getBackuppath()) == 0) {
                    $log->write("Code backup exists but has 0 length. ", "ERROR");
                    throw new XenForo_Exception('Code backup exists but has 0 length. Run again with debug enabled');
                } else {
                    $log->write("Verified that code backup has data... ");
                }
            }
            else
            {
                $log->write("Code backup wasn't created. ", "ERROR");
                throw new XenForo_Exception('Code backup wasn\'t created. Run again with debug enabled');
            }

            if (class_exists('SolidMean_ForumBackup_PHPSSH2_CronEntry_Backup')) {
                $sftp = new SolidMean_ForumBackup_PHPSSH2_CronEntry_Backup('code');
                $sftp->upload($opt, $log);
            }

            if (class_exists('SolidMean_ForumBackup_DropBox_CronEntry_Backup')) {
                $dropbox = new SolidMean_ForumBackup_DropBox_CronEntry_Backup('code');
                $dropbox->upload($opt, $log);
            }

            if (class_exists('SolidMean_ForumBackup_GoogleDrive_CronEntry_Backup')) {
                $gdrive = new SolidMean_ForumBackup_GoogleDrive_CronEntry_Backup('code');
                $gdrive->upload($opt, $log);
            }

            $opt->setStoptime(time());

            // Generate an email with information if the admin has chosen to receive one.
            if($opt->getEmail())
            {
                self::_sendEmail($opt);
            }

            $log->write("END " . $opt->getType() . " backup");
        }

    }

    /**
     * This check is done at installation, but it also required in case the server configuration
     * has been changed.
     * @throws XenForo_Exception
     */
    private static function _assertExecFunction() {
        // Try to see if the exec function exists, and actually works.
        if(! exec('echo SolidMeanForumBackup') == 'SolidMeanForumBackup')
        {
            throw new XenForo_Exception('The PHP exec() function is required for this add-on.', true);
        }
    }


     /**
     * This keeps the database credentials in a hidden, temporary config file that
     * mysqldump will use.
     *
     * @param $directory - Where to store the hidden file
     * @param $log - The log file.
     * @return array - The database configuration parms.
     * @throws XenForo_Exception - Unable to create temp config file.
     */

    private static function _writeTempConfig($directory, SolidMean_ForumBackup_Helper_Log $log)
    {
        $dbconfig = XenForo_Model::create('SolidMean_ForumBackup_Model_Database')->getConfig();

        $tempconfig = $directory . '.ForumBackup_' . substr(md5(rand()), 0, 7) . '.cnf';

        $fp = fopen($tempconfig, "w");

        fwrite($fp,
            "# This file is used for the ForumBackup addon in XenForo, it should be deleted\n[client]\n"  .
            "host=" . $dbconfig['host'] . "\n" .
            "password=\"" . $dbconfig['password'] . "\"\n" .
            "user=" . $dbconfig['username'] . "\n" .
            "port=" . $dbconfig['port'] . "\n");

        fclose($fp);

        $log->write("Created temp config:  ".$tempconfig);

        return array('file' => $tempconfig, 'dbname' => $dbconfig['dbname']);
    }

    /**
     * Backup the database using the mysqldump command via PHP exec.
     *
     * @param SolidMean_ForumBackup_Options $opt
     * @param SolidMean_ForumBackup_Helper_log $log
     * @throws XenForo_Exception
     * @internal param $backup
     * @return bool
     */
    private static function _backupDatabase(SolidMean_ForumBackup_Options $opt, SolidMean_ForumBackup_Helper_log $log)
    {
        $command = $opt->getMysqldumppath() .
            ' --defaults-extra-file=' . $opt->getDbconfigfile() .
            ' --single-transaction ' .
            $opt->getDbname() . self::_formatTableExclusions($opt->getDbname(),$opt->getExcludeTbls()) .
            $opt->getExportoptions() .
            ' 2>> ' .  $log->getPath();

        $log->write( "Running database backup with: " . $command);

        // Run the database backup
		XenForo_Model::create('SolidMean_ForumBackup_Model_Database')->backup($command);

        $log->write("Database backup complete");

        return true;
    }

    /**
     * This method backs up the XenForo root directory to the same directory that the database
     * directory resides.
     *
     * @param $opt
     * @param $log
     * @internal param $backup
     * @return bool
     */
    private static function _backupCode(SolidMean_ForumBackup_Options $opt, SolidMean_ForumBackup_Helper_log $log)
    {
        $compressioncommand = $opt->getCompressiontype();
        if($opt->getGzippath() != 'gzip')
        {
            $compressioncommand = $opt->getGzippath();
        }

        // The tar (can't seem to get piping to work between tar and gzip, why why why?
        $command = $opt->getTarpath() . ' --ignore-failed-read -f ' . trim($opt->getBackuppath(),'.gz') . ' -cP ' .
            XenForo_Application::getInstance()->getRootDir() . self::_formatFileExclusions($opt->getExcludeDirs()) .
            ' 2>> ' .  $log->getPath();

        $log->write("Creating the tar file: " . $command);

        exec($command);

        if($opt->getCompressCode())
        {
            // Now the gzip/pigz if compress is enabled.
            $command = $compressioncommand . ' ' . trim($opt->getBackuppath(),'.gz') .  ' 2>> ' .  $log->getPath();
            $log->write("Compressing the file: " . $command);

            exec($command);
        }
        else
        {
            $log->write("No file compression chosen");
        }

        $log->write("Code backup complete... ");

        return true;

    }

    /**
     * This method Removes old versions of database backups outside the bounds of the number of copies to keep.
     *
     * @param $directory - Which directory to find the files
     * @param $copies - How many copies of the backup files to keep.
     * @param $log - The log file
     * @param $extension - What type of file extension are we looking for.
     * @return bool - Don't think this is necessary
     */
    private static function _deleteOldSQLVersions($directory, $copies, SolidMean_ForumBackup_Helper_log $log, $extension)
    {
        $log->write("Deleting old database backups, keeping " . $copies);

        // Read all database backup files into an array
        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle)))
            {
                if(self::extensionMatches($file, $extension))
                {
                    $sqls[] = array( filemtime($directory . $file), $directory . $file );
                    $log->write("Found backup file: " .$file .", modified " .
                        date ("Y-m-d H:i:s", filemtime($directory . $file)));
                }

             }
            closedir($handle);
        }

        $numberfilesdeleted = 0;
        if(!empty($sqls))
        {
            // Sort the files by their modified time, in reverse order (newest first).
            rsort($sqls);
            $log->write ("Sorted " . count($sqls) . " filenames by filemtime");
            // Delete any backup files outside the bounds of the number of copies we want to keep.
            for($x = $copies; $x < count($sqls); $x++)
            {

                if(unlink ($sqls[$x][1])) {
                    $log->write("Deleted sql backup: " . $sqls[$x][1]);
                    $numberfilesdeleted++;
                }
                else {
                    $log->write("Could not delete sql backup: " . $sqls[$x][1]);
                }

            }

        }

        if($numberfilesdeleted == 0)
        {
            $log->write("No sql backups to delete...");
        }

        return true;
    }

    /**
     * This method Removes old versions of code backups outside the bounds of the number of copies to keep.
     *
     * @param $directory - Which directory to find the files
     * @param $copies - How many copies of the backup files to keep.
     * @param $log - The log file
     * @param $extension - What type of file extension are we looking for.
     * @return bool - Don't think this is necessary
     *
     */
    private static function _deleteOldCodeVersions($directory, $copies, SolidMean_ForumBackup_Helper_log $log, $extension)
    {
        $log->write("Deleting old code backups, keeping " . $copies);

        // Read all code backup files into an array
        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle)))
            {
                if(self::extensionMatches($file, $extension))
                {
                    $codes[] = array( filemtime($directory . $file), $directory . $file );
                    $log->write("Found backup file: " . $file .", modified " .
                        date ("Y-m-d H:i:s", filemtime($directory . $file)));
                }
            }
            closedir($handle);
        }

        $numberfilesdeleted = 0;
        if(!empty($codes))
        {
            // Sort the files by their modified time, in reverse order (newest first).
            rsort($codes);
            $log->write ("Sorted " . count($codes) . " filenames by filemtime");

            // Delete any backup files outside the bounds of the number of copies we want to keep.
            for($x = $copies; $x < count($codes); $x++)
            {
                if(unlink ($codes[$x][1])) {
                    $log->write("Deleted code backup: " . $codes[$x][1]);
                    $numberfilesdeleted++;
                }
                else {
                    $log->write("Could not delete code backup: " . $codes[$x][1]);
                }
            }
        }
        if($numberfilesdeleted == 0)
        {
            $log->write("No code backups to delete...");
        }

        return true;
    }

    /**
     * This method just checks to see if the board was active when the backup process started,
     * and if so, disables the board, returning the original "inactive" message, to restore later.
     *
     * @param $boardWasActive
     * @param SolidMean_ForumBackup_Options $opt
     * @param SolidMean_ForumBackup_Helper_log $log
     * @return mixed
     * @throws XenForo_Exception
     */
    private static function _disableBoard($boardWasActive,
                                         SolidMean_ForumBackup_Options $opt,
                                         SolidMean_ForumBackup_Helper_log $log)
    {
        // Save the original 'board inactive' message, to restore later, possibly.
        $originalInactiveMessage =  XenForo_Application::getOptions()->boardInactiveMessage;
        if($boardWasActive)
        {
            $newmessage = $opt->getMaintenancemessage();

            if(empty($newmessage)) {
                $newmessage = $originalInactiveMessage;
            }

            $log->write("Turning forum off...");
            XenForo_Model::create('SolidMean_ForumBackup_Model_Board')->setStatus(0,
                $newmessage);
        }
        else
        {
            $log->write("Forum is already in maintenance mode... ");
        }

        return $originalInactiveMessage;
    }

    /**
     * This method enables the board if the board wasn't already in maintenance mode by an admin.
     *
     * @param $originalInactiveMessage
     * @param $boardWasActive
     * @param SolidMean_ForumBackup_Helper_log $log
     * @throws XenForo_Exception
     */
    private static function _enableBoard($originalInactiveMessage,
                                        $boardWasActive,
                                        SolidMean_ForumBackup_Helper_log $log)
    {
        // Don't re-enable the board if it was already in maintenance mode.
        if($boardWasActive)
        {
            $log->write("Turning forum on... ");
            // Make the board active again, restoring original boardInactiveMessage
            XenForo_Model::create('SolidMean_ForumBackup_Model_Board')->setStatus(1,
                $originalInactiveMessage);
        }
        else
        {
            $log->write("Not enabling forum, it was disabled when backup started. ");
        }
    }

    /**
     * Send an email to the admin that the backup has finished.
     *
     * @param SolidMean_ForumBackup_Options $opt
     * @throws Zend_Exception
     */
    private static function _sendEmail(SolidMean_ForumBackup_Options $opt)
    {
        // Build the template with the start/stop times of the backup, and elapsed time.
        $starttime = new DateTime(strftime("%X", $opt->getStarttime()), XenForo_Locale::getDefaultTimeZone());
        $stoptime = new DateTime(strftime("%X", $opt->getStoptime()), XenForo_Locale::getDefaultTimeZone());
        $elapsedtime = $starttime->diff($stoptime);

        // Load up the phrases with their variable replacements.
        $subject = new XenForo_Phrase('solidmean_forumbackup_email_subject',
            array(
                'type' => $opt->getType(),
                'board_title' => XenForo_Application::get('options')->boardTitle
            )
        );
        $message = new XenForo_Phrase('solidmean_forumbackup_email_body_html',
            array(
                'type' => $opt->getType(),
            )
        );

        $mailtemplate = 'solidmean_forumbackup_email_admin';

        $mailParams = array(
            'type' => $opt->getType(),
            'starttime' => strftime("%c", $opt->getStarttime()),
            'stoptime' => strftime("%c", $opt->getStoptime()),
            'elapsedtime' => $elapsedtime->format('%H:%I:%S'),
            'path' => $opt->getBackuppath(),
            'subject' => $subject,
            'message' => $message,
        );

        $mail = XenForo_Mail::create($mailtemplate, $mailParams, 0);

        // Send the mail to the address listed in the addon options
        $mail->send(
            $opt->getEmail(),
            XenForo_Application::get('options')->boardTitle,
            array(
                'Sender' => XenForo_Application::get('options')->contactEmailAddress
            )

        );
    }

    /**
     * @param $dirs
     * @return string
     */
    private static function _formatFileExclusions($dirs) {

        if(empty($dirs)) {
            return '';
        }

        // Create an array of excluded dirs, trimmed.
        $excludes = array_map('trim', explode(',', trim($dirs)));

        // Create a string of tar --excludes to add to the command line
        $tarexclude = '';
        foreach($excludes as $exclude) {
            $tarexclude .= "--exclude='$exclude' ";
        }
        return ' '.$tarexclude;
    }

    /**
     * This method formats the list of comma delimited tables to exclude into a
     * string to pass to the mysqldump command.
     *
     * @param $db - The database that contains xenforo tables.
     * @param $tbls - The tables to ignore
     * @return string - The appropriate mysql CLI string to exclude tables.
     */
    private static function _formatTableExclusions($db, $tbls) {

        if(empty($tbls)) {
            return '';
        }

        // Create an array of excluded dirs, trimmed.
        $excludes = array_map('trim', explode(',', trim($tbls)));

        // Create a string of tar --excludes to add to the command line
        $tblexclude = '';
        foreach($excludes as $exclude) {
            $tblexclude .= "--ignore-table=$db.$exclude ";
        }
        return ' '.$tblexclude;
    }

    /**
     * This method matches a filename to a particular extension. The file name may
     * be a physical location, or just a name extracted from another service (i.e. DropBox)
     * This method is normally called when backup files are deleted.
     *
     * @param $filename - The filename that we're trying to match an extension to
     * @param $extension - The extension that we're looking for.
     * @return bool - Do they match or not?
     */
    public static function extensionMatches($filename, $extension) {

        // We just want to make sure that the file names we are reading are the ones are related to
        // this backup run. i.e. code or database, and if the files are compressed or not.
        if (substr($filename, -1 * strlen($extension)) == $extension) {
            return true;
        }

        return false;
    }
}
