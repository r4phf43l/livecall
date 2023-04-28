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

define('PLUGIN_LIVECALL_VERSION', '1.0.0');

class PluginLiveCallConfig extends CommonDBTM {

  static protected $notable = true;
  
  static function getMenuName() {
    return __('LiveCall');
  }
  
  static function getMenuContent() {
    $menu = array();
    $menu['title']   = __('LiveCall Plugin','livecall');
    $menu['page']    = "/plugins/livecall/front/index.php";
    $menu['icon'] = 'fas fa-commenting';
    return $menu;
  }
}

/**
 * Init the hooks of the plugins - Needed
 *
 * @return void
 */

function plugin_init_livecall() {
   global $PLUGIN_HOOKS;
   $PLUGIN_HOOKS['csrf_compliant']['livecall'] = true;
   $PLUGIN_HOOKS['menu_toadd']['livecall'] = ['admin'  => 'PluginLiveCallConfig'];
   $PLUGIN_HOOKS['config_page']['livecall'] = 'front/index.php';

   $plugin = new Plugin();
   if ($plugin->isInstalled('livecall') && $plugin->isActivated('livecall')) {
    global $DB;
    global $CFG_GLPI;
    $query = ['FROM'  => 'glpi_plugin_livecall_sets'];
    // Verify settings, generate or update livecall.js and start script
    $result = $DB->request($query);
    if (count($result)) {
      foreach ($result as $data) {
        $target = explode(',', $data['target']);
        $profile = isset($_SESSION['glpiactiveprofile']['id']) ? in_array($_SESSION['glpiactiveprofile']['id'], $target) : false;
        if ($data['enabled'] == 1 && $profile == true) {
          $file = 'livecall.js';
          // $path = $_SERVER['DOCUMENT_ROOT'] . '/' . Plugin::getWebDir('livecall', false) . '/';
          $path = GLPI_ROOT . '/' . Plugin::getWebDir('livecall', false) . '/';
          if (!file_exists($path . $file)) {
            file_put_contents($path . $file, $data['javascript']);
          }
          $PLUGIN_HOOKS['post_init']['livecall'] = 'load_user_data';
          $PLUGIN_HOOKS['add_javascript']['livecall']['cookies'] = 'cookies.js';
          $PLUGIN_HOOKS['add_javascript']['livecall']['livecall'] = $file;
        }
      }
    }
   }
}

/**
 * Get the name and the version of the plugin - Needed
 *
 * @return array
 */
function plugin_version_livecall() {
  global $DB, $LANG;
        
  return array('name'     => 'LiveCall',
      'version'           => PLUGIN_LIVECALL_VERSION,
      'author'            => '<a href="mailto:rafael@rafaantonio.com.br"> Rafael Antonio </b> </a>',
      'license'           => 'MIT',
      'homepage'          => 'https://github.com/r4phf43l/livecall',
      'minGlpiVersion'    => '10'
  );
}

/**
 * Optional : check prerequisites before install : may print errors or add to message after redirect
 *
 * @return boolean
 */
function plugin_livecall_check_prerequisites() {
  if (GLPI_VERSION>=10) {
    return true;
  } else {
      echo 'GLPI version NOT compatible. Requires GLPI 10';
  }
}

/**
 * Check configuration process for plugin : need to return true if succeeded
 * Can display a message only if failure and $verbose is true
 *
 * @param boolean $verbose Enable verbosity. Default to false
 *
 * @return boolean
 */
function plugin_livecall_check_config($verbose = false) {
   if (true) { // Your configuration check
      return true;
   }

   if ($verbose) {
      echo "Installed, but not configured";
   }
   return false;
}

/**
 * Optional: defines plugin options.
 *
 * @return array
 */
function plugin_livecall_options() {
   return [
      Plugin::OPTION_AUTOINSTALL_DISABLED => true,
   ];
}
