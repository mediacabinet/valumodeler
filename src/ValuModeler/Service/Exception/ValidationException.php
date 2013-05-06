<?php
namespace ValuModeler\Service\Exception;

use ValuSo\Exception\ValidationException as BaseException;

class ValidationException extends BaseException
{
    protected $code = 12008;
}