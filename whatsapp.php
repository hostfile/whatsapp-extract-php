<?php
// http://www.delincuentedigital.com.ar/2013/10/extrayendo-conversaciones-de-whatsapp.aspx
$msgstore = $argv[1];
$key     =  "346a23652a46392b4d73257c67317e352e3372482177652c";
$data    =  file_get_contents($msgstore);
$method  =  "aes-192-ecb";
$data = base64_encode($data);
$dbfile = md5(uniqid(rand(), true));  
$decrypted = openssl_decrypt($data,$method,hex2bin($key));
$fp = fopen("databases/".$dbfile.".db", 'w');
fwrite($fp, $decrypted);
fclose($fp);
$db = new SQLite3("databases/".$dbfile.".db");
$results = $db->query('SELECT key_remote_jid, key_from_me, remote_resource, status, datetime(timestamp), data, media_url, media_mime_type, media_size, latitude, longitude FROM messages;');
while ($row = $results->fetchArray()) {
    $decrypt = "
    <b>Remitente</b>: ".$row['key_remote_jid'].
    "<br> key_from_me: ".$row['key_from_me'].
    "<br> remote_resource: ".$row['remote_resource'].
    "<br> status: ".$row['status'].
    "<br> datetime(timestamp): ".$row['datetime(timestamp)'].
    "<br><b>Mensaje</b>: ".utf8_decode($row['data']).
    "<br> media_url: ".$row['media_url'].
    "<br> media_mime_type: ".$row['media_mime_type'].
    "<br> media_size: ".$row['media_size'].
    "<br> latitude: ".$row['latitude'].
    "<br> longitude: ".$row['longitude']."<br>";   
$fp = fopen("decrypt/".$dbfile.".html", 'a');
fwrite($fp, $decrypt);
fclose($fp);
}
?>
