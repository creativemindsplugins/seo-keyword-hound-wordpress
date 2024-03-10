<?php

use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\helpers;

$_06ba240e05dfaf902b9212d3504494e29b6f59dd = 'Be aware that statistics are only updated after post save &mdash; changing this option will not automatically update your post statistics';
?>
<p>Settings in this tab affect how keywords are analyzed and their statistics are displayed.</p>
<h2>Keywords Analysis</h2>
<table class="form-table">
    <tr>
        <th scope="row"><label>Headers *</th>
        <td>
            <?php foreach ( models\Options::getAllContentHeadersAssoc() as $k => $v ): ?>
                <?php echo '<label><input name="' . models\Options::CONTENT_HEADERS . '[]" type="checkbox" ' . (in_array( $k, models\Options::getContentHeaders() ) ? 'checked="checked"' : '') . ' value="' . esc_attr( $k ) . '" />' . esc_html( $v ) . '</label><br />'; ?>
            <?php endforeach; ?>
            <p class="description">
                Select which content headers will be included in the keyword analysis “Header” category
                <br />
                <?php echo $_06ba240e05dfaf902b9212d3504494e29b6f59dd; ?>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="<?php echo models\Options::FIRST_X_WORDS; ?>">First <code>X</code> words*</label></th>
        <td>
            <?php
            echo helpers\HTMLHelper::input( models\Options::FIRST_X_WORDS, models\Options::getFirstXWords(), [
                'type'     => 'number',
                'min'      => 1,
                'max'      => 9999,
                'step'     => 1,
                'required' => 'required',
                'class'    => 'small-text'
            ] );
            ?>
            <p class="description">
                The number of words at the beginning of your content. This will allow keyword analysis of this content as its own category
                <br />
                <?php echo $_06ba240e05dfaf902b9212d3504494e29b6f59dd; ?>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="<?php echo models\Options::DENSITY_THRESHOLD; ?>">Keyword density threshold*</label></th>
        <td>
            <?php
            echo helpers\HTMLHelper::input( models\Options::DENSITY_THRESHOLD, models\Options::getDensityThreshold(), [
                'type'     => 'number',
                'min'      => 0,
                'max'      => 100,
                'step'     => 1,
                'required' => 'required',
                'class'    => 'small-text'
            ] );
            ?> %
            <p class="description">
                Set the minimum threshold for a Keyword Density Warning to appear.
                <br />
                This setting takes into account only the page content and first <?php echo models\Options::getFirstXWords(); ?> words.
                <br />
                <?php echo $_06ba240e05dfaf902b9212d3504494e29b6f59dd; ?>
            </p>
        </td>
    </tr>
</table>
<p class="submit">
    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    <br />
    <small>* &mdash; required fields</small>
</p>