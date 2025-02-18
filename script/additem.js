$("document").ready(function(){
    function loadItemTable() {
        
        $("#itemsTable").empty();

        $.ajax({
            url: 'loadmenutable.php',
            type: 'get',
            success: function(response) {
                $("#itemsTable").append("<tr id='itemTableLabels'><td>Item Name</td><td>Item Price</td><td>Item Type</td><td>Item Availability</td><td>Edit</td></tr>")
                $("#itemsTable").append(response);
            }
        });
    }

    loadItemTable();

    $("#newItemBtn").click(function() {
        console.log("Add Item Button clicked...");

        event.preventDefault();

        var itemName = $("#itemName").val();
        var itemPrice = $("#itemPrice").val();
        var itemType = $("#itemType").val();
        var itemAvailability = "";

        if ($("#itemAvailability_B").is(":checked")) {itemAvailability = itemAvailability + "B";} else {console.log("Breakfast not checked");}
        if ($("#itemAvailability_L").is(":checked")) {itemAvailability = itemAvailability + "L";} else {console.log("Lunch not checked");}
        if ($("#itemAvailability_D").is(":checked")) {itemAvailability = itemAvailability + "D";} else {console.log("Dinner not checked");}

        $.ajax({
            url: 'additem.php',
            type: 'post',
            //data must be sent as a key value pair - {dataName: javascriptData}
            data: {itemName: itemName, itemPrice: itemPrice, itemType: itemType, itemAvailability: itemAvailability},
            success: function() {
                alert("Item added");
                loadItemTable();
            }
        });
    })

    // .on() is used for dynamically added elements, since the DOM is already loaded
    $(document).on("click", ".deleteItem", function() {

        var delID = (this.id);
        var delItem = delID.split("_");
        console.log(delItem[1]);

        $.ajax({
            url: 'deleteitem.php',
            type: 'post',
            data: {deleteItem: delItem[1]},
            success: function() {
                loadItemTable();
            }
        })

    });


});