<?php

namespace vendor\i4h\phpmip;


class MIPconstraint {

	const TYPE_DEFAULT = 1;		
	const TYPE_GE = 2;
	const TYPE_LE = 3;
	const TYPE_EQ = 4;	
		
	public $type;
	public $rhs = null;
	public $lhs = null;
	public $vars = null;
	
    public function __construct($type, $vars, $sideA = null, $sideB = null)
    {
    	$this->type = $type;
    	$this->vars = $vars;
    	switch($this->type) {
    		case self::TYPE_DEFAULT:
    			$this->lhs = $sideA;
    			$this->rhs = $sideB;
    			break;
    		case self::TYPE_GE:
    			$this->lhs = $sideA;
    			$this->rhs = 'inf';
    			break;
    		case self::TYPE_LE:
    			$this->lhs = '-inf';
    			$this->rhs = $sideA;
    			break;
    		case self::TYPE_EQ:
    			$this->lhs = $sideA;
    			$this->rhs = $sideA;    			
    			break;
    		default:
    			throw new \Exception("Unknonw MIPconstraint type ".$type);
    	}
    }

    public function __toString() 
    {
    	$vars = [];
    	$lineLength=10;
    	foreach($this->vars as $var) {
    		$lineLength+=strlen($var['coefficient']." ".$var['name'].(isset($var['setIdx']) ? "(".$var['setIdx'].")" : ""));
    		if ($lineLength < 420){
    		$vars[] = $var['coefficient']." ".$var['name'].(isset($var['setIdx']) ? "(".$var['setIdx'].")" : "");
    		}
    		else{
    			$lineLength=0;
    			$vars[] = $var['coefficient']." ".$var['name'].(isset($var['setIdx']) ? "(".$var['setIdx'].")" : "")."\n";
    		}
    	}
    	$varsString = implode(" + ", $vars);
    	
    	switch($this->type) {
    		case self::TYPE_DEFAULT:
    			return $varsString." >= ".$this->lhs."\n".$varsString." <= ".$this->rhs;
    		case self::TYPE_GE:
    			return $varsString.' >= '.$this->lhs;
    		case self::TYPE_LE:
    			return $varsString." <= ".$this->rhs;
    		case self::TYPE_EQ:
    			return $varsString." = ".$this->lhs;
    	}    	    	
    	
    }

	
}