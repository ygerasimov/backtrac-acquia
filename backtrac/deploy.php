<?php
/**
 * Script to trigger the jobs on BackTrac API.
**/

list($script, $site, $target_env, $source_branch, $deployed_tag, ) = $argv;

switch ($target_env) {
  case 'dev':
    $operation = 'compare_stage_dev';
    break;

  case 'test':
    $operation = 'compare_prod_stage';
    break;

  default:
    echo 'Neither Dev nor Test environment. Can not trigger the backtrac job.';
    exit;
}

$yaml = yaml_parse_file('backtrac.yml');
$api_key = $yaml['api']['key'];
$project_id = $yaml['api']['project_id'];

if (!empty($yaml['general']['uris'])) {
  $data = array(
    'uris' => $yaml['general']['uris'],
  );

  $ch = curl_init();
  $curl_options = array(
    CURLOPT_URL => 'https://backtrac.io/api/project/' . $project_id,
    CURLOPT_HTTPHEADER => array('x-api-key: ' . $api_key),
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_RETURNTRANSFER => TRUE,
  );
  curl_setopt_array($ch, $curl_options);
  $curl_response = json_decode(curl_exec($ch));
  curl_close($ch);
  if ($curl_response->status == 'success') {
    echo ucwords($curl_response->status) . ': Project ' . $curl_response->result->title . " updated.\n";
  }
  else {
    echo ucwords($curl_response->status) . ': ' . $curl_response->message . "\n";
  }
}

$curl = curl_init();
$curl_options = array(
  CURLOPT_URL => 'https://backtrac.io/api/project/' . $project_id . '/' . $operation,
  CURLOPT_POSTFIELDS => json_encode($data),
  CURLOPT_HTTPHEADER => array('x-api-key: ' . $api_key),
  CURLOPT_POST => 1,
  CURLOPT_RETURNTRANSFER => 1,
);
curl_setopt_array($curl, $curl_options);
$curl_response = json_decode(curl_exec($curl));
curl_close($curl);

if ($curl_response->status == 'success') {
  echo ucwords($curl_response->status) . ': ' . $curl_response->result->message . "\n";
  echo "Check out the result here: " . $curl_response->result->url . "\n";
} 
else {
  echo ucwords($curl_response->status) . ': ' . $curl_response->message . "\n";
}

