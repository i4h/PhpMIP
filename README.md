# PhpMIP

PhpMIP is a PHP class representing 
a Linear Mixed-Integer Programm. 

It comes with a writer to the IBM CPLEX LP file format
and a base writer and Interface that should facilitate adding
your own writer classes.

# Installation

The package can be installed into your project via composer:

````
 $ php composer.phar require i4h/php-mip "*"
````
 
# Usage
 
 See the examples directory for two examples MIPs.
  
 The workflow follows that of common modeling languages:
 - Create sets
 - Create variables
 - Create constraints
 - Define the objective function
 
This is the example from `example/trivial.php`:
 
````
$mip = new \vendor\i4h\PhpMIP\MIP();
 
/* Variable x */
$mip->addVariable("x", \vendor\i4h\PhpMIP\MIPvariable::TYPE_CONTINUOUS);
$mip->setVariableLB("x", 0);
  
/* Variable y */
$mip->addVariable("y", \vendor\i4h\PhpMIP\MIPvariable::TYPE_CONTINUOUS);
$mip->setVariableLB("y", 0);
  
/* Objective (default coefficient is 1) */
$mip->addObjectiveCoefficient("x");
$mip->addObjectiveCoefficient("y");
  
/* Constraint x + y <= 1 */
$vars = [
    ['name'=>'x', 'coefficient'=>1],
    ['name'=>'y', 'coefficient'=>1],
];
$mip->addConstraintLE($vars, 1);
  
/* Use Lp writer to write to a file */
$writer = new \vendor\i4h\PhpMIP\LpWriter();
$writer->setOutFile("/path/to/outfile.lp");
$writer->write(create);
````
 
The code above will create an lp file with the following content:

````
Minimize
obj: 1 x + 1 y 
Subject To
1 x + 1 y <= 1
Bounds
end
````
 
 
