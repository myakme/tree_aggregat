<?php
namespace Model;

use Model\Entity\Country;
use Model\Entity\State;

/**
 * Object for access to data - loads and prepares model-objects
 *
 * Class Repository
 * @package Model
 */
class Repository
{

    const STATES_DIR = '/states';

    /**
     * @var Country
     */
    protected $country = null;

    protected $dataFolder = '';
    protected $countryName = '';

    /**
     * @param $dataFolder
     * @param $countryName
     */
    public function __construct( $dataFolder, $countryName )
    {
        $this->dataFolder = $dataFolder;
        $this->countryName = $countryName;
    }

    /**
     * @return Country
     */
    public function getData()
    {
        if( is_null($this->country) ) {
            $this->loadData();
        }

        return $this->country;
    }

    /**
     * load data from xml-files in data-directory
     */
    protected function loadData()
    {
        $this->country = Country::create($this->countryName);

        $dataFiles = $this->scanForXML();

        foreach( $dataFiles as $source ) {
            $this->loadStateFromXML( $source );
        }
    }

    /**
     * Collects xml-files in data-directory
     * @return array
     */
    protected function scanForXML()
    {
        $files = scandir($this->dataFolder.self::STATES_DIR);
        $dataFiles = [];

        foreach( $files as $file ) {
            $tokens = explode('.',$file);
            $ext = array_pop($tokens);

            if( strtolower($ext) == 'xml' ) {
                $dataFiles[] = $file;
            }
        }
        return $dataFiles;
    }

    /**
     * one data-file is one state-entity
     *
     * @param $sourceFile
     * @throws \Exception
     */
    protected function loadStateFromXML( $sourceFile )
    {
        $xmlDocument = new \DOMDocument();

        if( !$xmlDocument->load( $this->dataFolder.self::STATES_DIR.'/'.$sourceFile ) ) {
            throw new \Exception('Cannot load XML source '.$sourceFile);
        }

        /**
         * Validate XML in case of wrong structure
         */
        if( !$xmlDocument->schemaValidate($this->dataFolder.'/schema.xsd') ) {
            throw new \Exception('Invalid XML source '.$sourceFile);
        }

        /**
         * Starting recursive creation of State-collection
         */
        $state = State::createFromXML($xmlDocument->firstChild);
        $this->country->addItem($state);
    }
}