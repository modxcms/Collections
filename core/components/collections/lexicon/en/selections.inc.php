<?php
/**
 * Default English Lexicon Entries for Collections
 *
 * @package collections
 * @subpackage lexicon
 */

// System lexicons
$_lang['selections.system.text_create_here'] = 'Selection';
$_lang['selections.system.text_create'] = 'Selection';
$_lang['selections.system.type_name'] = 'Selection';
$_lang['selections.system.new_container'] = 'New Selection';

// Selection
$_lang['selections.create'] = 'Link resource';
$_lang['selections.resource'] = 'Resource';
$_lang['selections.view'] = 'View resource';
$_lang['selections.edit'] = 'Update resource';
$_lang['selections.publish'] = 'Publish resource';
$_lang['selections.unpublish'] = 'Unpublish resource';
$_lang['selections.duplicate'] = 'Duplicate resource';
$_lang['selections.delete'] = 'Delete resource';
$_lang['selections.delete_confirm'] = 'Are you sure, that you want to delete this Resource?';
$_lang['selections.undelete'] = 'Undelete resource';
$_lang['selections.unlink'] = 'Unlink resource';
$_lang['selections.unlink_confirm'] = 'Are you sure, that you want to unlink this Resource?  ';
$_lang['selections.unlink_action'] = 'Unlink';
$_lang['selections.delete_multiple'] = 'Delete selected';
$_lang['selections.delete_multiple_confirm'] = 'Are you sure, that you want to delete all selected Resources?';
$_lang['selections.remove'] = 'Remove resource';
$_lang['selections.remove_confirm'] = 'Are you sure, you want to remove this Resource?<br /><strong>This operation is permanent and can\'t be reverted.</strong>';
$_lang['selections.resource'] = 'Resource';
$_lang['selections.unlink_multiple'] = 'Unlink multiple';

// Errors
$_lang['collection.err.selection_resource_children'] = 'Unfortunatelly it\'s not possible to make this view as a Selection, following resource has children: <br /> [[+resources]]';
$_lang['collection.err.selection_resources_children'] = 'Unfortunatelly it\'s not possible to make this view as a Selection, following resources have children: <br /> [[+resources]]';
$_lang['collections.err.selection_res_col_ns'] = 'Resource or Collection was not specified.';
$_lang['collections.err.cant_set_parent_selection'] = 'You can\'t set Selection as a parent resource.';
$_lang['collections.err.cant_switch_to_selection_children'] = 'You can\'t switch this Resource to Selection, because it has children.';
$_lang['collections.err.cant_switch_from_selection_linked'] = 'You can\'t switch this Resource from Selection, because it has linked Resources.';