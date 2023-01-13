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

  function plugin_livecall_install() {
    global $DB;
    
    //instanciate migration with version
    $migration = new Migration(100);
    
    //Create table only if it does not exists yet!
    if (!$DB->tableExists('glpi_plugin_livecall_sets')) {
      //table creation query
      $query_0 = "CREATE TABLE IF NOT EXISTS `glpi_plugin_livecall_sets` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `javascript` TEXT NOT NULL,
        `enabled` TINYINT(1) NOT NULL,
        `target` TEXT NOT NULL,
        PRIMARY KEY  (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
      $DB->query($query_0) or die("error creating table glpi_plugin_livecall_sets " . $DB->error());
      
      $query_1 = ['javascript' => '               
          (function(w, d, s, u) {
            w.RocketChat = function(c) { w.RocketChat._.push(c) }; w.RocketChat._ = []; w.RocketChat.url = u;
            var h = d.getElementsByTagName(s)[0], j = d.createElement(s);
            j.async = true; j.src = "http://chat.inumio.com:3000/livechat/rocketchat-livechat.min.js?";
            h.parentNode.insertBefore(j, h);
          })(window, document, "script", "http://chat.inumio.com:3000/livechat");',
        'enabled' => 0,
        'target' => 'Set de Group of Destination'
        ];
      $DB->insert('glpi_plugin_livecall_sets', $query_1) or die("error setting default data on database" . $DB->error());
    }
    
    //execute the whole migration
    $migration->executeMigration();

    return true;
  }
  
  function plugin_livecall_uninstall(){
      global $DB;
      
      $tables = [
        'sets'
      ];
      
      foreach ($tables as $table) {
        $tablename = 'glpi_plugin_livecall_' . $table;
        if ($DB->tableExists($tablename)) {
          $DB->queryOrDie(
            "DROP TABLE `$tablename`",
            $DB->error()
          );
        }
      }
    return true;
  }

  function load_user_data() {
    global $CFG_GLPI;
    global $DB;
    $cookieValue = [
      'user_id' => $_SESSION['glpiID'],
      'name' => $_SESSION['glpiname'],
      'firstname' => $_SESSION['glpifirstname'],
      'profile' => $_SESSION['glpiactiveprofile']['name'],
    ];
    $query_0 = ['FROM'  => 'glpi_users', 'id' => $_SESSION['glpiID']];
    $result = $DB->request($query_0);
    if (count($result)) {
      foreach ($result as $data) {
        $cookieValue['locations_id'] = $data['locations_id'];
      }
    }
    $query_1 = ['FROM'  => 'glpi_useremails', 'users_id' => $_SESSION['glpiID']];
    $result = $DB->request($query_1);
    if (count($result)) {
      foreach ($result as $data) {
        $cookieValue['email'] = $data['email'];
      }
    }
    $query_2 = ['FROM'  => 'glpi_locations', 'locations_id' => $cookieValue['locations_id']];
    $result = $DB->request($query_2);
    if (count($result)) {
      foreach ($result as $data) {
        $cookieValue['location'] = $data['completename'];
      }
    }
    $newCookie = [];
    foreach ($cookieValue as $key=>$cookie) {
      if (!isset($newCookie[$key])) {
        if ($key !== 'locations_id' || $key !== 'user_id') {
          $newCookie[$key] = $cookie;
        }
      }
    }
    setcookie('livecall-cookie', json_encode($newCookie));
    return true;
  }
