<?php

namespace Project\AppBundle\Service;

/**
 * Abstract class to save log messages.
 */
class Log {

    /**
     * The outpout format used.
     * Update writeMessageFactory function when adding new format.
     *
     * @var string
     */
    private $_sOutpoutFormat = 'text';

    /**
     * Path where save the log
     *
     * @var string
     */
    private $_sFilePath = '/var/www/ProjectB/app/cache/lpdw-website.log';


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

        // Factory for outpout tu use
        $this->writeMessageFactory();
    }


    /**
     * Format the message for deletion.
     *
     * @param array $aData
     */
    private function delete($aData)
    {
        $this->_sMessage .= "The {$aData['module']} has been deleted by the username {$aData['username']}.";
    }


    /**
     * Format the message when an occurrence is created.
     *
     * @param array $aData
     */
    private function add($aData)
    {
        $this->_sMessage .= "A new {$aData['module']} has been added by the username {$aData['username']}.".PHP_EOL;
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
        $this->_sMessage .= "The {$aData['module']} has been edited by the username {$aData['username']}.".PHP_EOL;

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
     * @param type $aData
     */
    private function customMessage($sData)
    {
        $this->_sMessage .= $sData;
    }


    /**
     * Write the log depends on the outpout format choose.
     *
     * @return void
     */
    private function writeMessageFactory()
    {
        switch ($this->_sOutpoutFormat) {
            case 'text':
                $this->writeText();
                break;
            case 'sgbd':
                break;
            default:
                break;
        }
    }


    /**
     * Write the log in a text file.
     *
     * @return boolean
     */
    private function writeText()
    {
        // Create file if doesn't exist.
        if(!file_exists($this->_sFilePath)) {
            touch($this->_sFilePath);
            chmod($this->_sFilePath, 0777);
        }

        // Make the file writable if it's not the case.
        if(!is_writable($this->_sFilePath)) {
            chmod($this->_sFilePath, 0777);
        }

        // Add newline at the end of current data.
        $this->_sMessage = $this->_sMessage . PHP_EOL;


        // file_put_contents return bytes on success and false if any error.
        $mResult = file_put_contents($this->_sFilePath, $this->_sMessage, FILE_APPEND | LOCK_EX);

        if($mResult === false) {
            //throw new Exception('Error when writing the log. Please contact administrator.');
            return false;
        }

        return true;
    }
}
