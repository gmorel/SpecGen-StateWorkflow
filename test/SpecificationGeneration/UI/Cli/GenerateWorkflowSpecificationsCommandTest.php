<?php

namespace Gmorel\SpecGenStateWorkflowBundle\Test\SpecificationGeneration\UI\Cli;

use Gmorel\SpecGenStateWorkflowBundle\SpecificationGeneration\UI\Cli\GenerateWorkflowSpecificationsCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Guillaume MOREL <guillaume.morel@verylastroom.com>
 */
class GenerateWorkflowSpecificationsCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $mockSpecificationService = $this->mockSpecificationService();

        $application = new Application();
        $application->add(new GenerateWorkflowSpecificationsCommand($mockSpecificationService));

        $command = $application->find('gmorel:state-engine:generate-workflow-specifications');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));
    }

    /**
     * @return \Gmorel\SpecGenStateWorkflowBundle\SpecificationGeneration\App\SpecificationService
     */
    private function mockSpecificationService()
    {
        $mock = $this->getMockBuilder('Gmorel\SpecGenStateWorkflowBundle\SpecificationGeneration\App\SpecificationService')
            ->disableOriginalConstructor()
            ->getMock();
        $mock->method('renderSpecification')
            ->willReturn(null);

        return $mock;
    }
}