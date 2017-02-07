<?php
namespace vendor\i4h\phpmip;

/**
 * This class represents a mixed-integer program
 * 
 * @author Ingmar Vierhaus <mail@ingmar-vierhaus.de>
 * @since 2016/09/21
 *
 */

function echon($msg) {
	echo $msg."\n";
}

//@todo: Remove YII dependency
class MIP extends Object
{

    /**
     * @var array list of virtual properties
     */
    public $magic = ['sets', 'variables', 'constraints', 'objectiveCoefficients'];

    /**
     * @var array sets of the MIP
     */
	private $_sets = [];

    /**
     * @var array variables of the MIP
     */
	private $_variables = [];


    /**
     * @var array constraints of the MIP
     */
	private $_constraints = [];

    /**
     * @var array objective function coefficients
     */
	private $_objectiveCoefficients = [];

	/**
     * @var constant value used for big m constraints
     */
	const bigM=100000;

    /**
     * @var bool should bounds be shown in __toString() output
     */
	public $boundsInToString = true;

    /**
     * Add a set to the mip
     * @param $name name of the set
     * @param $values array of values of the set
     * @throws \Exception if set already exists
     */
	public function addSet($name, Array $values) {
		if (in_array($name,$this->_sets)) {
			//@todo: more specialized exception for this 
			throw new \Exception("set ".$name." already exists in mip");
		}
		$this->_sets[$name] = $values;
	}

    /**
     * Add a variable to the MIP
     *
     * @param $name Variable name
     * @param $type Variable type (see MIPvariable for valid types)
     * @param null $set If given, the new variable will have one
     * value for each set element
     * @throws \Exception If variable already exists or type is not recognized
     */
	public function addVariable($name, $type, $set = null) {
		if (in_array($name,$this->_variables))
				throw new \Exception("variable ".$name." already exists in mip");

		if ($set === null) {
			$this->_variables[$name] = new MIPvariable($type);
		} else {
			if (!isset($this->_sets[$set]))
				throw new \Exception("Set ".$set." not known to mip");
			
			foreach ($this->_sets[$set] as $setIdx=>$setValue) {
				$this->_variables[$name][$setValue] = new MIPvariable($type);
			}
		}
	}

    /**
     *
     * Add a variable to the MIP if a variable
     * with given name does not yet exist
     *
     * @param $name Variable name
     * @param $type Variable type (see MIPvariable for valid types)
     * @param null $set If given, the new variable will have one
     * value for each set element
     */
	public function addVariableIfNew($name, $type, $set = null) {
		if (in_array($name,$this->_variables)) {
			return;
		}
		$this->addVariable($name, $type, $set);
	}

    /**
     * Set the lower bound of a variable
     *
     * @param $name Variable name
     * @param $bound value of the variable bound
     * @param null $setIdx If the variable is defined on a set, the
     * corresponding set index must be given
     */
	public function setVariableLB($name, $bound, $setIdx = null) {
		$this->checkVars([['name'=>$name, 'setIdx'=>$setIdx]]);
		if ($setIdx === null)
			$var = $this->_variables[$name];
		else
			$var = $this->_variables[$name][$setIdx];
		$var->setLB($bound);
	}


    /**
     * Set the upper bound of a variable
     *
     * @param $name Variable name
     * @param $bound value of the variable bound
     * @param null $setIdx If the variable is defined on a set, the
     * corresponding set index must be given
     */

	public function setVariableUB($name, $bound, $setIdx = null) {
		$this->checkVars([['name'=>$name, 'setIdx'=>$setIdx]]);
		if ($setIdx === null)
			$var = $this->_variables[$name];
		else
			$var = $this->_variables[$name][$setIdx];
		$var->setUB($bound);
	}	

	/**
	 * adds constraint of type $lhs <= $vars <= $rhs
	 * @param $vars the variables of the constraint with their coefficients
	 * @param $lhs value
	 * @param $rhs value
	 */
	public function addConstraint(Array $vars, $lhs, $rhs) {
		$this->checkVars($vars);
		$this->_constraints[] = new MIPconstraint(MIPconstraint::TYPE_DEFAULT, $vars, $lhs, $rhs);
	}
	
	/**
	 * adds constraint of type $bound <= $vars <= inf
     * @param $vars the variables of the constraint with their coefficients
	 * @param $bound number
	 */
	public function addConstraintGE(Array $vars, $bound) {
		$this->checkVars($vars);
		$this->_constraints[] = new MIPconstraint(MIPconstraint::TYPE_GE, $vars, $bound);
	}
	
