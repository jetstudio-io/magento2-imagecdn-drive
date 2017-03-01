<?php

namespace Macosxvn\ImageCDN\Model\Api\Data;

interface DriveInterface {
    public function getId();
    public function setId();

    public function getLocalPath();
    public function setLocalPath();

    public function getFileId();
    public function setFileId();

    public function getSharedUrl();
    public function setSharedUrl();
}