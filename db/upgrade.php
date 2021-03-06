<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// This file keeps track of upgrades to
// the flashcards module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

defined('MOODLE_INTERNAL') || die();

function xmldb_flashcards_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2016080200) {

        // Define field skipglobal to be added to flashcards.
        $table = new xmldb_table('flashcards');
        $field = new xmldb_field('skipglobal', XMLDB_TYPE_INTEGER, '1', null, null, null, '1', 'timemodified');

        // Conditionally launch add field skipglobal.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Flashcards savepoint reached.
        upgrade_mod_savepoint(true, 2016080200, 'flashcards');
    }

    if ($oldversion < 2016080500) {

        // Define field finishedscattermsg to be added to flashcards.
        $table = new xmldb_table('flashcards');
        $field = new xmldb_field('finishedscattermsg', XMLDB_TYPE_TEXT, null, null, null, null, null, 'skipglobal');

        // Conditionally launch add field finishedscattermsg.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

         // Define field completedmsg to be added to flashcards.
        $table = new xmldb_table('flashcards');
        $field = new xmldb_field('completedmsg', XMLDB_TYPE_TEXT, null, null, null, null, null, 'finishedscattermsg');

        // Conditionally launch add field completedmsg.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Flashcards savepoint reached.
        upgrade_mod_savepoint(true, 2016080500, 'flashcards');
    }

    return true;
}