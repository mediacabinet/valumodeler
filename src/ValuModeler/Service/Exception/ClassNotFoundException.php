<?php
namespace ValuModeler\Service\Exception;

use ValuSo\Exception\NotFoundException;

class ClassNotFoundException extends NotFoundException
{
    protected $code = 12003;
}