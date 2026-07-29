<?php

namespace Modules\Journals\lang\en;

return [
    // Common / Actions
    'journals' => 'Journals',
    'journal' => 'Journal',
    'journals_list' => 'List of Journals',
    'add_journal' => 'Add Journal',
    'add_new_journal' => 'Add New Journal',
    'edit_journal' => 'Edit Journal',
    'journal_details' => 'Journal Details',
    'search' => 'Search journals...',
    'actions' => 'Actions',
    'save' => 'Save Journal',
    'update' => 'Update Journal',
    'cancel' => 'Cancel',
    'back' => 'Back to List',
    'export' => 'Export',
    'delete_selected' => 'Delete Selected',
    'status' => 'Status',
    'active' => 'Active',
    'inactive' => 'Inactive',

    // Fields
    'name' => 'Journal Name',
    'slug' => 'Slug',
    'short_name' => 'Short Name / Abbreviation',
    'issn_p' => 'P-ISSN (Print)',
    'issn_e' => 'E-ISSN (Online)',
    'description' => 'Description',
    'scope' => 'Focus & Scope',
    'guidelines' => 'Author Guidelines',
    'publication_ethics' => 'Publication Ethics',
    'enter_name' => 'e.g. Journal of Genomics and Health',
    'enter_short_name' => 'e.g. JGH',
    'enter_issn_p' => 'e.g. 2345-6789',
    'enter_issn_e' => 'e.g. 2987-6543',

    // Editorial Boards
    'editorial_board' => 'Editorial Board',
    'add_editorial_member' => 'Add Team Member',
    'edit_editorial_member' => 'Edit Team Member',
    'member_name' => 'Member Name',
    'role' => 'Editorial Role',
    'affiliation' => 'Affiliation / Institution',
    'email' => 'Email',
    'order_no' => 'Display Order',
    'select_user' => 'Select Registered User (Optional)',

    // Roles
    'editor_in_chief' => 'Editor-in-Chief',
    'managing_editor' => 'Managing Editor',
    'section_editor' => 'Section Editor',
    'editorial_board_member' => 'Editorial Board Member',
    'advisory_board' => 'International Advisory Board',

    // Messages
    'created_success' => 'Journal created successfully.',
    'updated_success' => 'Journal updated successfully.',
    'deleted_success' => 'Journal deleted successfully.',
    'cannot_delete_has_relations' => 'Cannot delete journal because it has active issues, submissions, or editorial board records.',
    'cannot_delete_member_has_relations' => 'Cannot delete team member due to active assignments.',
    'are_you_sure' => 'Are you sure?',
    'delete_confirm_text' => 'This action will soft-delete the selected items!',
    'yes_delete' => 'Yes, delete it!',
    'yes_delete_selected' => 'Yes, delete selected!',
    'notice' => 'Notice',
    'no_eligible_items' => 'No eligible items selected for deletion.',
    'deleted' => 'Deleted!',
    'error' => 'Error!',
];
