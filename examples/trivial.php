<?php
/**
 * Formulate the following trivial LP using phpmip:
 *
 *  minimize    \f$x + y\f$
 *  subject to  \f$ x + y \le 1  \f$       (1)
 *              \f$ x \ge 0, y \ge 0 \f$
 *              \f$ x,y \in \mathcal{R} \f$
 */

require_once('bootstrap.php');

$mip = new \vendor\i4h\phpmip\MIP();

/* Variable x */
$mip->addVariable("x", \vendor\i4h\phpmip\MIPvariable::TYPE_CONTINUOUS);
$mip->setVariableLB("x", 0);

/* Variable y */
$mip->addVariable("y", \vendor\i4h\phpmip\MIPvariable::TYPE_CONTINUOUS);
$mip->setVariableLB("y", 0);

/* Objective */
$mip->addObjectiveCoefficient("x");
$mip->addObjectiveCoefficient("y");

/* Constraint x + y <= 1 */
$vars = [
    ['name'=>'x', 'coefficient'=>1],
    ['name'=>'y', 'coefficient'=>1],
];
$mip->addConstraintLE($vars, 1);


/* Output in human readable format */
$mip->boundsInToString = false;
echo "String representation of MIP:\n";
echo "--------------------------------\n";
echo $mip;

/* Output in LP format */
$writer = new \vendor\i4h\phpmip\LpWriter();
$writer->write($mip);
echo "\n";
echo "\n";
echo "LP Format:\n";
echo "--------------------------------\n";
echo $writer;

