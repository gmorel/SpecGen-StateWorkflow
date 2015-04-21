<?php

namespace Gmorel\SpecGenStateWorkflowBundle\SpecificationGeneration\Domain;

use Gmorel\SpecGenStateWorkflowBundle\SpecificationGeneration\Domain\Representation\SpecificationRepresentationInterface;
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
