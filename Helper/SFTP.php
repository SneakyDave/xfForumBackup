<?php
/**
 * Class SolidMean_ForumBackup_Helper_SFTP
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2014 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */
class SolidMean_ForumBackup_Helper_SFTP
{

    /**
     * @var
     */
    private $connection;
    private $log;
    private $opt;

    private $remotepath;

    /**
     * The SFTP object to transfer files to a different host.
     *
     * @param SolidMean_ForumBackup_Options $opt
     * @param SolidMean_ForumBackup_Helper_Log $log
     * @throws XenForo_Exception
     */
    public function __construct(SolidMean_ForumBackup_Options $opt, SolidMean_ForumBackup_Helper_Log $log)
    {
        $this->log = $log;
        $this->opt = $opt;

        if (!function_exists('ssh2_connect'))
        {
            throw new XenForo_Exception("The PHPSSH2 library is not installed in this environment.");
        }

        $remotedir = $opt->getSFTPremotedir();
        if(empty($remotedir))
        {
            $this->log->write("No remote directory specified ", 'ERROR');
            throw new XenForo_Exception("No remote directory defined in options.");
        }

        // Cheap way to validate the directory and file name
        if (! strpbrk($opt->getSFTPremotedir(), "\\?%*:|\"<>") === FALSE)
        {
            $this->log->write("Invalid remote directory name.", 'ERROR');
            throw new XenForo_Exception("Invalid remote directory name.");
        }

        return true;

     }

    /**
     * Try to make a connection to the remote host
     *
     * @return bool
     * @throws XenForo_Exception
     */
    public function makeConnection()
    {
        try
        {
            $this->log->write("Connecting to " . $this->opt->getSFTPhost());
            if($this->connection = @ssh2_connect($this->opt->getSFTPhost(), $this->opt->getSFTPport()))
            {
                $this->log->write("Connected... ");
            }
        }
        catch (Exception $e)
        {
            $this->log->write("Could not connect: " . $e->getMessage(),'ERROR');
            throw new XenForo_Exception("Could not connect to the host " . $this->opt->getSFTPhost() .
                " via ssh protocol.");
        }

        return true;


    }

    /**
     * Try to authenticate the user to the host.
     *
     * @return bool
     * @throws XenForo_Exception
     */
    public function authenticateUser()
    {
        $this->log->write("Authenticating user...");
        if (@ssh2_auth_password($this->connection, $this->opt->getSFTPusername(), $this->opt->getSFTPpassword()))
        {
            $this->log->write("User Authenticated...");
        }
        else
        {
            $this->log->write("Could not authenticate: ",'ERROR');
            throw new XenForo_Exception("Could not authenticate user to ssh host.");
        }

        return true;

    }

    /**
     * Try to transfer the file to the external host
     *
     * @return bool
     * @throws XenForo_Exception
     */
    public function transferBackup()
    {
        $this->opt->setSFTPRemotedir(rtrim($this->opt->getSFTPRemotedir(), '/') . '/');

        self::setRemotepath(  $this->opt->getSFTPRemotedir() . basename($this->opt->getBackuppath()) );

        $this->log->write("Trying to save local file: " . $this->opt->getBackuppath() .
            " to remote file: " . self::getRemotepath() .
            " with perms of: " . $this->opt->getSFTPfileperms());

        // Need to convert the permissions to octal for this function to work.
        if (ssh2_scp_send($this->connection, $this->opt->getBackuppath(), self::getRemotepath(),
            octdec($this->opt->getSFTPfileperms())))
        {
            $this->log->write("File saved...");
        }
        else
        {
            $this->log->write("Could not save local file " . $this->opt->getBackuppath() .
                " to remote file: " . self::getRemotepath(), 'ERROR');
            throw new XenForo_Exception("Could not save local file to remote location.");

        }

        return true;
    }

    /**
     * Verify the saved backup on the external host
     *
     * @return bool
     */
    public function verifyBackup()
    {
        $sftp = ssh2_sftp($this->connection);
        $statinfo = ssh2_sftp_stat($sftp, self::getRemotepath() );

        if(isset($statinfo['size']))
        {
            // If the backup file size, and the external file size are the same, then we're golden
            if($statinfo['size'] == $this->opt->getFilesize())
            {
                $this->log->write("Local and external backup files match in size: " . $statinfo['size'] . " bytes");
                return true;
            }
            else
            {
                // This is a warning for now, as I'm not sure how accurate comparing these sizes really are.
                $this->log->write("Local and external backup files not same size: Local (" . $this->opt->getFilesize() .
                    "), External (" . $statinfo['size'] . ")", 'MINOR');
            }
        }
        else
        {
            $this->log->write("Could not stat external file:" . self::getRemotepath(), "ERROR");
            throw new XenForo_Exception("Could not get the external file's stat info");
        }

        return true;
    }



    /**
     * Delete the local copy of the backup file after it is transferred to the external host.
     * @param $path
     * @throws XenForo_Exception
     */
    public function deleteLocalCopy($path)
    {
        $this->log->write("Deleting local copy of: " . $path);

        try
        {
            @unlink($path);
            $this->log->write("File deleted...");

            return true;
        }
        catch (Exception $e)
        {
            $this->log->write("Could not delete local copy: " . $path . " : " . $e->getMessage(),'ERROR');
            throw new XenForo_Exception("Could not delete local copy of backup.");

        }


    }

    /**
     * @return mixed
     */
    public function getRemotepath()
    {
        return $this->remotepath;
    }

    /**
     * @param mixed $remotepath
     */
    public function setRemotepath($remotepath)
    {
        $this->remotepath = $remotepath;
    }

}
