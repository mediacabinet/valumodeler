<?php
namespace ValuModeler\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Reference extends AbstractAssociation
{}
