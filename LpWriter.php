<?php

namespace vendor\i4h\PhpMIP;

/**
 * Writer for the CPLEX LP file format
 *
 * @author Fabian Gnegel <gnegelf@hsu-hh.de>
 * @author Ingmar Vierhaus <mail@ingmar-vierhaus.de>
 * @since 2016/09/21
 */
class LpWriter extends MIPwriter implements MIPwriterInterface
{
    protected $extension = "lp";

    /**
     * Writes the mip in CPLEX LP-Format
     * @see MIPwriterInterface::write()
     */
    public function write(MIP $mip)
    {
        $this->openFile();

        /* Objective function */
        $this->writeLine('Minimize');
        $this->writeToCurrentLine('obj: ');
        $lineLength = 4;
        $objParts = [];
        foreach ($mip->objectiveCoefficients as $name=>$val) {
            if (is_array($val)) {
                foreach ($val as $index=>$coeff) {
                    $lineLength+=strlen($coeff.' '.$name.'('.$index.')')+3;
                    if ($lineLength > 400) {
                        $objParts[] = $coeff.' '.$name.'('.$index.')'."\n";
                        $lineLength = 0;
                    } else {
                        $objParts[] = $coeff . ' ' . $name . '(' . $index . ')';
                    }
                }
            } else {
                $lineLength += strlen($val.' '.$name)+3;
                if ($lineLength > 400){
                    $lineLength = 0;
                    $objParts[] = $val.' '.$name."\n";
                }
                else {
                    $objParts[] = $val.' '.$name;
                }
            }
        }
        $this->writeToCurrentLine(str_replace('+ -','- ',implode(' + ',$objParts)));
        $this->writeToCurrentLine(" \n");

        /* constraints */
        if (!$mip->constraints == NULL) {
            $this->writeLine('Subject To');
            foreach ($mip->constraints as $constraint) {
                $this->writeLine(str_replace('+ -','- ',$constraint));
            }
        }

        /* variable specifications (i.e. binary and integer) */
        $this->writeLine('Bounds');

        $integers=[];
        $binaries=[];
        foreach($mip->variables as $name=>$arr) {
            if (is_array($arr)) {
                foreach($arr as $setIdx=>$var){
                    if ($var->type == MIPvariable::TYPE_INTEGER)
                        $integers[] = $name.'('.$setIdx.')';
                    if ($var->type == MIPvariable::TYPE_BINARY)
                        $binaries[]=$name.'('.$setIdx.')';
                }
            } else {
                if ($arr->type == MIPvariable::TYPE_INTEGER)
                    $integers[] = $name;
                if ($arr->type == MIPvariable::TYPE_BINARY)
                    $binaries[] = $name;
            }
        }

        if (!empty($integers)) {
            $this->writeLine('General');
            $this->writeToCurrentLine(implode("\n",$integers));
            $this->writeToCurrentLine("\n");
        }

        if(!empty($binaries)){
            $this->writeLine('Binaries');
            $this->writeToCurrentLine(implode("\n",$binaries));
            $this->writeToCurrentLine("\n");
        }

        $this->writeLine("end");
        $this->closeFile();
    }
}