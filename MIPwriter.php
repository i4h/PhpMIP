<?php

namespace vendor\i4h\phpmip;

/**
 * This is the base class for MIP writers
 * 
 * @author Ingmar Vierhaus <mail@ingmar-vierhaus.de>
 * @since 2016/09/03
 *
 */
abstract class MIPwriter
{

    protected $outFilePath = null;
    protected $outFile = null;
    protected $mipString = "";
	
	public function getExtension() {
		return $this->extension;
	}
	
	public function setOutFile($filePath) {
		$this->outFilePath = $filePath;
	}

	protected function openFile() {
		if ($this->outFilePath !== null)
            $this->outFile = fopen($this->outFilePath,"w");
		else
		    $this->mipString = "";
	}

	public function __toString() {
        return $this->mipString;
    }

	protected function closeFile() {
	    if ($this->outFile !== null)
            fclose($this->outFile);
	}

	/**
	 * Adds the string in $line to the outputfile and adds a "\n" statement at the and for a linebreak.
	 * @param string $line
	 */
	protected function writeLine($line) {
	    if ($this->outFile !== null)
            fwrite($this->outFile, $line."\n");
	    else
	        $this->mipString .= $line."\n";
	}

	/**
	 * Adds the string in $str to the current line.
	 * @param string $str
	 */
	protected function writeToCurrentLine($str){
        if ($this->outFile !== null)
            fwrite($this->outFile, $str);
        else
            $this->mipString .= $str;
	}
}