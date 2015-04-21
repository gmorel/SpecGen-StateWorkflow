<?php

namespace Gmorel\SpecGenStateWorkflow\Domain;

use Gmorel\SpecGenStateWorkflow\Domain\Representation\SpecificationRepresentationInterface;
use SpecificationGeneration\Domain\Exception\UnableToWriteSpecificationException;

/**
 * @author Guillaume MOREL <guillaume.morel@verylastroom.com>
 */
interface SpecificationWriterInterface
{
    /**
     * Write specification on a target
     * @param SpecificationRepresentationInterface $specificationRepresentation
     * @param string                               $target
     *
     * @throws UnableToWriteSpecificationException
     */
    public function write(SpecificationRepresentationInterface $specificationRepresentation, $target);
}
