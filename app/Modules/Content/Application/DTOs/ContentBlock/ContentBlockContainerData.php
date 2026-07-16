<?php

namespace App\Modules\Content\Application\DTOs\ContentBlock;

class ContentBlockContainerData
{
    public function __construct(
        public int $containerId,
        public string $containerType,
    )
    {

    }
}
