<?php

namespace vendor\i4h\phpmip;

class MIPvariable extends Object

{
    /**
     * @var array list of virtual properties
     */
    public $magic = ['typeString', 'boundsString'];

	const TYPE_CONTINUOUS = 1;		
	const TYPE_INTEGER = 2;
	const TYPE_BINARY = 3;	
	
	public $type;
	public $lb = null;
	public $ub = null;
	public $level = null;
	
	public static $typeStrings = [
			self::TYPE_CONTINUOUS => 'cnt',
			self::TYPE_INTEGER => 'int',
			self::TYPE_BINARY => 'bin',
	];
	
	public function getTypeString()
    {
		return MIPvariable::$typeStrings[$this->type];
	}
	
    public function __construct($type)
    {
    	$this->type = $type;
    	switch($this->type) {
    		case self::TYPE_BINARY:
    			$this->lb = 0;
    			$this->ub = 1;
    			break;
    		case self::TYPE_INTEGER:
    			$this->lb = 0;
    			break;
    		case self::TYPE_CONTINUOUS:
    			break;
    		default:
    			throw new \Exception("Unknonw MIPvariable type ".$type);
    	}
    }
    
    public function setLB($value)
    {
    	$this->lb = $value;
    }
    
    public function setUB($value)
    {
    	$this->ub = $value;
    }

	public function getBoundsString()
    {
		if ($this->lb === null && $this->ub === null)
			return "[-inf,inf]";
		else if ($this->lb === null)
			return "[-inf,".$this->ub."]";
		else if ($this->ub === null) 
			return "[".$this->lb.",inf]";
		else 
			return "[".$this->lb.",".$this->ub."]";						
	}   
}