<?php require_once __DIR__ . '/../templates/header.php'; ?>

<h1>Options</h1>
<table>
<tr>
    <form>
        <td>Number of Tables: </td>
        <td><input type="number" id="numTables"></input></td>
        <td><button id="numTablesSubmitBtn">Submit</button></td>
    </form>
    <br>
    <a href="/../resetOrders.php" class="button">Reset All Orders and Order Items</a>

<tr>
</table>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>