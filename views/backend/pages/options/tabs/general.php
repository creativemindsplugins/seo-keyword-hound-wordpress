<?php

use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\helpers;
?>

<h2>Post Type</h2>
<table class="form-table">
    <tr>
        <th scope="row"><label for="<?php echo models\Options::KEYWORDS_METABOX_SCREEN; ?>">Supported post types*</label></th>
        <td>
            <?php foreach ( get_post_types( array( 'public' => true ), 'objects', 'and' ) as $item ): ?>
                <?php echo '<label><input name="' . models\Options::KEYWORDS_METABOX_SCREEN . '[]" type="checkbox" ' . (in_array( $item->name, models\Options::getKeywordsMetaboxScreen() ) ? 'checked="checked"' : '') . ' value="' . esc_attr( $item->name ) . '" />' . esc_html( $item->labels->singular_name . ' (' . $item->name . ')' ) . '</label><br />'; ?>
            <?php endforeach; ?>
            <p class="description">
                Post types on which keywords metabox will be visible
            </p>
        </td>
    </tr>
 </table>

<h2>Keywords Metabox</h2>
<table class="form-table">
    <tr>
        <th scope="row"><label for="<?php echo models\Options::METABOX_GRID_HEIGHT; ?>">Metabox grid size</label></th>
        <td>
            <?php echo helpers\HTMLHelper::select( models\Options::METABOX_GRID_HEIGHT, models\Options::getMetaboxGridHeight(), models\Options::getAllMetaboxGridHeightAssoc() ); ?>
            <p class="description">
                Maximum height of grids in plugin metabox
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="<?php echo models\Options::METABOX_GRID_DISPLAY_DENSITY; ?>">Metabox grid density</label></th>
        <td>
            <?php echo helpers\HTMLHelper::select( models\Options::METABOX_GRID_DISPLAY_DENSITY, models\Options::getMetaboxGridDisplayDensity(), models\Options::getAllMetaboxGridDisplayDensityAssoc() ); ?>
            <p class="description">
                Metabox grid display density
            </p>
        </td>
    </tr>
   <tr>
        <th scope="row"><label>Keywords statistics table columns*</label></th>
        <td>
            <?php foreach ( models\Options::getAllStatTableColumnsAssoc() as $k => $v ): ?>
                <?php
                if ( $k == 'marked' ) {
                    $v = 'Marked out (keywords in <code>STRONG</code>, <code>EM</code>, <code>B</code> and <code>I</code> tags)';
                }
                if ( $k == 'images' ) {
                    $v = 'Images (images with keywords in <code>ALT</code> and <code>TITLE</code> attributes)';
                }
                ?>
                <?php echo '<label><input name="' . models\Options::STAT_TABLE_COLUMNS . '[]" type="checkbox" ' . (in_array( $k, models\Options::getStatTableColumns() ) ? 'checked="checked"' : '') . ' value="' . esc_attr( $k ) . '" />' . $v . '</label><br />'; ?>
            <?php endforeach; ?>
            <p class="description">
                Columns visible in the keywords metabox statistics table
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="<?php echo models\Options::ALLOW_MULTIPLE_EXPANDED; ?>">Expand multiple alternative keyword lists</label></th>
        <td>
            <input type="checkbox" id="<?php echo models\Options::ALLOW_MULTIPLE_EXPANDED; ?>" onchange="jQuery( this ).next().val( this.checked ? 1 : 0 )" <?php echo models\Options::getIsAllowMultipleExpanded() ? 'checked' : ''; ?> />
            <input type="hidden" name="<?php echo models\Options::ALLOW_MULTIPLE_EXPANDED; ?>" value="<?php echo models\Options::getIsAllowMultipleExpanded(); ?>" />
            <label for="<?php echo models\Options::ALLOW_MULTIPLE_EXPANDED; ?>">Allow for multiple alternate keywords to be expanded at the same time</label>
            <p class="description">
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label>Show keyword alerts</label></th>
        <td>
            <label>
                <input type="checkbox" id="" onchange="jQuery( this ).next().val( this.checked ? 1 : 0 )" <?php echo models\Options::getKeywordMetaboxWarningNotFoundEnabled() ? 'checked="checked"' : '' ?> />
                <input type="hidden" name="<?php echo models\Options::KEYWORDS_METABOX_WARNING_NOT_FOUND_ENABLED; ?>" value="1" />
                Keyword not found
            </label>
            <br />
            <label>
                <input type="checkbox" id="" onchange="jQuery( this ).next().val( this.checked ? 1 : 0 )" <?php echo models\Options::getKeywordMetaboxWarningDensityEnabled() ? 'checked="checked"' : '' ?> />
                <input type="hidden" name="<?php echo models\Options::KEYWORDS_METABOX_WARNING_DENSITY_ENABLED; ?>" value="1" />
                Density warning
            </label>
            <p class="description">
                Keyword warning icon visible in the keyword meta box. The icon will appear like this: <span style="color: #d54e21;" class="dashicons dashicons-warning"></span>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="<?php echo models\Options::IS_KEYWORD_CHANGELOG; ?>">Keyword changelog</label></th>
        <td>
            <input type="checkbox" id="<?php echo models\Options::IS_KEYWORD_CHANGELOG; ?>" onchange="jQuery( this ).next().val( this.checked ? 1 : 0 )" <?php echo models\Options::getIsKeywordChangelog() ? 'checked' : ''; ?> />
            <input type="hidden" name="<?php echo models\Options::IS_KEYWORD_CHANGELOG; ?>" value="<?php echo models\Options::getIsKeywordChangelog(); ?>" />
            <label for="<?php echo models\Options::IS_KEYWORD_CHANGELOG; ?>">Enable keyword changelog</label>
            <p class="description">
                All keyword events will automatically be saved as a new entry to the keyword note
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="<?php echo models\Options::METABOX_GRID_KEYWORD_NOTE_COLORS_LABELS; ?>">Keyword note labels</label></th>
        <td>
            <?php
            $colors = models\Options::getKeywordsColors();
            foreach ( $colors as $key => $color ) :
                ?>
                <p>
                    <span class="color-sample" style="background-color:<?php echo esc_attr( $color ); ?>"></span>
                    <?php echo helpers\HTMLHelper::input( models\Options::METABOX_GRID_KEYWORD_NOTE_COLORS_LABELS . '[]', models\Options::getKeywordsColorsLabels( $key ), ['class' => 'medium-text' ] ); ?>
                </p>
            <?php endforeach; ?>
            <p class="description">
                Color labels for the keyword notes
            </p>
        </td>
    </tr>
</table>
<p class="submit">
    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    <br />
    <small>* &mdash; required fields</small>
</p>
<?php wp_enqueue_style( 'wp-color-picker' ); ?>
<?php wp_enqueue_script( 'wp-color-picker' ); ?>
<script type="text/javascript">
    ( function ( $ ) {
        $( function () {
            $( '.cmsk-input-color' ).wpColorPicker();
        } );
    }( jQuery ) );
</script>
<style>
    .color-sample{
        display: inline-block;
        width: 21px;
        height: 21px;
        vertical-align: text-bottom;
        padding: 0;
        margin: 0;
    }
</style>