$(document).ready(function(){
    $("#submit").click(function(){
        $("form").append('<input type="hidden" name="antibot" value="on" />');
    });
});