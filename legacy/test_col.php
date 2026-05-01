<?php
include"./api/include_api.php";
$q = "SELECT project_user_assoc.email_new_bug FROM project_user_assoc";
$rs = $db->Execute($q);
if (!$rs) {
    echo $db->ErrorMsg();
} else {
    echo "Success! Rows: " . $rs->RecordCount();
}
?>
