document.addEventListener('DOMContentLoaded', function() {
    // Edit menu button handler
    $("#editmenuBtn").on("click", function() {
        document.location.href = "/../../pages/additem.php";
    });

    // Number of tables adjustment handler
    $("#numTablesSubmitBtn").on("click", function() {
        var numTables = $("#numTables").val();

        $.ajax({
            url: '/../../pages/phpfunctions/adjustNumberTables.php',
            type: 'post',
            data: {numTables: numTables},
            success: function() {
                alert("Number of tables adjusted!");
            }
        });
    });

    // Add to cart form handler - preserves notes
    $(document).on('submit', 'form[action*="menu-items.php"]', function(e) {
        var notes = $("#notes-column-box").val();
        $(this).find('input[name="orderNotes"]').val(notes);
    });

    // Remove item handler - preserves notes
    $(document).on('click', '.button.danger', function() {
        var notes = $("#notes-column-box").val();
        $(this).closest('form').append('<input type="hidden" name="orderNotes" value="' + notes + '">');
    });

    // Checkout button handler
    $("#checkoutbtn").on("click", function() {
        var notes = $("#notes-column-box").val();
        $(this).closest('form').find('input[name="orderNotes"]').val(notes);
    });

    
    $(".tableSelectBtn").on("click", function() {
        var tableNum = $(this).attr('id');
        
        $.ajax({
            url: '/../../pages/phpfunctions/tableStatusValidation.php',
            type: 'post',
            data: {tableNum: tableNum},
            success: function( status ) {
                console.log("Table validated...");
                if (status == 'open') {
                    window.location.replace("/pages/menu.php?table=" + tableNum);
                } else {
                    alert("Table already in use!");
                    location.reload();
                }
            },
            fail: function() {
                alert("Table error, JavaScript main.js");
                window.location.reload();
            }
        });
    });
    
});