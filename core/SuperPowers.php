<?php namespace SuperPowers;

require_once "SuperObject.php";

class SuperPowers extends SuperObject {

	public $applicationDirectory;
	public $url;

	private $typeId = null;
	private $subtypeId = null;

	protected $types       = array();
	protected $taxonomies  = array();

	/** @var  \SuperPowers\Controller\SuperTypeController */
	public $controller;
	public $version = "1.0.0";


	function __construct() {

		parent::__construct();

		$rootDirectory = dirname(WP_CONTENT_DIR);

		// Setup paths
		$this->applicationDirectory = "{$rootDirectory}/application";
		$this->directory            = WP_PLUGIN_DIR . "/wp-superpowers";
		$this->url                  = plugins_url('wp-superpowers');

		define('SUPERPOWERS_APPLICATION_DIR', $this->applicationDirectory);
		define('SUPERPOWERS_DIR', $this->directory);

	}

	/**
	 * Boot application
	 * - Register custom post types
	 * - Handle page context
	 */
	function boot() {

		$types = $this->config->get('types');

		$this->registerPostTypes($types);

		if (is_admin()) {
			add_action('admin_init', array(&$this, 'handleContext'));
			add_action('admin_enqueue_scripts', array(&$this, 'assets'));
			add_action('edit_page_form', function(){
				echo "<input type='hidden' name='subtype' value='{$this->subtypeId}'/>";
			});
			add_action('edit_form_advanced', function(){
				echo "<input type='hidden' name='subtype' value='{$this->subtypeId}'/>";
			});
		} else {
			add_action('template_include', array(&$this, 'handleContext'));
		}
	}

	/**
	 * Load plugin assets
	 */
	function assets(){
		wp_enqueue_style('superpowers.main', "{$this->url}/assets/build/styles/main.css", false, $this->version );
		wp_enqueue_script('superpowers.main', "{$this->url}/assets/build/scripts/main.min.js", array('superpowers.components'), $this->version );
		wp_enqueue_script('superpowers.components', "{$this->url}/assets/build/scripts/components.min.js", false, $this->version );
	}

	/**
	 * Register custom post types array
	 * @param array $types
	 */
	private function registerPostTypes(Array $types){

		foreach($types as $typeKey => $type){

			$this->registerPostType($typeKey, $type);
		}

		foreach($this->taxonomies as $tax){

			register_taxonomy($tax['id'], $tax['types'],
				array(
					'label' => $tax['label'],
					'rewrite' => array( 'slug' => $tax['id'] )
				)
			);
		}
	}

	/**
	 * Handle the current context based on $pagenow
	 */
	public function handleContext()
	{
		global $pagenow;

		switch($pagenow)
		{
			// List view
			case "edit.php":
				$this->loadController();
				break;

			// Post edit view
			case "post.php":
			case "post-new.php":
				$this->loadController();

				if($this->controller) {
					// Are we saving or editing?
					if (isset($_POST['action']) && $_POST['action'] == 'editpost') {
						$this->controller->save($_POST);
					} else {
						$this->editView();
					}
				}
				break;

			// Front view
			case "index.php":
				$this->loadController();
				if($this->controller){
					if($_SERVER['REQUEST_METHOD'] == 'GET'){
						$this->controller->render($_GET);
					} else if($_SERVER['REQUEST_METHOD'] == 'POST'){
						$this->controller->post($_POST);
					}
				}

				break;
			default:
				break;
		}
	}

	protected function editView()
	{

		if(empty($this->subtypeId) && $this->definition->typeHasSubtype($this->typeId)){
			// Render template page here
			$this->prepareCustomPreEditView();

			\add_meta_box('superPowersChooseSubTypeBox', __('Layout'), function($post)
			{
				$this->chooseTypeView($post);

			}, $this->typeId, "normal", "high");
		} else {
			$this->controller->editView();
		}
	}

	protected function prepareCustomPreEditView()
	{
		$supportToRemove = array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes');
		foreach ($supportToRemove as $support)
		{
			\remove_post_type_support($this->typeId, $support);
		}

		\remove_meta_box('submitdiv', $this->typeId, 'normal');
	}

	function chooseTypeView(\WP_Post $post){

		$def = $this->config->get("types.{$post->post_type}");

		$this->html->view('subtype.choose', array(
			'subtypes' =>  $def['subtypes'],
			'type' => ucfirst($post->post_type)
		));
	}


