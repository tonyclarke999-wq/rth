<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:80/login_validate.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "uname=legacy_admin&pword=password&button=Submit");
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, 'http://localhost:80/user_edit_account_page.php?user_id=00099');
curl_setopt($ch, CURLOPT_POST, 0);
$output = curl_exec($ch);
echo $output;
curl_close($ch);
?>
