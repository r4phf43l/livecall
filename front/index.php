<?php

/**
 * -------------------------------------------------------------------------
 * LiveCall a Chat Plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation files
 * (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge,
 * publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 * 
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2023 by Rafael Antonio (r4phf43l).
 * @license   MIT https://opensource.org/licenses/MIT
 * @link      https://github.com/r4phf43l/livecall
 * -------------------------------------------------------------------------
 */

include ('../../../inc/includes.php');
Session::checkLoginUser();

$plugin = new Plugin();
if (!$plugin->isInstalled('livecall') || !$plugin->isActivated('livecall')) {
    Html::displayNotFoundError();
}

$object = new PluginLivecallSets();
$object->getFromDB(1);

if (Config::canView()) {
    Html::header(__('LiveCall','livecall'), $_SERVER['PHP_SELF'], 'admin', 'livecall');
    echo Html::displayTitle('','', __('LiveCall Settings','livecall'));
    echo '<div id=\'searchcriteria\'>';
    echo isset($success) && $success ? '<p><h3>Atualização realizada com sucesso</hr></p>' : '';
    echo '<form method=\'post\' action=\'index.form.php\'>';
    echo 'Enable ';
    Html::showCheckBox(['name' => 'enabled', 'checked' => $object->fields['enabled']]);
    echo '<br><br>Script';
    Html::textarea(['name' => 'javascript', 'enable_richtext' => false, 'value' => $object->fields['javascript']]);
    echo "Cookie function livecall().<br>Tags: name, firstname, profile, location, email.<br>";
    echo '<br>Available on Profile<br>';
    $query = ['FROM'  => 'glpi_profiles'];
    $result = $DB->request($query);
    $items = [];
    if (count($result)) {
        foreach ($result as $data) {
            $items[$data["id"]] = $data["name"];
        }
    }
    echo "<br>";
    echo Html::hidden('target', ['value' => $object->fields['target']]);
    echo Html::select(
        'target-list',
        $items,
        [
            'id'  => 'target-list',
            'selected' => explode(',', $object->fields['target']),
            'multiple' => true
        ]
    );
    echo "<script>
        let tgt = document.getElementsByName('target-list')[0];
        tgt.addEventListener('change', evt => {
            evt.preventDefault();
            let tgts = [...tgt.options]
                .filter(option => option.selected)
                .map(option => option.value);
        document.getElementsByName('target')[0].value = tgts.toString();
        });
    </script>";
    echo "<br>";echo "<br>";
    if (Config::canUpdate()) {        
        echo Html::hidden('id', ['value' => $object->fields['id']]);
        echo Html::hidden('update', ['value' => 'now']);
        echo Html::submit(__('Save','livecall'));
    }
    Html::closeform();
    echo '</div>';
    Html::footer();
} else {
    Html::displayRightError();
}
