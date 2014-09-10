<?php

spl_autoload_register(function ($class) {
    $filename = './' . str_replace('\\', DIRECTORY_SEPARATOR, $class . '.php');
    if(file_exists($filename))
        include_once $filename;
});

$priorities = array(
    '(' => 0, ')' => 1,
    '+' => 2, '-' => 2,
    '*' => 3, '/' => 3,
    '^' => 4
);

$input1 = '(-30,32 + 4.3 * 2 + 78/6 +7 / (1 - 5) ^ 3';
$input2 = '3 + 4 * 2 / (1 - 5)^2';

try {
    $parser = new \Expression\Parser($priorities);
    $calculator = new \Expression\Calculator(array_keys($priorities));

    printf(
        "%s = %s\n%s = %s\n",
        $input1, $calculator->process($parser->process($input1)),
        $input2, $calculator->process($parser->process($input2))
    );
} catch(Exception $e) {
    printf("%s = \n%s = \nError: %s", $input1, $input2, $e->getMessage());
}
