# xfForumBackup
XenForo Addon that routinely backs up database and xfroot at scheduled intervals.

## About
This addon is distributed under the "SolidMean" brand is property of SolidMean Technology, LLC. More information about this addon can be found here:
http://sneakydave.com/community/products/solidmean-forumbackup.3/

## Requirements
* This add-on uses the PHP exec() function to run the mysqldump and tar commands to backup the database and forum directory, respectively. If you are on shared hosting, you may not have access to the exec() function. The installation should fail if this is the case.
* Your server must have the mysqldump and tar commands available. For 95% of XenForo installations, this shouldn't be a problem. You may have to specify the location of mysqldump in the options if the add-on tells you that mysqldump can't be found.
* For larger boards, you may need to have a pretty high PHP max_execution_time. From my testing, it took less than a minute to backup the database and forum root with 200,000+ posts and 3000+ members.
* XenForo 1.3.0 and above, and PHP 5.3+ is required.[/B][/B]

## What this add-on does NOT do

* This addon will not work on Windows servers.
* This addon will not allow you to restore from a backup.
* This addon does not provide a user interface to download backups.
* This addon does not have the ability to transfer backups offsite via ftp or other means.
* Integrated SFTP transfers can be utilized via this paid add-on: http://sneakydave.com/community/products/solidmean-forumbackup-sftp-transfer.5/
* Integrated Dropbox transfers can be utilized via this paid add-on: http://sneakydave.com/community/products/solidmean-forumbackup-dropbox-upload.8/
* See this guide on how to use WinSCP to transfer files: https://xenforo.com/community/resources/using-winscp-to-schedule-backup-transfers.3669/
