<?php

namespace vendor\i4h\phpmip;

/**
 * This is the interface for MIP writer implementations
 *
 * @author Ingmar Vierhaus
 */
interface MIPwriterInterface
{
    /**
     * @return mixed The default extension the writer should write to
     */
	public function getExtension();

    /**
     * Should write the MIP to file at $this->outFile
     * @param MIP $mip
     * @return mixed
     */
	public function write(MIP $mip);
}
