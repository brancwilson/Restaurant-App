$("document").ready(function(){

    $("#newItemBtn").click(function() {
        console.log("Add Item Button clicked...");

        event.preventDefault();

        var itemName = $("#itemName").val();
        var itemPrice = $("#itemPrice").val();
        var itemType = $("#itemType").val();
        var itemAvailability = String($("#itemAvailability_B").val()[0]).concat(String($("#itemAvailability_L").val()[0]).concat(String($("#itemAvailability_D").val()[0])));
        

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