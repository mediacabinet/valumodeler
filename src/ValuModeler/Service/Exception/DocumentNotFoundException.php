<?php
namespace ValuModeler\Service\Exception;

use ValuSo\Exception\NotFoundException;

class DocumentNotFoundException extends NotFoundException
{
    protected $code = 12005;
}