	/**
	 * adds constraint of type -inf <= $vars <= rhs
     * @param $vars the variables of the constraint with their coefficients
	 * @param $bound number
	 */
	public function addConstraintLE($vars, $bound) {
		$this->checkVars($vars);
		$this->_constraints[] = new MIPconstraint(MIPconstraint::TYPE_LE, $vars, $bound);
	}

	/**
	 * adds constraint of type $value <= $vars <= $value
     * @param $vars the variables of the constraint with their coefficients
	 * @param $value number
	 */
	public function addConstraintEQ($vars, $value) {
        $this->checkVars($vars);
		$this->_constraints[] = new MIPconstraint(MIPconstraint::TYPE_EQ, $vars, $value);
	}

    /**
     * Set the objective Coefficient for a variable
     * @param $var variable name
     * @param null $setValue set index
     * @param int $coefficient objective function coefficient value
     */
	public function addObjectiveCoefficient($var, $setValue = null, $coefficient = 1) 
	{
		$this->checkVars([['name'=>$var, 'setIdx'=>$setValue]]);
		if ($setValue === null)
			$this->_objectiveCoefficients[$var] = $coefficient;
		else 
			$this->_objectiveCoefficients[$var][$setValue] = $coefficient;			
	}

    /**
     * Returns a human readable string representation of this MIP
     *
     * @return string
     */
	public function __toString() {
		ob_start();
		echon("BEGIN MIP:");
		echon("Sets:");
		foreach($this->_sets as $name=>$values) {
			echon($name.": ".implode(", ",$values));
		}
		echon("");
		
		echon("Variables:");
		$vars = [];
		foreach($this->_variables as $name=>$arr) {
			if (!is_array($arr)) {
				$var = $arr;
				$vars[] = $var->getTypeString().": ".$name."".($this->boundsInToString ? ":".$var->boundsString : "");
			} else {
				$varList = [];
				foreach($arr as $setValue=>$var)
					$varList[] = $name."(".$setValue.")".($this->boundsInToString ? ":".$var->boundsString : "");
				$vars[] = $var->getTypeString().": ".implode(", ",$varList);
			} 
		}
		echon(implode("\n",$vars));
		echon("");
		
		echon("Constraints:");
		foreach($this->_constraints as $cons)
			echon($cons); 
		echon("");

		echon("Objective:");
		$objectiveParts = [];
		foreach($this->_objectiveCoefficients as $var=>$arr) {
			if (is_array($arr)) {
				foreach($arr as $setValue=>$coefficient)
					$objectiveParts[] = $coefficient." ".$var."(".$setValue.")";
			} else
				$objectiveParts[] = $arr." ".$var;				

			
		}
		echon("minimize ".implode(" + ",$objectiveParts));
		echon("");
		echon("END MIP");
		
		$str = ob_get_contents();
		ob_clean();

		return $str;
	}

    /**
     * Returns the sets of the MIP
     *
     * @return array
     */
	public function getSets() {
		return $this->_sets;
	}

    /**
     * Returns the variables of the MIP
     *
     * @return array
     */
	public function getVariables() {
		return $this->_variables;
	}

    /**
     * Returns the constraints of the MIP
     *
     * @return array
     */
	public function getConstraints() {
		return $this->_constraints;
	}

    /**
     * Returns the non-zero objective coefficients of the MIP
     *
     * @return array
     */
	public function getObjectiveCoefficients() {
		return $this->_objectiveCoefficients;
	}

	/**
	 * Checks the existence of sets with the respective names
     *
	 * @param $setArray array with set names to be checked
     * @throws \Exception if a set does not exist in the MIP
     * @return true
	 */
	public function checkSets($setArray) {
		foreach ($setArray as $name){
			if (!isset($this->_sets[$name])){
				throw new \Exception("Set ".$name." is not set.");
			}
		}
		return true;
	}

	/** Check that vars exist
	 *
	 * Checks that the variables described in the vars vector
	 * were previously added to the problem
	 *
	 * @param array $vars
	 * @throws \Exception
	 */
	public function checkVars(array $vars) {
		foreach($vars as $var) {
			/* Var array should exist */
			if (!isset($this->_variables[$var['name']]))
				throw new \Exception("Variable ".$var['name']." does not exist in MIP.");

			/* Check if var is on set */
			if (isset($var['setIdx'])) {
				if (!is_array($this->_variables[$var['name']]) || !isset($this->_variables[$var['name']][$var['setIdx']]))
 					throw new \Exception("Variable ".$var['name']." does not exist in MIP.");
			} else { /* Check if var is not on set */
				if (is_array($this->_variables[$var['name']]))
				    throw new \Exception("Set index ".$var['setIdx']." is not defined in variable set ".$var['name']);
			}
		}
	}
}