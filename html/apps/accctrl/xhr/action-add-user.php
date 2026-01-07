<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);


$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

if ($_POST['username'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please insert username!'
	));
} else if ($dbc->HasRecord("os_users", "name = '" . $_POST['username'] . "'")) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Username is already exist.'
	));
} else {

	$data = array(
		'#id' => "DEFAULT",
		'title' => $_POST['title'],
		'name' => $_POST['first'],
		'surname' => $_POST['surname'],
		'nickname' => $_POST['nickname'],
		'gender' => $_POST['gender'],
		'email' => $_POST['email'],
		'phone' => $_POST['phone'],
		'mobile' => $_POST['mobile'],
		'#created' => "NOW()",
		'#updated' => "NOW()",
		'status' => 1
	);
	if ($_POST['dob'] == "") {
		$data['#dob'] = "NULL";
	} else {
		$data['dob'] = $_POST['dob'];
	}

	$dbc->Insert("os_contacts", $data);
	$contact_id = $dbc->GetID();



	$data = array(
		'#id' => "DEFAULT",
		'address' => $_POST['address'],
		'#country' => $_POST['country'],
		'#city' => $_POST['city'],
		'#district' => $_POST['district'],
		'#subdistrict' => $_POST['subdistrict'],
		'postal' => $_POST['postal'],
		'#created' => "NOW()",
		'#updated' => "NOW()",
		'#contact' => $contact_id,
		'#organization' => 'NULL',
		'priority' => 1
	);

	$dbc->Insert("os_address", $data);
	$address_id = $dbc->GetID();
	$fulladdress = $os->load_fulladdress($address_id);
	$dbc->Update("os_address", array("fulladdress" => $fulladdress), "id=" . $address_id);

	$display_name = $_POST['username'];
	if ($_POST['first'] != "") $display_name = $_POST['first'];
	if ($_POST['surname'] != "") $display_name .= " " . $_POST['surname'];


	$data = array(
		'#id' => "DEFAULT",
		'name' => $_POST['username'],
		'#password' =>  "SHA2('" . $_POST['password'] . "', 224)",
		'display' => $display_name,
		'status' => 1,
		'#created' => "NOW()",
		'#updated' => "NOW()",
		'#gid' => $_POST['gid'],
		'#contact' => $contact_id,
		'setting' => json_encode(array())

	);

	if ($dbc->Insert("os_users", $data)) {
		$user_id = $dbc->GetID();
		echo json_encode(array(
			'success' => true,
			'msg' => $user_id
		));

		$user = $dbc->GetRecord("os_users", "*", "id=" . $user_id);
		$contact = $dbc->GetRecord("os_contacts", "*", "id=" . $contact_id);
		$address = $dbc->GetRecord("os_address", "*", "id=" . $address_id);
		$os->save_log(0, $_SESSION['auth']['user_id'], "user-add", $user_id, array("users" => $user, "contacts" => $contact, "address" => $address));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "Insert Error"
		));
	}
}

$dbc->Close();
