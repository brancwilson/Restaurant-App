// main.js - Simplified version without AJAX notes handling

document.addEventListener('DOMContentLoaded', function() {
    // Edit Menu Button Handler
    $("#editmenuBtn").on("click", function() {
        document.location.href = "/../../pages/additem.php";
    });

    // Number of Tables Submit Handler
    $("#numTablesSubmitBtn").on("click", function() {
        var numTables = $("#numTables").val();

        $.ajax({
            url: '/../../pages/phpfunctions/adjustNumberTables.php',
            type: 'post',
            data: {numTables: numTables},
            success: function() {
                alert("Number of tables adjusted!");
            },
            error: function(xhr, status, error) {
                console.error("Error adjusting tables:", error);
                alert("Failed to adjust tables. Please try again.");
            }
        });
    });

    // Proceed to Checkout Button - Now handled by standard form submission
    // Note: The form will submit normally, no need for AJAX
    // Remove any existing AJAX handling for checkoutbtn
});

// Remove any other checkout-related AJAX code