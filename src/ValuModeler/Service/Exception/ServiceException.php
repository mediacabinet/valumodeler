<?php
namespace ValuModeler\Service\Exception;

use \ValuSo\Exception\ServiceException as BaseException;

class ServiceException extends BaseException
{
    protected $code = 12001;
}