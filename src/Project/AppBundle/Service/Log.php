<?php

namespace Project\AppBundle\Service;

/**
 * Abstract class to save log messages.
 */
class Log {

    /**
     * The output format used.
     * Update writeMessageFactory function when adding new format.
     *
     * @var string
     */
    private $_sOutputFormat;

    /**
     * Path where save the log (start from app root directory)
     *
     * @var string
     */
    private $_sDirectoryPath;

    /**
     * Name of the log file
     *
     * @var string
     */
    private $_sFileName;

    /**
     * The max filesize before archive a log file.
     * The number should be in Bytes. 1Bytes = 1Megabytes
     * Default is set to 10Megabytes
     * 
     * @var int
     */
    private $_iFileMaxSize;

    /**
     * Required fields for the create message action
     * - module : string( Speaker, Lesson...)
     * - fields : array( id => 'new_id', title => 'new_title'...)
     *
     * @var array
     */
    private $_aRequiredFields = array('fields', 'module', 'username');

    /**
     * Message to add to save in log
     *
     * @var string
     */
    private $_sMessage = NULL;
    
    
    /**
     * Values for the function are given by the configuration file (located in
     * app/config/parameters.yml) and provided by the service log
     * (located in app/config/config.yml)
     *
     * @param string $sKernelPath    : App root directory of SF2
     * @param string $sDirectoryPath : Directory Path
     * @param string $sFileName      : File name in the directory path
     * @param string $sOutput        : Txt, SGBD...
     * @param int $iFileMaxSize      : Max bytes for a file
     * @return void|LogicException
     */
    public function __construct($sKernelPath, $sDirectoryPath, $sFileName, $sOutput, $iFileMaxSize)
    {
        $this->setOutputFormat($sOutput);
        $this->setDirectoryPath($sKernelPath, $sDirectoryPath);
        $this->setFileName($sFileName);
        $this->setFileMaxSize($iFileMaxSize);

        $this->archiveLogs();
    }


    /**
     * Create and format a message which will be insert in the log.
     *
     * @param array $aData    : @see $this->_aRequiredFields
     * @param string $sAction : delete, edit, add
     * @param bool $bFlagCustomLog : enable to create your own message (##TODO)
     * @throws void|Exception
     */
    public function createLog($aData, $sAction, $bFlagCustomLog = false)
    {
        if(false === $bFlagCustomLog) {
            // Check if data implements required fields
            foreach($this->_aRequiredFields as $sRequiredField) {
                if(isset($aData[$sRequiredField])) {
                    continue;
                } elseif(array_search($sRequiredField, $aData) !== false) {
                    continue;
                }
                throw new \Exception("The given data must implement '{$sRequiredField}'.");
            }
        } else {
            $sAction = 'customMessage';
        }

        // Start to write at the beginning of the message
        // by adding the current time
        $this->_sMessage = date('Y-m-d H:i:s').' :';

        // "Route" owing to the action name
        $this->{$sAction}($aData);

        // Factory for output tu use
        $this->writeMessageFactory();
    }


    /**
     * Format the message for deletion.
     *
     * @param array $aData
     */
    private function delete($aData)
    {
        $this->_sMessage .= "The {$aData['module']} has been deleted by the user {$aData['username']}.";
    }


    /**
     * Format the message when an occurrence is created.
     *
     * @param array $aData
     */
    private function add($aData)
    {
        $this->_sMessage .= "A new {$aData['module']} has been added by the user {$aData['username']}.".PHP_EOL;
        if( count($aData['fields']) > 0) {
            $this->_sMessage .= "The new values are :";
            foreach ($aData['fields'] as $_aValue) {
                $this->_sMessage .= $_aValue.';';
            }
        }
        // Remove last semi-colon
        $this->_sMessage = substr($this->_sMessage, 0, -1);
    }


    /**
     * Format the message when an occurrence is edited.
     *
     * @param array $aData
     */
    private function edit($aData)
    {
        $this->_sMessage .= "The {$aData['module']} has been edited by the user {$aData['username']}.".PHP_EOL;

        if( count($aData['fields']) > 0) {
            $this->_sMessage .= "The new values are :";
            foreach ($aData['fields'] as $_aValue) {
                $this->_sMessage .= $_aValue.';';
            }
        }
        // Remove last semi-colon
       $this->_sMessage = substr($this->_sMessage, 0, -1);
    }


