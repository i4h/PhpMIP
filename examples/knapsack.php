<?php
/**
 * Formulate a knapsack problem using PhpMIP
  */

require_once('bootstrap.php');

$mip = new \vendor\i4h\PhpMIP\MIP();

/* Create the set of items */
$indexArray = range(1,10);

/* Random weights */
$w = [];
foreach($indexArray as $idx)
    $w[$idx] = rand(1,10);

/* Random values */
$v = [];
foreach($indexArray as $idx)
    $v[$idx] = rand(1,10);

/* Random maximum weight */
$W = rand(5,10);

/* Add set i */
$mip->addSet('i', $indexArray);

/* Variable x */
$mip->addVariable("x", \vendor\i4h\PhpMIP\MIPvariable::TYPE_BINARY, 'i');

/* Objective */
foreach ($indexArray as $idx) {
    $mip->addObjectiveCoefficient("x", $idx, -1 * $v[$idx]);
}

/* Constraint */
$vars = [];
foreach ($indexArray as $idx) {
    $vars[] = ['name'=>'x', 'setIdx'=>$idx, 'coefficient'=>$w[$idx]];
}
$mip->addConstraintLE($vars, $W);

/* Output in human readable format */
$mip->boundsInToString = false;
echo "String representation of MIP:\n";
echo "--------------------------------\n";
echo $mip;

/* Output in LP format */
$writer = new \vendor\i4h\PhpMIP\LpWriter();
$writer->write($mip);
echo "\n";
echo "\n";
echo "LP Format:\n";
echo "--------------------------------\n";
echo $writer;

