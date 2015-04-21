<?php

namespace Gmorel\SpecGenStateWorkflow\Domain;

use Gmorel\StateWorkflowBundle\StateEngine\StateWorkflow;
use Gmorel\SpecGenStateWorkflow\UI\Representation\HtmlSpecificationRepresentation;

/**
 * @author Guillaume MOREL <guillaume.morel@verylastroom.com>
 */
interface SpecificationRepresentationGeneratorInterface
{
    /**
     * @param StateWorkflow $stateWorkflow
     * @return HtmlSpecificationRepresentation
     */
    public function createSpecification(StateWorkflow $stateWorkflow);
}
