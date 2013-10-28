
jQuery(document).ready(function($){
	$('.add-to-cart input[type=checkbox]').click(function(){
		if ( $(this).prop('checked')===true ) {
			$('.add-to-cart input[type=text]').val('0').prop('disabled', true).addClass('disabled');
		} else {
			$('.add-to-cart input[type=text]').val('1').prop('disabled', false).removeClass('disabled');
		}
	});
});
