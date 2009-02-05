<?php
    
    ClaroHeader::getInstance()->addHtmlHeader('
    <script type="text/javascript">
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
            });
    </script>');
    
?>