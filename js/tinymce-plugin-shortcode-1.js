/**
 * TinyMCE plugin for the shortcode "Tabulka společností".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-compare_filter_search
 */

(function() {
    tinymce.PluginManager.add('odwpcfs_shortcode_1', function( editor, url ) {
        editor.addButton( 'odwpcfs_shortcode_1', {
            //text: 'Tabulka společností',
            tooltip: 'Tabulka společností',
            icon: 'icon odwpcfs_shortcode_1-icon',
            onclick: function() {
                editor.windowManager.open( {
                    title: 'Vložte tabulku společností',
                    body: [{
                        type: 'textbox',
                        name: 'title',
                        label: 'Název',
                        value: 'Tabulka společností'
                    }, {
                        type: 'checkbox',
                        name: 'show_title',
                        label: 'Zobrazit název?'
                    }, {
                        type: 'checkbox',
                        name: 'show_filter',
                        label: 'Zobrazit filter společností?',
                        checked: true
                    }, {
                        type: 'textbox',
                        subtype: 'number',
                        name: 'posts_per_page',
                        label: 'Počet',
                        description: 'Počet položek na stránku.',
                        value: 5
                    }],
                    onsubmit: function( e ) {
                        var ret = '[tabulka-spolecnosti';
                        if( e.data.title != "" ) {
                            ret = ret + ' title="' + e.data.title + '"';
                        }
                        ret = ret + ' show_title="' + ( e.data.show_title === true ? '1' : '0' ) + '"';
                        ret = ret + ' show_filter="' + ( e.data.show_filter === true ? '1' : '0' ) + '"';
                        var ppp = Number.parseInt( e.data.posts_per_page );
                        if( ppp <= 0 ) {
                            ppp = 5;
                        }
                        ret = ret + ' posts_per_page="' + ppp + '"]';
                        editor.insertContent(ret);
                    }
                });
            }
        });
    });
})();