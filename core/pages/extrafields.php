<?php
/**
 * Dolibase
 * 
 * Open source framework for Dolibarr ERP/CRM
 *
 * Copyright (c) 2018 - 2019
 *
 *
 * @package     Dolibase
 * @author      AXeL
 * @copyright	Copyright (c) 2018 - 2019, AXeL-dev
 * @license
 * @link
 * 
 */

dolibase_include_once('/core/class/page.php');
include_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
include_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';

/**
 * ExtraFieldsPage class
 */

class ExtraFieldsPage extends Page
{
	/**
	 * @var string element type
	 */
	protected $elementtype;


	/**
	 * Constructor
	 * 
	 * @param     $elementtype    Must be the $table_element of the class that manage extrafield
	 * @param     $page_title     HTML page title
	 * @param     $access_perm    Access permission
	 */
	public function __construct($elementtype, $page_title = 'ExtraFields', $access_perm = '$user->admin')
	{
		global $langs;

		// Load lang files
		$langs->load("admin");

		// Set attributes
		$this->elementtype = $elementtype;

		parent::__construct($page_title, $access_perm);
	}

	/**
	 * Generate page body
	 *
	 */
	protected function generate()
	{
		global $langs, $dolibase_config;

		// Add sub title
		$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php?mainmenu=home">'.$langs->trans("BackToModuleList").'</a>';
		$this->addSubTitle($this->title, 'title_generic.png', $linkback);

		// Add default tabs
		if (empty($this->tabs)) {
			$this->addTab("Settings", "/".$dolibase_config['module']['folder']."/admin/".$dolibase_config['other']['setup_page']."?mainmenu=home");
			$this->addTab("ExtraFields", "/".$dolibase_config['module']['folder']."/admin/extrafields.php?mainmenu=home", true);
			$this->addTab("About", "/".$dolibase_config['module']['folder']."/admin/".$dolibase_config['other']['about_page']."?mainmenu=home");
		}

		parent::generate();
	}

	/**
	 * Generate tabs
	 *
	 * @param     $noheader     -1 or 0=Add tab header, 1=no tab header.
	 */
	protected function generateTabs($noheader = -1)
	{
		parent::generateTabs($noheader);
	}

	/**
	 * Generate page begining
	 *
	 */
	public function begin()
	{
		global $db, $conf, $langs;

		// Get parameters
		$action      = GETPOST('action', 'alpha');
		$attrname    = GETPOST('attrname', 'alpha');
		$elementtype = $this->elementtype;

		// List of supported format
		$tmptype2label = ExtraFields::$type2label;
		$type2label    = array('');
		foreach ($tmptype2label as $key => $val) {
			$type2label[$key] = $langs->transnoentitiesnoconv($val);
		}

		// Init objects
		$extrafields = new ExtraFields($db);
		$form        = new Form($db);

		// Actions
		include_once DOL_DOCUMENT_ROOT.'/core/actions_extrafields.inc.php';

		// Page begin
		parent::begin();

		// Extrafields view/table
		include_once DOL_DOCUMENT_ROOT.'/core/tpl/admin_extrafields_view.tpl.php';

		// Buttons
		if ($action != 'create' && $action != 'edit')
		{
			echo '<div class="tabsAction">';
			echo '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=create">'.$langs->trans("NewAttribute").'</a></div>';
			echo '</div>';
		}

		// Add form
		if ($action == 'create')
		{
			echo '<br>';
			echo load_fiche_titre($langs->trans('NewAttribute'));

			include_once DOL_DOCUMENT_ROOT.'/core/tpl/admin_extrafields_add.tpl.php';
		}

		// Edit form
		if ($action == 'edit' && ! empty($attrname))
		{
			echo '<br>';
			echo load_fiche_titre($langs->trans("FieldEdition", $attrname));

			include_once DOL_DOCUMENT_ROOT.'/core/tpl/admin_extrafields_edit.tpl.php';
		}
	}
}