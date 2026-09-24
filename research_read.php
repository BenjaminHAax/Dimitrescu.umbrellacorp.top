<?php include("_research_page_start.php"); ?>
<h2>Research Database</h2>
<?php if (in_array($securityAccessLevel, ["A", "B"], true)) { ?><p class="registry-actions"><a class="button" href="research_add.php">Add research object</a></p><?php } ?>
<table class="registry-table research-table"><tr><th>Number</th><th>Name</th><th>Created</th><th>By</th><th>Entries</th><th>Last Entry</th><th>Status</th><?php if(in_array($securityAccessLevel,["A","B"],true)){ ?><th>Edit</th><th>Delete</th><?php } ?></tr>
<?php
$sql = "SELECT o.*, COUNT(e.id) entries, MAX(STR_TO_DATE(CONCAT(e.entryDate,' ',e.entryTime),'%d.%m.%Y %H:%i')) lastEntry
        FROM research_objects o LEFT JOIN research_entries e ON e.researchObjectId=o.id GROUP BY o.id ORDER BY o.objectNumber";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>".htmlspecialchars($row["objectNumber"])."</td><td><a href=\"research_read_object.php?id=".(int)$row["id"]."\">".htmlspecialchars($row["objectName"])."</a></td><td>".htmlspecialchars($row["objectCreatedDate"])."</td><td>".htmlspecialchars($row["objectCreator"])."</td><td>".(int)$row["entries"]."</td><td>".htmlspecialchars($row["lastEntry"] ? date("d.m.Y", strtotime($row["lastEntry"])) : "-")."</td><td>".htmlspecialchars($row["objectStatus"])."</td>";
    if (in_array($securityAccessLevel, ["A","B"], true)) echo "<td><a class=\"edit-link\" href=\"research_edit.php?id=".(int)$row["id"]."\">Edit</a></td><td><a class=\"delete-link\" href=\"research_delete_doit.php?id=".(int)$row["id"]."\" onclick=\"return confirm('Delete this research object?');\">Delete</a></td>";
    echo "</tr>";
}
?>
</table>
<?php include("_research_page_end.php"); ?>
