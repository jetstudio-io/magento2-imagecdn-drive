<?php
namespace Macosxvn\ImageCDN\Model\Image\Storage;


abstract class StorageAbstract {

    /**
     * Mapping model
     * @var string
     */
    protected $_mappingModel = "";

    /**
     * Store a file to cloud storage
     */
    public function storeFile() {}

    /**
     * Get shared url of a file on cloud storage
     * @param $fileId
     */
    public function getShareUrl($fileId) {}
}