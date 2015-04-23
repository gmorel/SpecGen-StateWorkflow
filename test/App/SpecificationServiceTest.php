<?php

namespace Gmorel\SpecGenStateWorkflow\Test\App;

use BookingEngine\Domain\State\Implementation\StateCancelled;
use BookingEngine\Domain\State\Implementation\StateIncomplete;
use BookingEngine\Domain\State\Implementation\StatePaid;
use BookingEngine\Domain\State\Implementation\StateToDelete;
use BookingEngine\Domain\State\Implementation\StateWaitingPayment;
use Gmorel\SpecGenStateWorkflow\App\Command\RenderWorkflowSpecificationFromWorkflowServiceCommand;
use Gmorel\SpecGenStateWorkflow\Domain\WorkflowContainer;
use Gmorel\SpecGenStateWorkflow\Infra\CytoscapeSpecificationRepresentationGenerator;
use Gmorel\SpecGenStateWorkflow\Infra\FileSystemSpecificationWriter;
use Gmorel\StateWorkflowBundle\StateEngine\StateWorkflow;
use Gmorel\SpecGenStateWorkflow\App\SpecificationService as SUT;

/**
 * @author Guillaume MOREL <github.com/gmorel>
 */
class SpecificationServiceTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_introspect_workflow_states()
    {
        // Given
        $stateWorkflow = $this->createValidStateWorkflow();
        $outputFileName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid() . '.html';

        $command = new RenderWorkflowSpecificationFromWorkflowServiceCommand(
            $stateWorkflow->getServiceId(),
            $outputFileName
        );
        $workflowContainer = new WorkflowContainer();
        $workflowContainer->addWorkflow($stateWorkflow);

        $specificationWriter = new FileSystemSpecificationWriter();

        $introspectedWorkflow = new SUT(
            $workflowContainer,
            new CytoscapeSpecificationRepresentationGenerator(),
            $specificationWriter
        );

        $expected = '<!DOCTYPE html>
<html>
    <head>
        <link href="https://rawgit.com/gmorel/StateWorkflowBundle/develop/SpecificationGeneration/UI/Resource/style.css" rel="stylesheet" />
        <meta charset=utf-8 />
        <title>Booking Workflow Specification</title>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="http://cytoscape.github.io/cytoscape.js/api/cytoscape.js-latest/cytoscape.min.js"></script>
        <script src="https://rawgit.com/gmorel/StateWorkflowBundle/develop/SpecificationGeneration/UI/Resource/code.js"></script>
        <script type="application/javascript">
            var dataWorkflow = {"nodes":[{"data":{"id":"incomplete","name":"Incomplete","weight":50,"faveColor":"#F0F1A2","faveShape":"triangle"}},{"data":{"id":"waiting_for_payment","name":"Waiting for payment","weight":50,"faveColor":"#99F6F0","faveShape":"rectangle"}},{"data":{"id":"paid","name":"Paid","weight":50,"faveColor":"#29FF29","faveShape":"ellipse"}},{"data":{"id":"cancelled","name":"Cancelled","weight":50,"faveColor":"#F90FFF","faveShape":"rectangle"}},{"data":{"id":"to_delete","name":"To delete","weight":50,"faveColor":"#617FFF","faveShape":"ellipse"}}],"edges":[{"data":{"source":"incomplete","target":"waiting_for_payment","faveColor":"#F0F1A2","strength":20}},{"data":{"source":"incomplete","target":"paid","faveColor":"#F0F1A2","strength":20}},{"data":{"source":"waiting_for_payment","target":"paid","faveColor":"#99F6F0","strength":20}},{"data":{"source":"waiting_for_payment","target":"cancelled","faveColor":"#99F6F0","strength":20}},{"data":{"source":"cancelled","target":"to_delete","faveColor":"#F90FFF","strength":20}}]};
        </script>
    </head>

    <body>
        <div id="cy"></div>
    </body>
</html>
';

        // When
        $introspectedWorkflow->renderSpecification($command);
        $actual = file_get_contents($outputFileName);

        // Then
        $this->assertEquals($expected, $actual, 'Workflow Specification is not well rendered anymore.');
    }

    /**
     * @return StateWorkflow
     */
    private function createValidStateWorkflow()
    {
        $stateIncomplete = new StateIncomplete();
        $stateWaitingPayment = new StateWaitingPayment();
        $statePaid = new StatePaid();
        $stateCancelled = new StateCancelled();
        $stateToDelete = new StateToDelete();

        $stateWorkflow = new StateWorkflow('Booking Workflow', 'key');
        $stateWorkflow->addAvailableState($stateIncomplete);
        $stateWorkflow->addAvailableState($stateWaitingPayment);
        $stateWorkflow->addAvailableState($statePaid);
        $stateWorkflow->addAvailableState($stateCancelled);
        $stateWorkflow->addAvailableState($stateToDelete);

        $stateWorkflow->setStateAsDefault($stateIncomplete->getKey());

        return $stateWorkflow;
    }
}
