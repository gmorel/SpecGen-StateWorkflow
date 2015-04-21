<?php

namespace Gmorel\SpecGenStateWorkflow\Test\UI\Representation;

use BookingEngine\Domain\State\Implementation\StateCancelled;
use BookingEngine\Domain\State\Implementation\StateIncomplete;
use BookingEngine\Domain\State\Implementation\StatePaid;
use BookingEngine\Domain\State\Implementation\StateToDelete;
use BookingEngine\Domain\State\Implementation\StateWaitingPayment;
use Gmorel\SpecGenStateWorkflow\Domain\IntrospectedWorkflow;
use Gmorel\StateWorkflowBundle\StateEngine\StateWorkflow;
use Gmorel\SpecGenStateWorkflow\UI\Representation\CytoscapeWorkflowRepresentation as SUT;

/**
 * @author Guillaume MOREL <github.com/gmorel>
 */
class CytoscapeWorkflowRepresentationTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_represent_itself_in_json()
    {
        // Given
        $stateWorkflow = $this->createValidStateWorkflow();
        $introspectedWorkflow = new IntrospectedWorkflow($stateWorkflow);

        $expected = '{"nodes":[{"data":{"id":"incomplete","name":"Incomplete","weight":50,"faveColor":"#F0F1A2","faveShape":"triangle"}},{"data":{"id":"waiting_for_payment","name":"Waiting for payment","weight":50,"faveColor":"#99F6F0","faveShape":"rectangle"}},{"data":{"id":"paid","name":"Paid","weight":50,"faveColor":"#29FF29","faveShape":"ellipse"}},{"data":{"id":"cancelled","name":"Cancelled","weight":50,"faveColor":"#F90FFF","faveShape":"rectangle"}},{"data":{"id":"to_delete","name":"To delete","weight":50,"faveColor":"#617FFF","faveShape":"ellipse"}}],"edges":[{"data":{"source":"incomplete","target":"waiting_for_payment","faveColor":"#F0F1A2","strength":20}},{"data":{"source":"incomplete","target":"paid","faveColor":"#F0F1A2","strength":20}},{"data":{"source":"waiting_for_payment","target":"paid","faveColor":"#99F6F0","strength":20}},{"data":{"source":"waiting_for_payment","target":"cancelled","faveColor":"#99F6F0","strength":20}},{"data":{"source":"cancelled","target":"to_delete","faveColor":"#F90FFF","strength":20}}]}';

        // When
        $representation = new SUT($introspectedWorkflow);
        $actual = $representation->serialize();

        // Then
        $this->assertEquals($expected, $actual, 'State Workflow are not well represented in JSON Cytoscape anymore.');
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
