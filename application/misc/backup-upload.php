<?php

use Aws\S3\S3Client;

//  Setup AWS Client
$client = S3Client::factory(
  array(
    'key'    => AWS_ACCESS_KEY_ID,
    'secret' => AWS_SECRET_ACCESS_KEY,
  )
);

//  File details
$file_path = $file;
$file_name = 'DatabaseBackup_'.date('Y-m-d-H-i').'.sql';

//  Execute request
try {
  $info = array(
    'Bucket'      => BUCKET_BACKUP,
    'Key'         => $file_name,
    'SourceFile'  => $file_path
  );
  $result = $client->putObject($info);
} catch (Exception $e) {
  error_log($e->getMessage());
}

//  Poll for upload completion
$client->waitUntilObjectExists(array(
  'Bucket' => BUCKET_BACKUP,
  'Key'    => $file_name
));

?>
