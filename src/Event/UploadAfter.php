<?php

declare(strict_types=1);


namespace Easy\Event;

class UploadAfter
{
    public array $fileInfo;

    public function __construct(array $fileInfo)
    {
        $this->fileInfo = $fileInfo;
    }
}
