<?php

$target_path = basename( $_FILES['file']['name']); 
if ($_FILES['file']['type'] == "application/x-pkcs12"){
if(move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
    
$filename = $_FILES['file']['name'];
$downloadname = explode(".", $filename);
$downloadname = $downloadname[0];
$password = null;
$results = array();
$worked = openssl_pkcs12_read(file_get_contents($filename), $results, $password);
if(!$worked) {
    echo openssl_error_string();
}

$new_password = null;
$worked = openssl_pkey_export_to_file($results['pkey'], "apns-dev-key-noenc.pem", $new_password);
if($worked) {
openssl_x509_export_to_file($results['cert'], "apns-dev-cert.pem");

system('cat apns-dev-cert.pem apns-dev-key-noenc.pem > apns-dev.pem');
unlink($filename);
unlink("apns-dev-cert.pem");
unlink("apns-dev-key-noenc.pem");

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $downloadname . '.pem"');
readfile('apns-dev.pem');
unlink("apns-dev.pem");
exit;


} else {
    echo openssl_error_string();
}

    
} else{
    echo "There was an error uploading the file, please try again!";
}

} else {
	echo "wrong file type uploaded.";
}
?>