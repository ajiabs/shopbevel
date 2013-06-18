
/*
 * clickclear
 * simple placeholder plugin
 * @author Hanam Le @ Pixafy
 * @version: 0.1 (6/14/2012)
 */
;(function(jQuery) {
    "use strict";

    jQuery.fn.clickclear = function() {

        // iterate through each element
        this.each(function(index) {

            // set default value to placeholder if needed
            if (jQuery(this).val() == "") {
                jQuery(this).val(jQuery(this).attr("placeholder"));
            }

            // add click event to remove placeholder
            jQuery(this).on("focus", null, function(e) {
                if (jQuery(this).val() == jQuery(this).attr("placeholder")) {
                    jQuery(this).val("");
                }
            });

            // add blur event to add in placeholder if needed
            jQuery(this).on("blur", null, function(e) {
                if (jQuery(this).val() == "") {
                    var placeholderText = jQuery(this).attr("placeholder");
                    jQuery(this).val(placeholderText);
                }
            });
        });
    };
})(jQuery);
