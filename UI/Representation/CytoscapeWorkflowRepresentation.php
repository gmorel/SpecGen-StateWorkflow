<?php

namespace SpecGen\StateWorkflowSpecGenBundle\UI\Representation;

use SpecGen\StateWorkflowSpecGenBundle\Domain\IntrospectedState;
use SpecGen\StateWorkflowSpecGenBundle\Domain\IntrospectedTransition;
use SpecGen\StateWorkflowSpecGenBundle\Domain\IntrospectedWorkflow;
use SpecGen\StateWorkflowSpecGenBundle\Domain\Representation\WorkflowRepresentationInterface;

/**
 * @author Guillaume MOREL <github.com/gmorel>
 */
class CytoscapeWorkflowRepresentation implements WorkflowRepresentationInterface
{
    const KEY_STATE = 'nodes';
    const KEY_TRANSITION = 'edges';

    const DEFAULT_STATE_WEIGHT = 50;
    const DEFAULT_TRANSITION_STRENGTH = 20;

    const STATE_SHAPE_ROOT = 'triangle';
    const STATE_SHAPE_NORMAL = 'rectangle';
    const STATE_SHAPE_LEAF = 'ellipse';

    /** @var string */
    private $workflowName;

    /** @var array */
    private $jsonableStates;

    /** @var array */
    private $jsonableTransitions;

    public function __construct(IntrospectedWorkflow $instrospectedWorkflow)
    {
        $this->workflowName = $instrospectedWorkflow->getWorkflowName();
        $colors = $this->assignUniqueColorToStates(
            $instrospectedWorkflow->getIntrospectedStates()
        );

        $this->jsonableStates = $this->createStatesRepresentation(
            $instrospectedWorkflow->getIntrospectedStates(),
            $colors
        );

        $this->jsonableTransitions = $this->createTransitionsRepresentation(
            $instrospectedWorkflow->getIntrospectedTransitions(),
            $colors
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    /**
     * {@inheritdoc}
     * @return string JSON
     */
    public function serialize()
    {
        return json_encode(
            array(
                self::KEY_STATE => $this->jsonableStates,
                self::KEY_TRANSITION => $this->jsonableTransitions
            )
        );
    }

    /**
     * @param IntrospectedState[] $introspectedStates
     * @param string[]            $colors
     *
     * @return array
     */
    private function createStatesRepresentation(array $introspectedStates, array $colors)
    {
        $jsonableStates = array();
        foreach ($introspectedStates as $introspectedState) {
            $jsonableStates[] = array(
                'data' => array(
                    'id' => $introspectedState->getKey(),
                    'name' => $introspectedState->getName(),
                    'weight' => self::DEFAULT_STATE_WEIGHT,
                    'faveColor' => $colors[$introspectedState->getKey()],
                    'faveShape' => $this->getStateShape($introspectedState)
                )
            );
        }

        return $jsonableStates;
    }

    /**
     * @param IntrospectedTransition[] $introspectedTransitions
     * @param string[] $colors
     *
     * @return array
     */
    private function createTransitionsRepresentation(array $introspectedTransitions, array $colors)
    {
        $jsonableTransitions = array();
        foreach ($introspectedTransitions as $introspectedTransition) {
            $jsonableTransitions[] = array(
                'data' => array(
                    'source' => $introspectedTransition->getFromIntrospectedState()->getKey(),
                    'target' => $introspectedTransition->getToIntrospectedState()->getKey(),
                    'faveColor' => $colors[$introspectedTransition->getFromIntrospectedState()->getKey()],
                    'strength' => self::DEFAULT_TRANSITION_STRENGTH
                )
            );
        }

        return $jsonableTransitions;
    }

    /**
     * @param IntrospectedState[] $introspectedStates
     *
     * @return string[]
     */
    private function assignUniqueColorToStates(array $introspectedStates)
    {
        $colors = array();
        foreach ($introspectedStates as $introspectedState) {
            $colors[$introspectedState->getKey()] = $this->createUniqueColor($introspectedState->getKey());
        }

        return $colors;
    }

    /**
     * @param IntrospectedState $introspectedState
     *
     * @return string
     */
    private function getStateShape(IntrospectedState $introspectedState)
    {
        if ($introspectedState->isRoot()) {
            return self::STATE_SHAPE_ROOT;
        }

        if ($introspectedState->isLeaf()) {
            return self::STATE_SHAPE_LEAF;
        }

        return self::STATE_SHAPE_NORMAL;
    }

    /**
     * @param int $number
     *
     * @return string
     */
    private function getColorChar($number)
    {
        $modulo = $number % 22;

        if ($modulo < 10) {
            return strrev((string) $modulo);
        }

        $numberMap = array(
            10 => 'A',
            11 => 'B',
            12 => 'C',
            13 => 'D',
            14 => 'E'
        );

        if (isset($numberMap[$number])) {
            return strrev($numberMap[$number]);
        }

        $moduloMap = array(
            21 => 'A',
            20 => 'B',
            19 => 'C',
            18 => 'D',
            17 => 'E'
        );

        if (isset($moduloMap[$modulo])) {
            return strrev($moduloMap[$modulo]);
        }

        return strrev('F');
    }

    /**
     * @param string $initializationVector
     *
     * @return string #FF0000
     * @credits https://github.com/baykall/Php-Unique-HTML-Color-Generator-From-String/blob/master/color.php
     */
    private function createUniqueColor($initializationVector)
    {
        if(empty($initializationVector)) {
            return '#FF0000';
        }

        $length = strlen($initializationVector);
        $color = '';
        for ($i = 1; $i <= 6; $i++) {

            $charNumber = ($i - 1) % $length;
            $color = $color . $this->getColorChar(ord($initializationVector{$charNumber}));
        }

        return '#' . $color;
    }
}
