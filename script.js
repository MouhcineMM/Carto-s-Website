$(document).ready(function() {
    $("#button1").click(function() {
        // Action 1
        $.ajax({
            url: 'process.php',
            type: 'POST',
            data: { action: 'action1' },
            success: function(response) {
                $("#result").html(response);
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

    $("#button2").click(function() {
        // Action 2
        $.ajax({
            url: 'process.php',
            type: 'POST',
            data: { action: 'action2' },
            success: function(response) {
                $("#result").html(response);
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
});
