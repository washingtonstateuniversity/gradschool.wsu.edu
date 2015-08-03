<?php

class WSU_Grad_Degrees {

	/**
	 * Setup hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_filter( 'template_include', array( $this, 'handle_degree_rewrite' ) );
	}

	/**
	 * Add a rewrite rule to handle the individual degree factsheetes that we pull from
	 * the source dynamically.
	 */
	public function rewrite_rules() {
		add_rewrite_rule( '^degrees/factsheet/([^/]*)/?','index.php?degree_id=$matches[1]', 'top' );
		add_rewrite_rule( '^degrees/?', 'index.php?degrees_load=1', 'top' );
		add_rewrite_rule( '^certificates/factsheet/([^/]*)/?', 'index.php?degree_id=$matches[1]', 'top' );
		add_rewrite_rule( '^certificates/?', 'index.php?certificates_load=1', 'top' );
	}

	/**
	 * Make WordPress aware of the degree_id query variable in our rewrite rule.
	 *
	 * @param array $query_vars Current list of query_vars passed.
	 *
	 * @return array Modified list of query_vars.
	 */
	function query_vars( $query_vars ) {
		$query_vars[] = 'degree_id';
		$query_vars[] = 'degrees_load';
		$query_vars[] = 'certificate_id';
		$query_vars[] = 'certificates_load';

		return $query_vars;
	}

	/**
	 * Help WordPress find templates for degrees and certificates when found.
	 *
	 * @param string $template Expected template.
	 *
	 * @return string Replacement template.
	 */
	public function handle_degree_rewrite( $template ) {
		if ( '' !== get_query_var( 'degree_id' ) ) {
			add_filter( 'spine_get_title', array( $this, 'degree_all_title' ), 15 );
			$new_template = locate_template( 'degree.php' );
			if ( '' !== $new_template ) {
				return $new_template;
			}
		} elseif ( 1 == get_query_var( 'degrees_load' ) ) {
			add_filter( 'spine_get_title', array( $this, 'degree_all_title' ), 15 );
			$new_template = locate_template( 'degree-all.php' );
			if ( '' !== $new_template ) {
				return $new_template;
			}
		} elseif ( 1 == get_query_var( 'certificates_load' ) ) {
			add_filter( 'spine_get_title', array( $this, 'certificate_all_title' ), 15 );
			$new_template = locate_template( 'certificate-all.php' );
			if ( '' !== $new_template ) {
				return $new_template;
			}
		}

		return $template;
	}

	/**
	 * Provide a static title to display when viewing degree programs.
	 *
	 * @return string
	 */
	public function degree_all_title() {
		return 'Degree Programs - Graduate School | Washington State University';
	}

	/**
	 * Provide a static title to display when viewing certificates.
	 * @return string
	 */
	public function certificate_all_title() {
		return 'Certificates - Graduate School | Washington State University';
	}

