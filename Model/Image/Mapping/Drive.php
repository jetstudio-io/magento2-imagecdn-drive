<?php

namespace Macosxvn\ImageCDN\Model\Image\Mapping;

use \Macosxvn\ImageCDN\Model\Api\Data\DriveInterface;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;

class Drive extends AbstractModel implements DriveInterface, IdentityInterface {

    const CACHE_TAG = "macosxvn_imagecdn_drive";

    protected $_cacheTag = self::CACHE_TAG;


    public function getLocalPath() {
        // TODO: Implement getLocalPath() method.
    }

    public function setLocalPath() {
        // TODO: Implement setLocalPath() method.
    }

    public function getFileId() {
        // TODO: Implement getFileId() method.
    }

    public function setFileId() {
        // TODO: Implement setFileId() method.
    }

    public function getSharedUrl() {
        // TODO: Implement getSharedUrl() method.
    }

    public function setSharedUrl() {
        // TODO: Implement setSharedUrl() method.
    }

    public function getIdentities() {
        // TODO: Implement getIdentities() method.
    }
}