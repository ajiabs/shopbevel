$j(function(){
	if( $j('body').find('.home') ){
		$j('.trunk-show').each(function(){
			$j('.trunk-show').children(':nth-child(1)').addClass('trunkShow-image');
			$j('.trunk-show').children(':nth-child(2)').addClass('trunkShow-name');
			$j('.trunk-show').children(':nth-child(3)').addClass('trunkShow-endDate');
		});
	}
});