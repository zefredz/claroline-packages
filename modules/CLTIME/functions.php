<?php
    
    ClaroHeader::getInstance()->addHtmlHeader('
        function timeExam(){
            
                $.ajax({
                
                    url: "'.get_module_url('CLTIME').'/index.php",
                    
                    success: function(data){
                    
                        $("#serverTimeApplet").html(data);
                        
                    }
                    
                });
                
                setTimeout(timeExam, 1000);
                
            }
            
            $(function(){
                timeExam();
            }); ');
    
?>