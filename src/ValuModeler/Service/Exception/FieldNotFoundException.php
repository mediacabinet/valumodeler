<?php
namespace ValuModeler\Service\Exception;

use ValuSo\Exception\NotFoundException;

class FieldNotFoundException extends NotFoundException
{
    protected $code = 12006;
}