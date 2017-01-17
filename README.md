# ambimax® ArrayExport

Magento module that lets you export all product as array to be
modified and arranged during export and saved to csv or any other format

## Installation

Copy src/* to your root folder or use composer.json:
 
```
"require": {
    "ambimax/magento-module-ambimax-arrayexport": "~1.0"
}
```

## Usage

### 1.) Create a file for all logic

Create an Model file in app/code/comminity/My/Module/Model/Export.php

```
class My_Module_Model_Export {

    /** @var  Mage_ImportExport_Model_Export_Adapter_Csv */
    protected $_writeAdapter;
    
    /** @var bool */
    protected $_headerColsSet = false;

    /**
     * Init to export all products
     *
     * @param Varien_Event_Observer $observer
     */
    public function initExport()
    {
        $file = Mage::getBaseDir('var').DS.'export'.DS.'exportfile.csv';
        $this->_writeAdapter = new Mage_ImportExport_Model_Export_Adapter_Csv($file);

        $data = array(
            "entity" => 'catalog_product',
            "file_format" => 'csv',
            "export_filter" => array(),
            "skip_attr" => array(),
        );

        /** @var Ambimax_ArrayExport_Model_Export $export */
        $export = Mage::getModel('ambimax_arrayexport/export');
        $export->setData($data);
        $export->export();
    }

    /**
     * Writes every product to $_writeAdapter
     *
     * @param Varien_Event_Observer $observer
     */
    public function writeRow(Varien_Event_Observer $observer)
    {
        // Set header cols
        if( ! $this->_headerColsSet) {
            $this->_writeAdapter->setHeaderCols($observer->getHeaderCols());
            $this->_headerColsSet = true;
        }

        // $data contains store codes with product data
        $data = $observer->getProductData();
        $row = $data['default'];

        // do something with all the data

        // write to csv
        $this->_writeAdapter->writeRow($row);
    }
}
```

### 2.) Add an Observer.php

Create an Observer file in app/code/community/My/Module/Model/Observer.php

_This file is required so we use a Singleton at all times, otherwise
the writeAdapter will not be set_

```
class My_Module_Model_Observer {

    /**
     * Init to export all products
     *
     * @param Varien_Event_Observer $observer
     */
    public function initCompleteProductExport(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('my_module/export')->initExport();
    }

    /**
     * Write row to file
     *
     * @param Varien_Event_Observer $observer
     */
    public function writeRow(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('my_module/export')->writeRow($observer);
    }
}
```

## 3.) Add row handler event

This event is supposed to handle the formation of your data and send
it to your write adapter

```
<events>
    <ambimax_arrayexport_product_row>
        <observers>
            <start_export>
                <class>my_module/observer</class>
                <method>writeRow</method>
            </start_export>
        </observers>
    </ambimax_arrayexport_product_row>
</events>
```

## 4.) Add cronjob to trigger the event

```
<crontab>
    <jobs>
        <my_module_product_export>
            <schedule><cron_expr>0 0 * * *</cron_expr></schedule>
            <run>
                <model>my_module/observer::initExport</model>
            </run>
        </my_module_product_export>
    </jobs>
</crontab>
```

## Tips

- You can use [n98-magrun](https://github.com/netz98/n98-magerun) to trigger your cronjob from the command line
- You can use [Aoe_Scheduler](https://github.com/AOEpeople/Aoe_Scheduler) for usage within Magento
 
## License

[MIT License](http://choosealicense.com/licenses/mit/)

## Author Information

 - [Tobias Schifftner](https://twitter.com/tschifftner), [ambimax® GmbH](https://www.ambimax.de)