	/**
	 * Load type controller
	 * @return SuperTypeController
	 */
	public function loadController(){

		$data = $this->getCurrentPostData();

		// Load super controller
		$this->load->controllerFile('SuperTypeController');

		$this->typeId = $data->type;
		$this->subtypeId = $data->subtype;

		$typeClassName = ucfirst($data->type);

		if($this->subtypeId){
			$typeClassName .= '.' . ucfirst($this->subtypeId);
		}

		$this->controller = $this->load->controller($typeClassName);

		if($this->controller){
			$this->controller->load(strtolower($data->type), $data->subtype);
			if(!empty($data->postId)){
				$this->controller->setPost($data->postId);
			}

			if(!empty($data->subview)){
				$this->controller->setSubview($data->subview);
			}
		}

		return $data;
	}

	/**
	 * Get current context post data
	 * Type, subtype and postId
	 * @return object
	 */
	private function getCurrentPostData()
	{
		$postId = null;
		$type = null;
		$subtype = null;
		$subview = null;

		global $wp_query;

		if(!empty($wp_query->query_vars['subview']))
			$subview = $wp_query->query_vars['subview'];

		if(!empty($wp_query->query_vars['type'])) {

			$type = $wp_query->query_vars['type'];

			if(!empty($wp_query->query_vars[$type])) {
				$postId = $wp_query->query_vars[$type];
			}
		}

		if(empty($type)){

			// Type
			if (isset($_GET['post']))
			{
				$type = get_post_type($_GET['post']);
				$postId = intval($_GET['post']);
			}
			else if (isset($_POST['post_type']) && isset($_POST['post_ID']))
			{
				$type = $_POST['post_type'];
				$postId = intval($_POST['post_ID']);
			}
			else if (isset($_GET['post_type']))
			{
				$type = $_GET['post_type'];
			}
			else if (!isset($_GET['post_type']))
			{
				global $post;
				if($post){
					$postId = $post->ID;

					$type = get_post_type($postId);

				} else {
					$postId = null;
					$type = 'post';
				}
			}
		}



		// Subtype
		if (isset($_GET['subtype']))
		{
			$subtype = $_GET['subtype'];
		}
		else if (!empty($_POST['subtype']))
		{
			$subtype = $_POST['subtype'];
		}
		else if ($postId != null)
		{
			$subtype = $this->post->getSubtype($postId);

			// Ensure it's null...
			if (empty($subtype))
				$subtype = null;
		}

		return (object)array(
			'postId'    => $postId,
			'type'      => $type,
			'subtype'   => $subtype,
			'subview'   => $subview
		);
	}

	/**
	 * Register custom post type to wordpress
	 * @param string $id
	 * @param array $args
	 */
	private function registerPostType($id, Array $args) {
		$labels = array(
			'name'               => $args['label'],
			'singular_name'      => $args['label'],
			'menu_name'          => $args['label'] . "s",
			'name_admin_bar'     => $args['label'],
			'add_new'            => "Add New",
			'add_new_item'       => "Add New " . $args['label'],
			'new_item'           => "New " . $args['label'],
			'edit_item'          => "Edit " . $args['label'],
			'view_item'          => "View " . $args['label'],
			'all_items'          => "All " . $args['label'] . "s",
			'search_items'       => "Search " . $args['label'] . "s:",
			'parent_item_colon'  => "Parent " . $args['label'] . "s",
			'not_found'          => "No " . $args['label'] . "s found",
			'not_found_in_trash' => "No " . $args['label'] . "s found in Trash."
		);

		$defaults = array(
			'show_ui' => true,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-format-quote',
			'publicly_queryable' => true,
			'labels' => $labels,
			'hierarchical' => false,
			'rewrite' => array('slug' => strtolower($args['label']), 'with_front' => false),
		);

		// Fill with default args
		$args = wp_parse_args($args,$defaults);


		// Register post type
		if(!in_array($id, array('page', 'post', 'attachment'))){
			$type = register_post_type($id, $args);
		}

		//flush_rewrite_rules();

		// Register type to plugin
		$this->types[$id] = $args;

		// Register taxonomy
		if(!empty($args['taxonomy'])){

			foreach($args['taxonomy'] as $tax){

				if(!array_key_exists($tax['id'], $this->taxonomies)){
					$this->taxonomies[$tax['id']] = $tax;
					$this->taxonomies[$tax['id']]['types'] = array();
				}

				$this->taxonomies[$tax['id']]['types'][] = $id;
			}
		}
	}

	function redirect($path){
		$url = home_url() . "/$path";
		header("Location: {$url}");
		exit;
	}
}


