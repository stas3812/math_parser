<?php

namespace Expression;

class Parser extends AbstractEntity
{
    protected $_priorities;
    protected $_operatorsStack;

    /**
     * @param array $priorities
     */
    public function __construct($priorities)
    {
        parent::__construct(array_keys($priorities));
        $this->_priorities = $priorities;
    }

    public function __destruct()
    {
        unset($this->_priorities, $this->_operatorsStack);
    }

    protected function _initStacks()
    {
        parent::_initStacks();
        $this->_operatorsStack = new \SplStack();
    }

    /**
     * @param string $expression
     * @return string
     */
    protected function _filter($expression)
    {
        $expression = preg_replace('~\s+~', '', $expression);
        $expression = preg_replace('~,~', '.', $expression);
        return $expression;
    }

    /**
     * @param string $expression
     * @return SplStack
     */
    public function process($expression)
    {
        $this->_initStacks();

        $expression = $this->_filter($expression);

        $waitNewOperand = true;
        for($i=0, $limit = strlen($expression); $i<$limit; $i++) {
            if(!in_array($expression[$i], $this->_operators)) {
                $this->_parseOperand($expression[$i], $waitNewOperand);
                $waitNewOperand = false;
            } else {
                $waitNewOperand = true;
                if($this->_priorities[$expression[$i]] == 0)
                    $this->_operatorsStack->push($operator);
                else if($this->_priorities[$expression[$i]] == 1)
                    $this->_parseBrackets();
                else
                    $this->_parseOperator($expression[$i]);
            }
        }

        return $this->_finishParsing();
    }

    /**
     * @param string $operand
     * @param boolean $newOperand
     */
    protected function _parseOperand($operand, $newOperand)
    {
        if(!$newOperand)
            $this->_dataStack->push($this->_dataStack->pop() . $operand);
        else
            $this->_dataStack->push($operand);
    }

    /**
     * @throws \Exception
     */
    protected function _parseBrackets()
    {
        if(!$this->_operatorsStack->count())
            throw new \Exception('Incorrect expression. Unpaired brackets');

        while($this->_operatorsStack->count()) {
            $stackOperator = $this->_operatorsStack->pop();

            if($this->_priorities[$stackOperator] == 0)
                return;

            $this->_dataStack->push($stackOperator);
        }
    }

    /**
     * @param string $operator
     */
    protected function _parseOperator($operator)
    {
        if($this->_operatorsStack->count() > 0) {
            while($this->_operatorsStack->count()) {
                $stackOperator = $this->_operatorsStack->pop();

                if($this->_priorities[$stackOperator] < $this->_priorities[$operator]) {
                    $this->_operatorsStack->push($stackOperator);
                    $this->_operatorsStack->push($operator);
                    break;
                }

                $this->_dataStack->push($stackOperator);
            }
        }

        if($this->_operatorsStack->count() == 0)
                $this->_operatorsStack->push($operator);
    }

    /**
     * @return SplStack
     * @throws \Exception
     */
    protected function _finishParsing()
    {
        while($this->_operatorsStack->count() > 0) {
            $stackOperator = $this->_operatorsStack->pop();
            if($this->_priorities[$stackOperator] <= 1)
                throw new \Exception('Incorrect expression. Unpaired brackets');

            $this->_dataStack->push($stackOperator);
        }

        return $this->_dataStack;
    }
}