    /**
     * Simple text log write by developper.
     *
     * @param string $aData
     */
    private function customMessage($sData)
    {
        $this->_sMessage .= htmlentities($sData);
    }


    /**
     * Write the log depends on the output format choose.
     *
     * @return void
     */
    private function writeMessageFactory()
    {
        switch ($this->_sOutputFormat) {
            case 'text':
                $this->writeText();
                break;
            case 'sgbd':
                break;
            default:
                throw new \Exception("The output {$this->_sOutputFormat}
                    is misspelled or not yet implemented.");
                break;
        }
    }


    /**
     * Write the log in a text file. Moreover, check if we have
     * permission to write into the file.
     *
     * @return boolean|Exception
     */
    private function writeText()
    {
        /** Check if we have grant all access on the directory. **/
        if(!is_dir($this->_sDirectoryPath)) {
            throw new \LogicException("Your path to directory {$this->_sDirectoryPath} is not correct.");
        } else {
            if(!is_writable($this->_sDirectoryPath)) {
                throw new \Exception("The directory {$this->_sDirectoryPath} should be writable.");
            }
        }

        // Concatenate folder path and file name.
        $sFullPath = $this->_sDirectoryPath.$this->_sFileName;

        /** Check if we have grant all access on the file. **/
        if(file_exists($sFullPath)) {
            if(!is_writable($sFullPath)) {
                if(!chmod($sFullPath, 0777)) {
                    throw new \Exception("Error when changing permission on the file {$this->_sFileName}.");
                }
            }
        } else {
            $bCreatedFile    = touch($sFullPath);
            $bPermissionFile = chmod($sFullPath, 0777);

            if(false === $bCreatedFile || false === $bPermissionFile) {
                throw new \Exception("Error creating/adding permission on {$sFullPath}.");
            }
        }

        // Insert newline at the end of current message.
        $this->_sMessage = $this->_sMessage . PHP_EOL;

        // file_put_contents return bytes on success and false if any error.
        $mResult = file_put_contents($sFullPath, $this->_sMessage, FILE_APPEND);

        if($mResult === false) {
            throw new \Exception('Error when writing log. Please contact administrator.');
        }

        return true;
    }


    /**
     * Create a Zip log file to save space on the hard disk.
     *
     * @return void|Exception
     */
    public function archiveLogs()
    {
        $_sFileFullPath = $this->_sDirectoryPath.$this->_sFileName;
        // Check if file already exist
        if(file_exists($_sFileFullPath)) {
            // Check if the file size is enough huge to be zip
            if(filesize($_sFileFullPath) >= $this->_iFileMaxSize) {
                $oZipArchive  = new \ZipArchive();
                $oCurrentDate = new \DateTime();

                // Create the zip archive log_date
                if ($oZipArchive->open($this->_sDirectoryPath."log_{$oCurrentDate->format('Y-m-d')}.zip",
                                       ZipArchive::CREATE) === TRUE)
                {
                    // Add the current file to the archive
                    $oZipArchive->addFile($_sFileFullPath, $this->_sFileName);
                    // Save and close archive
                    $oZipArchive->close();
                }
                // Delete the old file
                unlink($_sFileFullPath);
            }
        }
        
    }

/************************************************
******************* SETTERS *********************
*************************************************/

    /**
     * Set the output format
     *
     * @param string $sOutputFormat
     */
    public function setOutputFormat( $sOutputFormat)
    {
        $this->_sOutputFormat = $sOutputFormat;
    }

    /**
     * set the directory path
     *
     * @param string $sKernelPath
     * @param string $sDirectoryPath
     */
    public function setDirectoryPath( $sKernelPath, $sDirectoryPath)
    {
        $this->_sDirectoryPath = $sKernelPath . $sDirectoryPath;
    }

    /**
     * Set the log file name
     *
     * @param string $sFileName
     */
    public function setFileName( $sFileName)
    {
        $this->_sFileName = $sFileName;
    }

    /**
     * Set the file max size
     *
     * @param integer $iFileMaxSize
     */
    public function setFileMaxSize( $iFileMaxSize)
    {
        $this->_iFileMaxSize = (int) $iFileMaxSize;
    }

}