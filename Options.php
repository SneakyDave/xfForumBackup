<?php
/**
 * Class SolidMean_ForumBackup_Options
 * @author      David Dotson <sneaky@sneakydave.com>
 * @copyright   �2014 Solid Mean Technology. All rights reserved.
 * @link        http://sneakydave.com
 * @package     SolidMean_ForumBackup
 */

class SolidMean_ForumBackup_Options
{

    // These are variables obtained from the addon options
    private $backuppath;
    private $dateformat;
    private $databasecopies;
    private $codecopies;
    private $excludedirs;
    private $excludetbls;
    private $mysqldumppath;
    private $tarpath;
    private $gzippath;
    private $debug;
    private $includedatabase;
    private $includecode;
    private $maintenancemessage;
    private $maintenancedatabase;
    private $maintenancecode;
    private $email;
    private $compressiontype;
    private $exportoptions;
    private $compressdatabase;
    private $compresscode;

    // These are variables only available in this class.
    private $type;
    private $directory;
    private $filename;
    private $extension;
    private $starttime;
    private $stoptime;
    private $dbconfigfile;
    private $dbname;


    /**
     * Constructor gets the addon options, and formats them for the backup.
     *
     * @param $type
     */
    function __construct($type)
    {
        $options = XenForo_Application::getOptions();

        $this->type = $type;
        self::setFilename($options->SolidMeanForumBackupFileName);
        self::setDirectory($options->SolidMeanForumBackupDirectory);
        self::setDateformat($options->SolidMeanForumBackupDateFormat);
        self::setDatabasecopies($options->SolidMeanForumBackupCopies);
        self::setCodecopies($options->SolidMeanForumBackupCopiesCode);
        self::setExcludeDirs($options->SolidMeanForumBackupExcludeDirs);
        self::setExcludeTbls($options->SolidMeanForumBackupExcludeTbls);
        self::setMysqldumppath($options->SolidMeanBackupForumMysqldumpPath);
        self::setTarpath($options->SolidMeanBackupForumTarPath);
        self::setCompressiontype($options->SolidMeanForumBackupCompressionType);
        self::setCompressDatabase($options->SolidMeanForumBackupCompressDatabase);
        self::setCompressCode($options->SolidMeanForumBackupCompressCode);
        self::setGzippath($options->SolidMeanBackupForumGzipPath);
        self::setDebug($options->SolidMeanForumBackupDebug);
        self::setIncludedatabase($options->SolidMeanForumBackupIncludeDatabase);
        self::setIncludecode($options->SolidMeanForumBackupIncludeCode);
        self::setMaintenancemessage($options->SolidMeanForumBackupInactiveMessage);
        self::setMaintenancedatabase($options->SolidMeanForumBackupInactive);
        self::setMaintenancecode($options->SolidMeanForumBackupInactiveCode);
        self::setEmail($options->SolidMeanForumBackupEmail);
        self::setStarttime(time());
        self::setStoptime(0);
        self::setExportoptions('');
        self::setDbconfigfile('');
        self::setDbname('');

        if($this->type == "Database")
        {
            $compressioncommand = $this->getCompressiontype();
            if($this->getGzippath() != 'gzip')
            {
                $compressioncommand = $this->getGzippath();
            }
            if($this->getCompressDatabase())
            {
                $this->extension = ".sql.gz";
            }
            else {
                $this->extension = ".sql";
            }
            $this->backuppath = $this->directory . $this->filename . '_' . Date($this->dateformat) . $this->extension;

            if ($this->getCompressDatabase()) {
                $this->exportoptions = " | " . $compressioncommand .  " > " . $this->backuppath;
            }
            else {
                $this->exportoptions = " > " . $this->backuppath;
            }

        }
        elseif ($this->type == "Code")
        {
            if($this->getCompressCode())
            {
                $this->extension = ".code.tar.gz";
            }
            else {
                $this->extension = ".code.tar";
            }
            $this->backuppath = $this->directory . $this->filename . '_' . Date($this->dateformat) . $this->extension;
        }
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getCodecopies()
    {
        return $this->codecopies;
    }

    /**
     * @param mixed $codecopies
     */
    public function setCodecopies($codecopies)
    {
        $this->codecopies = $codecopies;
    }

    /**
     * @return mixed
     */
    public function getExcludeDirs()
    {
        return $this->excludedirs;
    }

    /**
     * @param mixed $excludedirs
     */
    public function setExcludeDirs($excludedirs)
    {
        $this->excludedirs = $excludedirs;
    }

    public function getExcludeTbls()
    {
        return $this->excludetbls;
    }

    /**
     * @param mixed $excludetbls
     */
    public function setExcludeTbls($excludetbls)
    {
        $this->excludetbls = $excludetbls;
    }

    /**
     * @return mixed
     */
    public function getDatabasecopies()
    {
        return $this->databasecopies;
    }

    /**
     * @param mixed $databasecopies
     */
    public function setDatabasecopies($databasecopies)
    {
        $this->databasecopies = $databasecopies;
    }

    /**
     * @return mixed
     */
    public function getDateformat()
    {
        return $this->dateformat;
    }

    /**
     * @param mixed $dateformat
     */
    public function setDateformat($dateformat)
    {
        $this->dateformat = $dateformat;

        if (empty($this->dateformat))
        {
            $this->dateformat = 'Y-m-d-His';
        }

    }

    /**
     * @return mixed
     */
    public function getDbconfigfile()
    {
        return $this->dbconfigfile;
    }

    /**
     * @param mixed $dbconfigfile
     */
    public function setDbconfigfile($dbconfigfile)
    {
        $this->dbconfigfile = $dbconfigfile;
    }

    /**
     * @return mixed
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    /**
     * @param mixed $dbname
     */
    public function setDbname($dbname)
    {
        $this->dbname = $dbname;
    }

    /**
     * @return mixed
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * @param mixed $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @return mixed
     */
    public function getDirectory()
    {
        if ($this->directory != '/')
        {
            return $this->directory;
        }
        else
        {
            return false;
        }
    }

    /**
     * @param mixed $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;

        $this->directory = rtrim($this->directory, '/') . '/';

        if(strstr($this->directory, ' ' )) {
            $this->directory =  '"' . $this->directory . '"';
        }

    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getExportoptions()
    {
        return $this->exportoptions;
    }

    /**
     * @param mixed $exportoptions
     */
    public function setExportoptions($exportoptions)
    {
        $this->exportoptions = $exportoptions;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        if (empty($this->filename))
        {
            $this->filename = 'forumbackup';
        }

    }

    /**
     * @return mixed
     */
    public function getGzippath()
    {
        return $this->gzippath;
    }

    /**
     * @param mixed $gzippath
     */
    public function setGzippath($gzippath)
    {
        $this->gzippath = $gzippath;

        if (empty($this->gzippath))
        {
            // linux assumed, no extension
            $this->gzippath =  'gzip';
        }
        else
        {
            // Windows fixes
            if(strstr($this->gzippath, ' ' ))
            {
                $this->gzippath =  '"' . $this->gzippath . '"';
            }
        }
    }

    /**
     * @return mixed
     */
    public function getIncludecode()
    {
        return $this->includecode;
    }

    /**
     * @param mixed $includecode
     */
    public function setIncludecode($includecode)
    {
        $this->includecode = $includecode;
    }

    /**
     * @return mixed
     */
    public function getIncludedatabase()
    {
        return $this->includedatabase;
    }

    /**
     * @param mixed $includedatabase
     */
    public function setIncludedatabase($includedatabase)
    {
        $this->includedatabase = $includedatabase;
    }

    /**
     * @return mixed
     */
    public function getMaintenancecode()
    {
        return $this->maintenancecode;
    }

    /**
     * @param mixed $maintenancecode
     */
    public function setMaintenancecode($maintenancecode)
    {
        $this->maintenancecode = $maintenancecode;
    }

    /**
     * @return mixed
     */
    public function getMaintenancedatabase()
    {
        return $this->maintenancedatabase;
    }

    /**
     * @param mixed $maintenancedatabase
     */
    public function setMaintenancedatabase($maintenancedatabase)
    {
        $this->maintenancedatabase = $maintenancedatabase;
    }

    /**
     * @return mixed
     */
    public function getMaintenancemessage()
    {
        return $this->maintenancemessage;
    }

    /**
     * @param mixed $maintenancemessage
     */
    public function setMaintenancemessage($maintenancemessage)
    {
        $this->maintenancemessage = $maintenancemessage;
    }

    /**
     * @return mixed
     */
    public function getMysqldumppath()
    {
        return $this->mysqldumppath;
    }

    /**
     * @param mixed $mysqldumppath
     */
    public function setMysqldumppath($mysqldumppath)
    {
        $this->mysqldumppath = $mysqldumppath;

        if (empty($this->mysqldumppath))
        {
            // linux assumed, no extension
            $this->mysqldumppath =  'mysqldump';
        }
        else
        {
            // Windows fixes
            if(strstr($this->mysqldumppath, ' ' ))
            {
                $this->mysqldumppath =  '"' . $this->mysqldumppath . '"';
            }
        }
    }

    /**
     * @return mixed
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * @param mixed $starttime
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    }

    /**
     * @return mixed
     */
    public function getStoptime()
    {
        return $this->stoptime;
    }

    /**
     * @param mixed $stoptime
     */
    public function setStoptime($stoptime)
    {
        $this->stoptime = $stoptime;
    }

    /**
     * @return mixed
     */
    public function getTarpath()
    {
        return $this->tarpath;
    }

    /**
     * @param mixed $tarpath
     */
    public function setTarpath($tarpath)
    {
        $this->tarpath = $tarpath;

        if (empty($this->tarpath))
        {
            // linux assumed, no extension
            $this->tarpath =  'tar';
        }
        else
        {
            // Windows fixes
            if(strstr($this->tarpath, ' ' ))
            {
                $this->tarpath =  '"' . $this->tarpath . '"';
            }
        }
    }

    /**
     * @return string
     */
    public function getBackuppath()
    {
        return $this->backuppath;
    }

    /**
     * @param string $backuppath
     */
    public function setBackuppath($backuppath)
    {
        $this->backuppath = $backuppath;
    }

    /**
 * @return string
 */
    public function getCompressiontype()
    {
        return $this->compressiontype;
    }

    /**
     * @param string $compressiontype
     */
    public function setCompressiontype($compressiontype)
    {
        $this->compressiontype = $compressiontype;
    }

    /**
     * @return boolean
     *
     */
    public function getCompressDatabase()
    {
        return $this->compressdatabase;
    }

    /**
     * @param boolean $compressdatabase
     */
    public function setCompressDatabase($compressdatabase)
    {
        $this->compressdatabase = $compressdatabase;
    }

    /**
 * @return boolean
 *
 */
    public function getCompressCode()
    {
        return $this->compresscode;
    }

    /**
     * @param boolean $compresscode
     */
    public function setCompressCode($compresscode)
    {
        $this->compresscode = $compresscode;
    }

    /**
     * @return string
     *
     */
    public function getFileExtension()
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     */
    public function setFileExtension($extension)
    {
        $this->extension = $extension;
    }


}