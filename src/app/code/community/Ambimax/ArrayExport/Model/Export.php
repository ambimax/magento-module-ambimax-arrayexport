<?php

class Ambimax_ArrayExport_Model_Export extends Mage_ImportExport_Model_Export
{
    /**
     * Use own export entity adapter
     *
     * @return Ambimax_ArrayExport_Model_Export_Entity_Product|Mage_ImportExport_Model_Export_Entity_Abstract
     */
    protected function _getEntityAdapter()
    {
        if (!$this->_entityAdapter && $this->getEntity() == 'catalog_product') {
            $this->_entityAdapter = new Ambimax_ArrayExport_Model_Export_Entity_Product();
            $this->_entityAdapter->setEventPrefix($this->getEventPrefix());
        }

        return parent::_getEntityAdapter();
    }

}