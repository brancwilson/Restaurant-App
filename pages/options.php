<?php require_once __DIR__ . '/../templates/header.php'; ?>

<h1>Options</h1>
<table>
<tr>
    <form>
        <td>Number of Tables: </td>
        <td><input type="number" id="numTables"></input></td>
        <td><button id="numTablesSubmitBtn">Submit</button></td>
        <td>(Will reset all table statuses)</td>
    </form>
</tr>

<tr>
    <a href="/../resetOrders.php" class="button">Reset All Orders and Order Items</a>
</tr>

</table>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>