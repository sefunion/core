<?php

declare(strict_types=1);


namespace Easy\Interfaces\ServiceInterface;

use Easy\Interfaces\ServiceInterface\Resource\BaseResource;
use Easy\Interfaces\ServiceInterface\Resource\FieldValueResource;
use Easy\Interfaces\ServiceInterface\Resource\QueryResource;

interface QueryResourceServiceInterface extends BaseResource, QueryResource, FieldValueResource {}
