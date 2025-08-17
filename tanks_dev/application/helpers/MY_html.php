<?php defined('SYSPATH') or die('No direct script access.');
/**
 * HTML helper Class Overload
 *
 * This html class is a static helper class designed to assist with the production of HTML elements of most kinds.
 *
 * This is a customized extension/overload of the html helper class.
 *
 * @package onestop
 * @subpackage helpers
 * @author Min Lee
 */

class html extends html_Core {
	public static function h($str, $flag=NULL) {
		if (! $flag) $flag = 1|2|0; // ENT_QUOTES|ENT_HTML401 - constants not defined?
		return(htmlspecialchars($str, $flag));
	}


	public static function tabular_js($rows, $config) {
		static $tabular_idx = 0;
		$tabular_id = 'tabular' . $tabular_idx;

		$headers = (isset($config['headers']) ? $config['headers'] : array());
		$row_func = (isset($config['row_func']) ? $config['row_func'] : 'html::display_row_default');
		$filter_cols_html = (isset($config['filter_cols']) ? implode($config['filter_cols'], ',') : array());
	?>
		<table id="<?= $tabular_id ?>" class="display">
			<thead>
				<tr>
				<?php
					foreach($headers as $header)
						echo("<th>{$header}</th>");
				?></tr>
			</thead>
			<tbody>
				<?= array_reduce($rows, $row_func); ?>
			</tbody>
		</table>

		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				<?= $tabular_id ?>_obj = $('#<?= $tabular_id ?>').dataTable({
					"bJQueryUI": true,
					"sPaginationType": "full_numbers"
				});
			} );
		</script>
	<?php
		$tabular_idx++;
	}


	public static function display_row_default($result, $row) {
		if ($result == NULL) $result = '';
		$result .= '<tr>';

		foreach($row as $cell)
			$result .= "<td>{$cell}</td>";

		$result .= '</tr>';
		return($result);
	}

	
	public static function horiz_table_tr($label, $data, $escape_html=TRUE, $classes=array('th'=>'')) {
		$data = ($escape_html ? nl2br(htmlspecialchars($data)) : $data);
		return("<tr><th class='{$classes['th']}'>{$label}:</th><td class='ui-widget-content'>{$data}</td></tr>");
	}

	public static function horiz_table_tr_form($label, $data, $is_required=FALSE) {
		$classes = array('th' => ($is_required ? 'required' : ''));
		return html::horiz_table_tr($label, $data, FALSE, $classes);
	}

	public static function table_foot_info($row) {
		$left_info = (isset($row['USER_CREATED']) ? "Created By: {$row['USER_CREATED']}" : '')
			. (isset($row['USER_MODIFIED']) ? "<br />Modified By: {$row['USER_MODIFIED']}" : '');

		$right_info = (isset($row['DATE_CREATED']) ? "Created On: {$row['DATE_CREATED']}" : '')
			. (isset($row['DATE_MODIFIED']) ? "<br />Modified On: {$row['DATE_MODIFIED']}" : '');
		return("<tfoot><tr><td colspan='2'>
			<div class='left_float'>{$left_info}</div>
			<div class='right_float'>{$right_info}</div>
			</td></tr></tfoot>");
	}

	/**
	 * Displays drop-down site navivation menu. Only works 2 levels deep.
	 **/
	public static function nav_menu($nav_menu, $nav_id) {
		$result = '<ul class="nav">';
		foreach($nav_menu as $name => $nav_item) {
			if (isset($nav_item['children']))
				$result .= "<li><a href='{$nav_item['url']}'"
					. (html::_has_selected($name, $nav_item['children'], $nav_id) ? 'class="selected"' : '')
					. ">{$nav_item['title']}</a>"
					. html::nav_menu($nav_item['children'], $nav_id) . '</li>';
			elseif (! isset($nav_item['hidden']))
				$result .= html::_nav_link($nav_item['title'], $nav_item['url'], html::_has_selected($name, $nav_item, $nav_id));
		}
		return($result . '</ul>');
	}

	/**
	 * Only used by html::nav_menu
	 **/
	private static function _nav_link($title, $link, $is_selected) {
		return("<li><a href='{$link}'"
			. ($is_selected ? 'class="selected"' : '')
			. ">{$title}</a></li>");
	}

	/**
	 * Only used by html::nav_menu
	 **/
	private static function _has_selected($name, $nav_item, $nav_id) {
		if ($name == $nav_id) return(TRUE);
		return(array_key_exists($nav_id, $nav_item));  // not recursive search
	}	

	///////////////////////////////////////////////////////////////////
	// Onestop specific functions
	///////////////////////////////////////////////////////////////////

	/**
	 * Display all rows retrieved from entity_details
	 **/
	public static function entity_details_tr($label, $entity_type, $entity_id, $detail_type) {
		$entity_details = new Entity_details_Model();
		$rows = $entity_details->get_list_by_entity($entity_type, 'ENTITY_ID = :ENTITY_ID and DETAIL_TYPE = :DETAIL_TYPE', NULL, array(':ENTITY_ID' => $entity_id, ':DETAIL_TYPE' => $detail_type));

		if (count($rows)) {
			$html = '';
			foreach($rows as $row)
				$html .= html::horiz_table_tr($label,
					"{$row['DETAIL_VALUE']} <div class='popup' style='float:right'>" . Controller::_instance('Entity_details')->_edit_button($row['ID']) . "</div>"
					, FALSE);
		}
		else {
			$html = html::horiz_table_tr($label,
				"<div class='popup' style='float:right'>" . Controller::_instance('Entity_details')->_add_button(array($entity_id, $entity_type, $detail_type), 'add') . "</div>"
				, FALSE);
		}

		return($html);
	}

	public static function owner_link($id) {
		if (! $id) return('');

		$owner_row = Model::instance('Owners_mvw')->get_row($id);
		if ($owner_row) {
			$url = Controller::_instance('Owner')->_view_url($id);
			return("<a href='{$url}'>({$id}) {$owner_row['OWNER_NAME']}</a>");
		}
		else {
			return('Not in DB');
		}
	}

	public static function facility_link($id) {
		if (! $id) return('');

		$facility_row = Model::instance('Facilities_mvw')->get_row($id);
		if ($facility_row) {
			$url = Controller::_instance('Facility')->_view_url($id);
			return("<a href='{$url}'>({$id}) {$facility_row['FACILITY_NAME']}</a>");
		}
		else {
			return('Not in DB');
		}
	}

	public static function operator_link($id) {
		if (! $id) return('');

		$operator_row = Model::instance('Operators_mvw')->get_row($id);
		if ($operator_row) {
			$url = Controller::_instance('Operator')->_view_url($id);
			return("<a href='{$url}'>({$id}) {$operator_row['OPERATOR_NAME']}</a>");
		}
		else {
			return('Not in DB');
		}
	}

	/**
	 * Creates breadcrumb navigation feature using controller->name,
	 * controller->prev_name, and controller->model_name
	 **/
	public static function breadcrumbs($uri_args) {
		$parents = html::get_parents($uri_args);
		if (count($parents) > 1) // if traversed more than 1 deep
			return(implode(array_reverse(array_column($parents, 'link')), ' &gt; '));
		else
			return('');
	}

	public static function get_parents($uri_args, $parent_name=NULL) {
		$ids = array_values($uri_args);
		foreach($ids as $j => $id) // clean ids just in case
			$ids[$j] = url::kohana_decode($id);

		$controller = Controller::_instance(ucfirst(Router::$controller));
		if (!$controller->name || !$controller->model_name)
			return(array());

		// loop through controllers in reverse starting with current
		$path = array(array('name' => $controller->name,
			'ids' => $ids,
			'link' => $controller->_friendly_name()));
		$is_first = TRUE;
		while($prev_name = $controller->prev_name) {
			$child_model = $controller->_model_instance();
			$controller = Controller::_instance(ucfirst($prev_name));
			if ((Router::$method == 'add') && ($is_first))  // 'add' $ids are parent's
				$parent_ids = $ids;
			else
				$parent_ids = $child_model->parent_ids($ids);

			if ($parent_ids) {
				$crumb = array('name' => $prev_name,
					'ids' => $parent_ids,
					'link' => "<a href='{$controller->_view_url($parent_ids)}'>{$controller->_friendly_name()}</a>");
			}
			else { // no parent-child relationship exists in model
				$crumb = array('name' => $prev_name,
					'ids' => NULL,
					'link' => $controller->_friendly_name());
			}

			$path[] = $crumb;
			$is_first = FALSE;
		}

		if ($parent_name) {
			foreach($path as $object) {
				if ($object['name'] == $parent_name)
					return($object);
			}
			return(array());
		}
		else
			return($path);
	}
}
