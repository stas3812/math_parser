<?php

namespace Expression;

class Calculator extends AbstractEntity
{
    /**
     * @param SplStack $data
     * @return int|float
     */
    public function process(\SplStack $data)
    {
        $this->_initStacks();

        while($data->count() > 0) {
            $element = $data->shift();

            if(in_array($element, $this->_operators)) {
                $operand2 = $this->_dataStack->pop();
                $this->_dataStack->push(
                    $this->_dataStack->count() > 0
                    ? $this->_binaryOperation($this->_dataStack->pop(), $operand2, $element)
                    : $this->_unaryOperation($operand2, $element)
                );
            } else
                $this->_dataStack->push($element);
        }

        return $this->_dataStack->pop();
    }

    /**
     * @param string $operand
     * @param string $operator
     * @return int|float
     */
    protected function _unaryOperation($operand, $operator)
    {
        eval('$result = ' . $operator . $operand . ';');
        return $result;
    }

    /**
     * @param string $operand1
     * @param string $operand2
     * @param string $operator
     * @return int|float
     * @throws \Exception
     */
    protected function _binaryOperation($operand1, $operand2, $operator)
    {
        if($operator == '^') {
            if($operand1 < 0 && is_float($operand2))
                throw new \Exception('Incorrect expression. Can`t calculate: ' . $operand1 . '^' . $operand2);

            $str = 'pow(' . $operand1 .', ' . $operand2 . ')';

        } else {
            $str = $operand1 . $operator . $operand2;
        }

        eval('$result = ' . $str . ';');

        return $result;
    }
}
