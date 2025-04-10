// Add any necessary JavaScript here
document.addEventListener('DOMContentLoaded', function() {
    // Example: Add event listeners or other JS logic

    $("#editmenuBtn").on("click", function() {
        document.location.href = "/../../pages/additem.php";
    });

    // Takes text from order notes text area and passes to the checkout page
    $("#proceedtocheckout").on("click", function() {
        var orderNotes = null;

        if ($("#proceedtocheckout").val() != null) {
            orderNotes = $("#proceedtocheckout").val();
        }

        $.ajax({
            url: '/../../pages/checkout.php',
            type: 'post',
            //data must be sent as a key value pair - {dataName: javascriptData}
            data: {orderNotes: orderNotes},
            success: function() {
               console.log("Order note added");
            }
        });

    });

    $("#numTablesSubmitBtn").on("click", function() {
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