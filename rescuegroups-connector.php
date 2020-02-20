<?php
/**
 * Plugin Name: RescueGroups Connector
 * Plugin URI: https://github.com/aebs90/mm-rescuegroups-connector
 * Description: Connects to the RescueGroups HTTP API (v2) and returns a JSON response that can be used with WP All Import.
 * Version: 1.0.0-alpha
 * Requires at least: 5.3
 * Requires PHP: 7.3
 * Author: MIGHTYminnow, aebs90
 * Author URI: https://mightyminnow.com
 * Text Domain: rgconnector
 * 
 */

if ( ! isset( $_GET['rgconnector'] ) ) {
	return;
}

$data = array(
	"apikey" => sanitize_text_field( $_GET['rgconnector'] ),
	"objectType" => "animals",
	"objectAction" => "publicSearch",
	"search" => array (
		"resultStart" => 0,
		"resultLimit" => 50,
		"resultSort" => "animalID",
		"resultOrder" => "desc",
		"calcFoundRows" => "Yes",
		"filters" => array(
			/*
            array(
                "fieldName" => "animalStatus",
                "operation" => "equal",
                "criteria" => "Available",
            ),
			*/
			array(
				"fieldName" => "animalSpecies",
				"operation" => "equals",
				"criteria" => "cat",
			),
			array(
				"fieldName" => "animalLocationCitystate",
				"operation" => "equals",
				"criteria" => "Oakland, CA",
			),
		),
		"fields" => array(
			"animalID",
			'animalUpdatedDate',
			'animalName',
			'animalPictures',
		),
	),
);

$jsonData = json_encode($data);
$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
curl_setopt($ch, CURLOPT_URL, "https://api.rescuegroups.org/http/v2.json");
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
if (curl_errno($ch)) {
	$results = curl_error($ch);
} else {
	curl_close($ch);
	$results = $result;
}
header('Content-Type: application/json');
echo $results;
exit;
