<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>
<?php include("_security_access.php") ?>
<?php requireAccessLevelA($isLoggedIn, $securityAccessLevel); ?>
<?php
cleanupActivityLog();

$allowedLimits = [20, 50, 100, 150];
$limit = (int)($_GET["limit"] ?? 50);
if (!in_array($limit, $allowedLimits, true)) {
    $limit = 50;
}

$allowedSorts = [
    "date" => "date DESC, time DESC, id DESC",
    "user" => "`user` ASC, date DESC, time DESC, id DESC",
    "category" => "category ASC, date DESC, time DESC, id DESC"
];
$sort = $_GET["sort"] ?? "date";
if (!array_key_exists($sort, $allowedSorts)) {
    $sort = "date";
}

$page = max(1, (int)($_GET["page"] ?? 1));
$countResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM activitylog");
$total = $countResult ? (int)(mysqli_fetch_assoc($countResult)["total"] ?? 0) : 0;
$pageCount = max(1, (int)ceil($total / $limit));
$page = min($page, $pageCount);
$offset = ($page - 1) * $limit;

$query = "SELECT id, `user`, `date`, `time`, activity, object, info, category
          FROM activitylog
          ORDER BY " . $allowedSorts[$sort] . "
          LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<?php include("_master_head.php") ?>

<div class="grid-container">
    <div class="grid-emptyblack"></div>
    <div class="grid-header"><?php include("_master_header.php") ?></div>
    <div class="grid-emptyblack"></div>

    <?php $breadcrumimage = randomBreadcrumImage(); ?>
    <div class="grid-breadcrum" style="background-image: url(images/<?= $breadcrumimage ?>);">
        <?php include("_master_breadcrum.php") ?>
    </div>

    <div class="grid-topmenu"><?php include("_master_menu.php") ?></div>

    <div class="grid-empty"></div>
    <div class="grid-main">
        <div class="main">
            <h2>Activity log</h2>

            <form class="activitylog-controls" method="get">
                <label>Show max:
                    <select name="limit">
                        <?php foreach ($allowedLimits as $option) { ?>
                            <option value="<?= $option ?>" <?= $limit === $option ? "selected" : "" ?>><?= $option ?></option>
                        <?php } ?>
                    </select>
                </label>
                <label>Order by:
                    <select name="sort">
                        <option value="date" <?= $sort === "date" ? "selected" : "" ?>>Date</option>
                        <option value="user" <?= $sort === "user" ? "selected" : "" ?>>User</option>
                        <option value="category" <?= $sort === "category" ? "selected" : "" ?>>Category</option>
                    </select>
                </label>
                <input class="button" type="submit" value="Apply" />
            </form>

            <table class="registry-table activitylog-table">
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Activity</th>
                    <th>User</th>
                    <th>Category</th>
                    <th>Object</th>
                    <th>Info</th>
                    <th></th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row["date"]) ?></td>
                        <td><?= htmlspecialchars($row["time"]) ?></td>
                        <td><?= htmlspecialchars($row["activity"]) ?></td>
                        <td><?= htmlspecialchars($row["user"]) ?></td>
                        <td><?= htmlspecialchars($row["category"]) ?></td>
                        <td><?= htmlspecialchars($row["object"]) ?></td>
                        <td><?= nl2br(htmlspecialchars($row["info"])) ?></td>
                        <td>
                            <a class="delete-link" href="activitylog_delete_doit.php?id=<?= (int)$row["id"] ?>"
                               onclick="return confirm('Are you sure you want to delete log entry <?= (int)$row["id"] ?>?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>

            <?php if ($total === 0) { ?>
                <p>No activity log entries found.</p>
            <?php } ?>

            <div class="activitylog-pagination">
                <?php if ($page > 1) { ?>
                    <a class="button" href="?limit=<?= $limit ?>&sort=<?= urlencode($sort) ?>&page=<?= $page - 1 ?>">&lt;&lt; Previous <?= $limit ?></a>
                <?php } ?>
                <span>Page <?= $page ?> of <?= $pageCount ?></span>
                <?php if ($page < $pageCount) { ?>
                    <a class="button" href="?limit=<?= $limit ?>&sort=<?= urlencode($sort) ?>&page=<?= $page + 1 ?>">Next <?= $limit ?> &gt;&gt;</a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="grid-rightmenu"><?php include("_master_info-menu.php") ?></div>
    <div class="grid-empty"></div>

    <div class="grid-emptyblack"></div>
    <div class="grid-footer"><?php include("_master_footer.php") ?></div>
    <div class="grid-emptyblack"></div>
</div>

<?php
$stmt->close();
include("_master_bottom.php");
?>
