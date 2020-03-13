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

if ( ! function_exists( 'rgconnector_cron' ) ) {
	function rgconnector_cron( $url ) {
		$response = wp_remote_get( $url, array(
			'timeout' => 360,
		) );
		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			error_log( 'CRON LOG SUCCESS: ' . $response['body'] );
		} else {
			error_log( 'CRON LOG ERROR: ' . $response['body'] );
		}
		exit;
	}
	add_action( 'rgconnector_import_trigger', 'rgconnector_cron' );
	add_action( 'rgconnector_import_processing', 'rgconnector_cron' );
}

if ( ! isset( $_GET['rgconnector'] ) ) {
	return;
}

$fields = array(
	'animalID' => 'ID (key)',
	'animalOrgID' => 'Org ID (key)',
	'animalActivityLevel' => 'Activity level (string from values list)',
	'animalAdoptionFee' => 'Adoption fee (string)',
	'animalAltered' => 'Altered (string from values list)',
	'animalAvailableDate' => 'Available Date (date)',
	'animalBirthdate' => 'Birthdate (date)',
	'animalBirthdateExact' => 'Exact Birthdate (string from values list)',
	'animalBreed' => 'Breed (string)',
	'animalCoatLength' => 'Coat Length (string from values list)',
	'animalColor' => 'Color (string)',
	'animalColorID' => 'Color (General) (key)',
	'animalColorDetails' => 'Color details (string)',
	'animalCourtesy' => 'Courtesy (string from values list)',
	'animalDeclawed' => 'Declawed (string from values list)',
	'animalDescription' => 'Description (string)',
	'animalDescriptionPlain' => 'Description (no html) (string)',
	'animalDistinguishingMarks' => 'Distinguishing marks (string)',
	'animalEarType' => 'Ear type (string from values list)',
	'animalEnergyLevel' => 'Energy level (string from values list)',
	'animalExerciseNeeds' => 'Exercise needs (string from values list)',
	'animalEyeColor' => 'Eye color (string from values list)',
	'animalFence' => 'Requires a home with fence (string from values list)',
	'animalFound' => 'Found (string from values list)',
	'animalFoundDate' => 'Found date (date)',
	'animalFoundPostalcode' => 'Found zip/postal code (postalcode)',
	'animalGeneralAge' => 'General Age (string from values list)',
	'animalGeneralSizePotential' => 'Size potential (general) (string from values list)',
	'animalGroomingNeeds' => 'Grooming needs (string from values list)',
	'animalHousetrained' => 'Housetrained (string from values list)',
	'animalIndoorOutdoor' => 'Indoor/Outdoor (string from values list)',
	'animalKillDate' => 'Euthanasia date (date)',
	'animalKillReason' => 'Euthanasia reason (string from values list)',
	'animalLocation' => 'Location (postalcode)',
	'animalLocationDistance' => 'Distance (int)',
	'animalLocationCitystate' => 'Distance (int)',
	'animalMicrochipped' => 'Microchipped (string from values list)',
	'animalMixedBreed' => 'Mixed breed (string from values list)',
	'animalName' => 'Name (string)',
	'animalSpecialneeds' => 'Has special needs (string from values list)',
	'animalSpecialneedsDescription' => 'Special needs description (string)',
	'animalNeedsFoster' => 'Needs a Foster (string from values list)',
	'animalNewPeople' => 'Reaction to new people (string from values list)',
	'animalNotHousetrainedReason' => 'Reason not housetrained (string)',
	'animalObedienceTraining' => 'Obedience training (string from values list)',
	'animalOKWithAdults' => 'Good with adults (string from values list)',
	'animalOKWithCats' => 'OK with cats (string from values list)',
	'animalOKWithDogs' => 'OK with dogs (string from values list)',
	'animalOKWithKids' => 'OK with kids (string from values list)',
	'animalOwnerExperience' => 'Owner experience needed (string from values list)',
	'animalPattern' => 'Pattern (string)',
	'animalPatternID' => 'Pattern ID (key)',
	'animalAdoptionPending' => 'Adoption pending (string from values list)',
	'animalPrimaryBreed' => 'Primary breed (string)',
	'animalPrimaryBreedID' => 'Primary breed ID (key)',
	'animalRescueID' => 'Rescue ID (string)',
	'animalSearchString' => 'Search (string)',
	'animalSecondaryBreed' => 'Secondary Breed (string)',
	'animalSecondaryBreedID' => 'Secondary Breed ID (key)',
	'animalSex' => 'Sex (string from values list)',
	'animalShedding' => 'Shedding amount (string from values list)',
	'animalSizeCurrent' => 'Current size (decimal)',
	'animalSizePotential' => 'Size potential (decimal)',
	'animalSizeUOM' => 'Size unit of measure (string from values list)',
	'animalSpecies' => 'Species (string)',
	'animalSpeciesID' => 'Species (key)',
	'animalSponsorable' => 'Allow sponsorship (string from values list)',
	'animalSponsors' => 'Sponsors (string)',
	'animalSponsorshipDetails' => 'Sponsorship details (string)',
	'animalSponsorshipMinimum' => 'Sponsorship minimum (decimal)',
	'animalStatus' => 'Status (string)',
	'animalStatusID' => 'Status ID (key)',
	'animalSummary' => 'Summary (string)',
	'animalTailType' => 'Tail type (string from values list)',
	'animalThumbnailUrl' => 'Thumbnail URL (string)',
	'animalUptodate' => 'Up-to-date (string from values list)',
	'animalUpdatedDate' => 'Last updated (date)',
	'animalUrl' => 'Web page (url)',
	'animalVocal' => 'Likes to vocalize (string from values list)',
	'animalYardRequired' => 'Requires a Yard (string from values list)',
	'animalAffectionate' => 'Affectionate (string from values list)',
	'animalApartment' => 'Apartment appropriate (string from values list)',
	'animalCratetrained' => 'Crate trained (string from values list)',
	'animalDrools' => 'Drools excessively (string from values list)',
	'animalEagerToPlease' => 'Eager to please (string from values list)',
	'animalEscapes' => 'Tries to escape (string from values list)',
	'animalEventempered' => 'Even-tempered (string from values list)',
	'animalFetches' => 'Likes to fetch (string from values list)',
	'animalGentle' => 'Gentle (string from values list)',
	'animalGoodInCar' => 'Does well in a car (string from values list)',
	'animalGoofy' => 'Goofy (string from values list)',
	'animalHasAllergies' => 'Has allergies (string from values list)',
	'animalHearingImpaired' => 'Hearing impaired (string from values list)',
	'animalHypoallergenic' => 'Hypoallergenic (string from values list)',
	'animalIndependent' => 'Independent / aloof (string from values list)',
	'animalIntelligent' => 'Intelligent (string from values list)',
	'animalLap' => 'Lap pet (string from values list)',
	'animalLeashtrained' => 'Leash trained (string from values list)',
	'animalNeedsCompanionAnimal' => 'Needs companion animal (string from values list)',
	'animalNoCold' => 'Cold sensitive (string from values list)',
	'animalNoFemaleDogs' => 'Not good with female dogs (string from values list)',
	'animalNoHeat' => 'Heat sensitive (string from values list)',
	'animalNoLargeDogs' => 'Not good with large dogs (string from values list)',
	'animalNoMaleDogs' => 'Not good with male dogs (string from values list)',
	'animalNoSmallDogs' => 'Not good with small dogs (string from values list)',
	'animalObedient' => 'Obedient (string from values list)',
	'animalOKForSeniors' => 'Good for seniors / elderly (string from values list)',
	'animalOKWithFarmAnimals' => 'Good with farm animals (string from values list)',
	'animalOlderKidsOnly' => 'Older/ considerate kids only (string from values list)',
	'animalOngoingMedical' => 'Needs ongoing medical care (string from values list)',
	'animalPlayful' => 'Playful (string from values list)',
	'animalPlaysToys' => 'Likes toys (string from values list)',
	'animalPredatory' => 'Predatory (string from values list)',
	'animalProtective' => 'Protective / territorial (string from values list)',
	'animalSightImpaired' => 'Sight impaired (string from values list)',
	'animalSkittish' => 'Skittish (string from values list)',
	'animalSpecialDiet' => 'Special diet required (string from values list)',
	'animalSwims' => 'Likes to swim (string from values list)',
	'animalTimid' => 'Timid / shy (string from values list)',
	'fosterEmail' => 'Email (string)',
	'fosterFirstname' => 'First Name (string)',
	'fosterLastname' => 'Last Name (string)',
	'fosterName' => 'Foster (string)',
	'fosterPhoneCell' => 'Phone (Cell) (string)',
	'fosterPhoneHome' => 'Phone (Home) (string)',
	'fosterSalutation' => 'Salutation (string)',
	'locationAddress' => 'Address (string)',
	'locationCity' => 'City (string)',
	'locationCountry' => 'Country (string)',
	'locationUrl' => 'Link (string)',
	'locationName' => 'Location (enumLookup)',
	'locationPhone' => 'Phone (string)',
	'locationState' => 'State (string)',
	'locationPostalcode' => 'Postal Code (postalcode)',
	'animalPictures' => 'Pictures (string)',
	'animalVideos' => 'Videos (string)',
	'animalVideoUrls' => 'Video URLs (string)',
);

$filter_fields = array();

foreach ( $fields as $key => $text ) {
	$filter_fields[] = $key;
}

$data = array(
	"apikey" => sanitize_text_field( $_GET['rgconnector'] ),
	"objectType" => "animals",
	"objectAction" => "publicSearch",
	"search" => array (
		"resultStart" => 0,
		"resultLimit" => 150,
		"resultSort" => "animalUpdatedDate",
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
		"fields" => $filter_fields,
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
$results = json_decode( $results );
foreach ( $results->data as $key => $animal ) {
	$results->data->{$key}->animalDescription = '';
}

header('Content-Type: application/json');
echo json_encode( $results->data );
exit;
