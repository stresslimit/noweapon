<?php
/*
Template Name: unitelb
*/

$hashtag = 'unitelb';
$base_url = get_bloginfo('template_url').'/'.$hashtag.'/';
// header('Content-Type: text/html; charset=utf-8' );
include(TEMPLATEPATH.'/inc/class.db.php');
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />	
<script src="http://code.jquery.com/jquery-1.5.1.js"></script>
<script>

$(document).ready(function() {
	setInterval(gethashes, 10000, '<?= $hashtag ?>');
});

function gethashes(hashtag) {
	$.getJSON("http://search.twitter.com/search.json?q=%23" + hashtag + "&callback=?", function(data, status) {
		$.each(data.results, function(i, info) {
			the_id = 'li-'+info.id_str;
			if( !$('#'+the_id).length ) {
				var text = info.text;
				// console.log(text);
				// text = info.text.replace( /(\#)\w+\b/,"").replace( /(\@)\w+\b/,"");                             // format @username properly
				// text = info.text.replace( /(\@)\w+\b/,"");                             // format @username properly
				// text = info.text.replace( /\#<?= $hashtag ?>/,"");                             // get rid of hash tag
				text = '<p class="tweet">'+ text +'</p><p class="from">— @'+ info.from_user +'</p>';            // add username after tweet line
				$('<div>').html(text).attr('id', the_id).css('display','none').prependTo('#feed').fadeIn(777);
			}
		});
		$.ajax({
			url: "<?= $base_url ?>inc/unitelb-handler.php",
			data: data,
			type: 'post',
			success: function(data) {
				// console.log(data);
				}
		});
	});
}

</script>
</head>

<body>

<div id="feed">
<?

$q = $db->query("select * from `hashtags` where `hashtag`='$hashtag' order by created_at desc");
while($r = $db->o($q)) :
	$data = unserialize($r->data);
?>

	<div id="li-<?= $r->id ?>">
		<p class="tweet"><?= /*str_replace('#loveandhate2','',*/$data['text']/*)*/ ?></p>
		<p class="from">— @<?= $data['from_user'] ?></p>
	</div>
<?
endwhile;
?></div>

</body>
</html>