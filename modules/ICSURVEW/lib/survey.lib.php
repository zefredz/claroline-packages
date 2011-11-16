<?php

class ICSURVEW_Survey
{
    protected $fileUrl;
    protected $questionnaire;
    
    public function __construct( $fileUrl )
    {
        $this->fileUrl = $fileUrl;
        $this->load();
    }
    
    public function load()
    {
        $this->questionnaire = json_decode( claro_utf8_encode ( file_get_contents( $this->fileUrl ) ) );
    }
    
    public function get()
    {
        return $this->questionnaire;
    }
    
    public function toJson()
    {
        return json_encode( claro_utf8_encode_array( $this->questionnaire ) );
    }
    
    
}
