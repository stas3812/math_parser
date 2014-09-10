<?php

namespace Expression;

class AbstractEntity
{
    protected $_operators;
    protected $_dataStack;

    /**
     * @param array $operators
     */
    public function __construct($operators)
    {
        $this->_operators = $operators;
    }

    public function __destruct()
    {
        unset($this->_operators, $this->_dataStack);
    }

    protected function _initStacks()
    {
        $this->_dataStack = new \SplStack();
    }
}
