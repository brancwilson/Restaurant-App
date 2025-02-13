$("document").ready(function(){

    $("#newItemBtn").click(function() {
        console.log("Add Item Button clicked...");

        event.preventDefault();

        var itemName = $("#itemName").val();
        var itemPrice = $("#itemPrice").val();
        var itemType = $("#itemType").val();
        var itemAvailability = "";

        if ($("#itemAvailability_B").val() == undefined) {} else {itemAvailability.concat("B")};
        if ($("#itemAvailability_L").val() == undefined) {} else {itemAvailability.concat("L")};
        if ($("#itemAvailability_D").val() == undefined) {} else {itemAvailability.concat("D")};

        $.ajax({
            url: 'additem.php',
            type: 'post',
            //data must be sent as a key value pair - {dataName: javascriptData}
            data: {itemName: itemName, itemPrice: itemPrice, itemType: itemType, itemAvailability: itemAvailability},
            success: function() {
                alert("Item added");
            }
        });
    })

});