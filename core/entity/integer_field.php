<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

class IntegerField extends ScalarField {
	protected $size = 10;

    public function __construct($name, $parameters = array()) {

        parent::__construct($name, $parameters);

	    $this->dataType = 'int';
	    $this->fieldType = 'integer';

        if(isset($parameters['size']) && intval($parameters['size']) > 0)
        {
            $this->size = intval($parameters['size']);
        }

    }

    public function getSize () {
        return $this->size;
    }

    /*
    public function getArray() {
        $arData = parent::getArray();

        $arData['size'] = self::getSize();

        return $arData;
    }
    */

    public function saveDataModification ($value)
    {
        $value = intval($value);
        $value = parent::saveDataModification($value);

        return $value;
    }

    public function fetchDataModification ($value)
    {
        $value = intval($value);
        $value = parent::fetchDataModification($value);

        return $value;
    }
}