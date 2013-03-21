<?php

// $Id$

/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 * @copyright (c) 2013 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package kernel
 * @author Frederic Minne <zefredz@claroline.net>
 *
 */

class Claro_ParseCsv extends parseCsv
{
    public function autoFromString( $data, $parse = true, $search_depth = null, $preferred = null, $enclosure = null )
    {
        // manualy load string in class data
        $data = trim($data);
          
        if ($this->convert_encoding)
            $data = iconv($this->input_encoding, $this->output_encoding, $data);
        
        if (substr($data, -1) != "\n")
            $data .= "\n";
        
        $this->file_data = &$data; // not sure why it's a reference here...
        
        // delegate to parent method now that data is loaded
        return parent::auto( null, $parse, $search_depth, $preferred, $enclosure );
    }
}