	/**
	 * Retrieve HTML from DOM element "content" on a requested page and parse accordingly.
	 *
	 * @param string $content_url URL to retrieve content from.
	 *
	 * @return string HTML output.
	 */
	public function get_content_html( $content_url ) {
		$degrees_raw = wp_remote_get( $content_url );
		$degrees_body = wp_remote_retrieve_body( $degrees_raw );

		$degrees_dom = new DOMDocument();
		libxml_use_internal_errors( true );
		$degrees_dom->loadHTML( $degrees_body );
		libxml_use_internal_errors( false );

		$degrees_html_xpath = new DOMXPath( $degrees_dom );

		// Find the DIV with a class attribute of "content"
		$degrees_content_nodes = $degrees_html_xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " content ")]');

		// Create a new document to store our content DIV
		$degree_content_dom = new DOMDocument();

		// Parse the DOM and grab all of the children of the main content DIV
		foreach( $degrees_content_nodes as $degree_content_node ) {
			foreach( $degree_content_node->childNodes as $degree_content_child_node ) {
				$new_degree_content = $degree_content_dom->importNode( $degree_content_child_node, true );
				$degree_content_dom->appendChild( $new_degree_content );
			}
		}

		$degree_content_html = $degree_content_dom->saveHTML();

		// Destroy our other DOM containers
		$degree_content_dom = null;
		$degrees_html = null;
		$degrees_html_xpath = null;

		$clean_degrees_dom = new DOMDocument();
		$clean_degrees_dom->loadHTML( $degree_content_html );

		$degrees_html_xpath = new DOMXPath( $clean_degrees_dom );

		// Query for and remove all inline styles.
		$degrees_html_nodes = $degrees_html_xpath->query( '//*[@style]' );
		foreach( $degrees_html_nodes as $degree_html_node ) {
			$degree_html_node->removeAttribute( 'style' );
		}

		// Find the DIV with a class attribute of "content"
		$degrees_html_nodes = $degrees_html_xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " one-third ")]');
		$x = 0;
		$columns = array( 'one', 'two', 'three' );
		foreach( $degrees_html_nodes as $degree_html_node ) {
			$degree_html_node->setAttribute( 'class', 'column ' . $columns[ $x ] );
			$x++;
		}

		$final_degrees_html = $clean_degrees_dom->saveHTML();
		$final_degrees_html = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $final_degrees_html );
		$final_degrees_html = str_replace( 'FactSheet/', 'factsheet/', $final_degrees_html );
		if ( substr( $final_degrees_html, 0, 5 ) ) {
			$final_degrees_html = '<div class="guttered">' . substr( $final_degrees_html, 5 );
		}

		return $final_degrees_html;
	}

	/**
	 * Retrieve the HTML for the list of certificates offered and store this information in cache.
	 *
	 * @return string HTML display of certificates.
	 */
	public function get_certificates_html() {
		$certificates_html = wp_cache_get( 'wsu_grad_certs_all' );

		if ( $certificates_html ) {
			return $certificates_html;
		}

		$final_certificate_html = $this->get_content_html( 'http://svr.gradschool.wsu.edu/FutureStudents/Certificates' );

		wp_cache_delete( 'wsu_grad_certs_all' );
		wp_cache_add( 'wsu_grad_certs_all', $final_certificate_html, '', HOUR_IN_SECONDS );

		return $final_certificate_html;
	}

	/**
	 * Retrieve the HTML for the list of degrees offered and store this information in cache.
	 *
	 * @return string HTML display of certificates.
	 */
	public function get_degrees_html() {
		$degrees_html = wp_cache_get( 'wsu_grad_degrees_all' );

		if ( $degrees_html ) {
			return $degrees_html;
		}

		$final_degrees_html = $this->get_content_html( 'http://svr.gradschool.wsu.edu/FutureStudents/Degrees' );

		wp_cache_delete( 'wsu_grad_degrees_all' );
		wp_cache_add( 'wsu_grad_degrees_all', $final_degrees_html, '', 3600 );

		return $final_degrees_html;
	}

	/**
	 * Retrieve information about a single requested degree. Store this information in cache for
	 * at least a day.
	 *
	 * @param int $degree ID of the degree factsheet being requested.
	 *
	 * @return string The HTML to be output for the degree.
	 */
	public function get_degree_html( $degree ) {
		$degree = absint( $degree );

		$degree_html = wp_cache_get( 'wsu_grad_degree_' . $degree );

		if ( $degree_html ) {
			return $degree_html;
		}

		$degree_raw = wp_remote_get( 'http://svr.gradschool.wsu.edu/FutureStudents/FactSheet/' . $degree );
		$degree_body = wp_remote_retrieve_body( $degree_raw );

		$degree_dom = new DOMDocument();
		libxml_use_internal_errors( true );
		$degree_dom->loadHTML( $degree_body );
		libxml_use_internal_errors( false );

		$degree_html_xpath = new DOMXPath( $degree_dom );

		// Find the DIV with a class attribute of "content"
		$degree_content_nodes = $degree_html_xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " content ")]');

		// Create a new document to store our content DIV
		$degree_content_dom = new DOMDocument();

		// Parse the DOM and grab all of the children of the main content DIV
		foreach( $degree_content_nodes as $degree_content_node ) {
			foreach( $degree_content_node->childNodes as $degree_content_child_node ) {
				$new_degree_content = $degree_content_dom->importNode( $degree_content_child_node, true );
				$degree_content_dom->appendChild( $new_degree_content );
			}
		}

		$degree_content_html = $degree_content_dom->saveHTML();

		// Destroy our other DOM containers
		$degree_content_dom = null;
		$degrees_html = null;
		$degrees_html_xpath = null;

		$clean_degrees_dom = new DOMDocument();
		libxml_use_internal_errors( true );
		$clean_degrees_dom->loadHTML( $degree_content_html );
		libxml_use_internal_errors( false );
		
		$degrees_html_xpath = new DOMXPath( $clean_degrees_dom );

		// Query for and remove all inline styles.
		$degrees_html_nodes = $degrees_html_xpath->query( '//*[@style]' );
		foreach( $degrees_html_nodes as $degree_html_node ) {
			$degree_html_node->removeAttribute( 'style' );
		}

		$final_degrees_html = $clean_degrees_dom->saveHTML();
		$final_degrees_html = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $final_degrees_html );
		$final_degrees_html = str_replace( 'src="/Images/', 'src="' . get_stylesheet_directory_uri() . '/images/', $final_degrees_html );
		$final_degrees_html = str_replace( 'http://gradschool.wsu.edu/futurestudents/apply.html', home_url( '/apply/' ), $final_degrees_html );
		$final_degrees_html = trim( $final_degrees_html );

		$clean_degrees_dom = null;

		if ( empty( $final_degrees_html ) ) {
			error_log( 'gradschool.wsu.edu error loading ' . esc_url_raw( 'http://svr.gradschool.wsu.edu/FutureStudents/FactSheet/' . $degree ) );
			return '';
		}

		wp_cache_add( 'wsu_grad_degree_' . $degree, $final_degrees_html, '', 8 * HOUR_IN_SECONDS );

		return $final_degrees_html;
	}
}
$wsu_grad_degrees = new WSU_Grad_Degrees();