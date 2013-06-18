;(function($)
{
	$(function()
	{
		$("#add-image").click(function()
		{
			$j("[name='entry-image[]']:last").after('<input id="image" name="entry-image[]" value="" type="file" class="input-file">')
		});
	});
})(jQuery);