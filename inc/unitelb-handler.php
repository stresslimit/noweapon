<?php 
include('inc/class.db.php');

// var_dump($_POST);
foreach($_POST['results'] as $row) {
	// die();
	$id = $row['id_str'];
	$args = array(
		'id'=>$id,
		'hashtag'=>'unitelb',
		'data'=>serialize($row),
		'created_at'=>strtotime($row['created_at'])
	);
	// var_dump($args);
	$dupe = $db->getOne("select * from `hashtags` where id='$id'");
	// var_dump($row);
	if(!isset($dupe->id))
		$db->insert('hashtags',$args);
}
