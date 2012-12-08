<?php
class Chaplin_Model_Field_Hash
    extends Chaplin_Model_Field_Abstract
{
    protected $_arrFields = array();
    protected $_collFields = array();

    public static function createFromData(Chaplin_Dao_Interface $dao, Array $arrArray)
    {
        $hash = new static();
        foreach($arrArray as $strField => $mixedValue) {
            $hash->_getFieldObject($strField)->setFromData($mixedValue);
        }
        
        return $hash;
    }

    protected function __construct()
    {
        foreach($this->_arrFields as $strField => $strClass) {
            $this->_collFields[$strField] = new $strClass;
        }
    }
    
    public function getValue($mixedDefault)
    {
        return $this;
    }
    
    public function getFields(Chaplin_Dao_Interface $dao)
    {
        return $this->_collFields;
    }
    
    private function _getFieldObject($strName)
    {
        if (!isset($this->_collFields[$strName])) {
            throw new OutOfBoundsException('Invalid field: '.$strName);
        }
        return $this->_collFields[$strName];
    }
    
    protected function _getField($strName, $mixedDefault)
    {
        try {
            return $this->_getFieldObject($strName)->getValue($mixedDefault);
        } catch(OutOfBoundsException $e) {
            return $mixedDefault;
        }
    }
    
    protected function _setField($strName, $mixedValue)
    {
        $this->_getFieldObject($strName)->setValue($mixedValue);
        $this->_bIsDirty = true;
        return $this;
    }
}