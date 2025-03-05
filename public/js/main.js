// Add any necessary JavaScript here
document.addEventListener('DOMContentLoaded', function() {
    // Example: Add event listeners or other JS logic

    $("#editmenuBtn").on("click", function() {
        document.location.href = "/../../pages/additem.php";
    });

    $("numTablesSubmitBtn").on("click", function() {
        var numTables = $("#numTables").val();

        $.ajax({
            url: '/../../pages/phpfunctions/adjustNumberTables.php',
            type: 'post',
            //data must be sent as a key value pair - {dataName: javascriptData}
            data: {numTables: numTables},
            success: function() {
                alert("Number of tables adjusted!");
            }
        });
    });
});