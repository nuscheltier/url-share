<?php
    $database = json_decode(file_get_contents('database.json'), true);
    $edit_mode = isset($_GET["edit"]);

    if(isset($_POST["title"]) && isset($_POST["url"])) { //a valid url is posted
        $new_entry = array(
            "title" => htmlspecialchars($_POST["title"]),
            "url" => htmlspecialchars($_POST["url"]),
            "description" => isset($_POST["description"]) ? htmlspecialchars($_POST["description"]) : "",
            "visited" => false
        );
        if(count(array_filter($database, function($element) {
            return $element["url"] == $_POST["url"];
        })) == 0) {
            array_push($database, $new_entry);
        } elseif(count(array_filter($database, function($element) {
            return $element["url"] == $_POST["url"];
        })) == 1 & $edit_mode) {
            $database[$_GET["edit"]] = $new_entry;
        }
        file_put_contents('database.json', json_encode($database));
    }
    if(isset($_GET["delete"])) {
        $id = $_GET["delete"];
        array_splice($database, $id, 1);
        file_put_contents('database.json', json_encode($database));
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>URL Share</title>
        <link rel="stylesheet" href="url_share.css">
    </head>
    <body>
        <section>
            <form method="POST" action="barebone_url_share.php<?php if($edit_mode) {echo "?edit=" . $_GET["edit"];} ?>">
                <table style="margin-left:auto; margin-right:auto;">
                    <tr>
                        <td style="text-align:right;">Title:</td>
                        <td><input type="text" name="title"<?php if($edit_mode) {echo " value=\"" . $database[$_GET["edit"]]["title"] . "\"";} ?>></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">URL:</td>
                        <td><input type="text" name="url"<?php if($edit_mode) {echo " value=\"" . $database[$_GET["edit"]]["url"] . "\"";} ?>></td>
                    </tr>
                    <tr>
                        <td style="text-align:right; vertical-align:top;">Description:</td>
                        <td><textarea name="description"><?php if($edit_mode) {echo $database[$_GET["edit"]]["description"];} ?></textarea></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="submit" value="share"></td>
                    </tr>
                </table>
            </form>
        </section>
        <section>
            <?php
                for($i = count($database) - 1; $i >= 0; $i--) {
                    ?>
                <div class="<?php echo $i % 2 == 0 ? uneven : even; ?>">
                    <a href="<?php echo $database[$i]["url"]; ?>">
                        <?php echo $database[$i]["title"]; ?>
                    </a>
                    <span>
                        <span class="button">
                            <a href="barebone_url_share.php?edit=<?php echo $i; ?>">
                                Edit
                            </a>
                        </span>
                        <span class="button">
                            <a href="barebone_url_share.php?delete=<?php echo $i; ?>">
                                Delete
                            </a>
                        </span>
                    </span>
                </div>
                <?php 
                    if($database[$i]["description"] != "") {
                    ?>
                <div class="description <?php echo $i % 2 == 0 ? uneven : even; ?>">
                    <?php echo $database[$i]["description"]; ?>
                </div>
                    <?php
                    }
                }
            ?>
        </section>
    </body>
</html>