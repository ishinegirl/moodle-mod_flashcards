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
/**
 * Define all the backup steps that will be used by the backup_flashcards_activity_task
 *
 * @package   mod_flashcards
 * @category  backup
 * @copyright 2016 Your Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
/**
 * Define the complete flashcards structure for backup, with file and id annotations
 *
 * @package   mod_flashcards
 * @category  backup
 * @copyright 2016 Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_flashcards_activity_structure_step extends backup_activity_structure_step {
    /**
     * Defines the backup structure of the module
     *
     * @return backup_nested_element
     */
    protected function define_structure() {
        // Get know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');
        // Define the root element describing the flashcards instance.
        $flashcards = new backup_nested_element('flashcards', array('id'), array(
            'name', 'intro', 'introformat', 'localtermcount', 'globaltermcount', 'completionwhenfinish', 'timecreated', 'timemodified',
            'skipglobal', 'finishedscattermsg', 'completedmsg'));

        $terms = new backup_nested_element('terms');
        $term = new backup_nested_element('term', array('id'), array(
            'term', 'definition', 'deleted'));

        $seens = new backup_nested_element('seens');
        $seen = new backup_nested_element('seen', array('id'), array(
            'userid', 'timecreated'));

        $associations = new backup_nested_element('associations');
        $association = new backup_nested_element('association', array('id'), array(
            'userid', 'lastfail', 'lastsuccess', 'failcount', 'successcount'));

        $progresses = new backup_nested_element('progresses');
        $progress = new backup_nested_element('progress', array('id'), array(
            'userid', 'state', 'statedata'));

        // If we had more elements, we would build the tree here.
        $flashcards->add_child($terms);
        $terms->add_child($term);

        $term->add_child($seens);
        $seens->add_child($seen);

        $term->add_child($associations);
        $associations->add_child($association);

        $flashcards->add_child($progresses);
        $progresses->add_child($progress);

        // Define data sources.
        $flashcards->set_source_table('flashcards', array('id' => backup::VAR_ACTIVITYID));

        $term->set_source_sql('
            SELECT *
              FROM {flashcards_terms}
             WHERE modid = ?',
            array(backup::VAR_PARENTID));

        // All the rest of elements only happen if we are including user info
        if ($userinfo) {
            $seen->set_source_table('flashcards_seen', array('termid' => '../../id'));

            $association->set_source_table('flashcards_associations', array('termid' => '../../id'));

            $progress->set_source_sql('
            SELECT *
              FROM {flashcards_progress}
             WHERE modid = ?',
            array(backup::VAR_PARENTID));
        }

        // If we were referring to other tables, we would annotate the relation
        // with the element's annotate_ids() method.
        $seen->annotate_ids('user', 'userid');
        $association->annotate_ids('user', 'userid');
        $progress->annotate_ids('user', 'userid');

        // Define file annotations (we do not use itemid in this example).
        $flashcards->annotate_files('mod_flashcards', 'intro', null);

        // Return the root element (flashcards), wrapped into standard activity structure.
        return $this->prepare_activity_structure($flashcards);
    }
}
