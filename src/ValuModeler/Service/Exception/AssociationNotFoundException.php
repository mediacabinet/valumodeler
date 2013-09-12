<?php
namespace ValuModeler\Service\Exception;

use ValuSo\Exception\NotFoundException;

class AssociationNotFoundException extends NotFoundException
{
    protected $code = 12002;
}