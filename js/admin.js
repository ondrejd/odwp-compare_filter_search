/**
 * JavaScripts for WP administration.
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-compare_filter_search
 */

jQuery(document).on("ready", function(e) {
    var el = jQuery("#odwpcfs-metabox-1-input");
    var onRatingInputChange = function() {
        var val = Number.parseInt(el.val());
        if (val < 0) {
            el.val(0);
            val = 0;
        }
        if (val > 100) {
            el.val(100);
            val = 100;
        }
        jQuery("#odwpcfs-metabox-1-stars").css("width", val + "%");
    };
    el.on("change", onRatingInputChange);
    onRatingInputChange();
});