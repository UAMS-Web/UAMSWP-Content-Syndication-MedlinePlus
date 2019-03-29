<?php
/**
 * A base class for UAMS medline syndicate shortcodes.
 *
 * Class UAMS_Syndicate_Shortcode_Medline
 */
class UAMS_Syndicate_Shortcode_Medline {
	/**
     * Instance of this class.
     *
     * @var      UAMS_Syndicate_Shortcode_Medline
     */
    private static $instance;
 
    /**
     * Initializes the plugin so that the dining information is appended to the end of a single post.
     * Note that this constructor relies on the Singleton Pattern
     *
     * @access private
     */
    public function __construct() {
		add_shortcode( 'uamswp_medline', array( $this, 'display_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_syndication_medline_stylesheet' ) );
		if ( class_exists('UAMS_Shortcakes') ) {
			add_action( 'admin_init', array( $this, 'build_shortcake' ) );
			add_action( 'enqueue_shortcode_ui', function() {
				// wp_enqueue_script( 'uams_syndications_editor_js', plugins_url( '/js/uams-medline-shortcake.js', __DIR__ ) );
			});
		}
	} // end constructor
	
	/**
	 * Enqueue styles specific to the network admin dashboard.
	 */
	public function enqueue_syndication_medline_stylesheet() {
		$post = get_post();
	 	if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'uamswp_medline' ) ) {
			// wp_enqueue_style( 'uamswp-syndication-medlinestyle', plugins_url( '/css/uamswp-syndication-medline.css', __DIR__ ), array(), '' );
			// wp_enqueue_style( 'uamswp-syndication-medline-lity', plugins_url( '/css/lity.min.css', __DIR__ ), array(), '' );
			// wp_enqueue_script( 'uamswp-syndication-medline-lity-js', plugins_url( '/js/lity.min.js', __DIR__ ), array(), '' );
		}
	}
 
    /**
     * Creates an instance of this class
     *
     * @access public
     * @return UAMS_Syndicate_Shortcode_Medline    An instance of this class
     */
    public function get_instance() {
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
        return self::$instance;
	} // end get_instance
	
	/**
	 * Enqueue styles specific to the network admin dashboard.
	 */
	public function enqueue_syndication_stylesheet_admin() {
		//add_editor_style( 'uamswp-syndication-dining-style-admin', plugins_url( '/css/uamswp-syndication-dining.css', __DIR__ ), array(), '' );
	}
	/**
	 * Build Shortcode-UI
	 */
	public function build_shortcake() {
		shortcode_ui_register_for_shortcode(
	 
			/** Your shortcode handle */
			'uamswp_medline',
			 
			/** Your Shortcode label and icon */
			array(
			 
			/** Label for your shortcode user interface. This part is required. */
			'label' => esc_html__('MedlinePlus Syndication', 'uamswp_medline'),
			 
			/** Icon or an image attachment for shortcode. Optional. src or dashicons-$icon.  */
			'listItemImage' => 'dashicons-book',
			 
			/** Shortcode Attributes */
			'attrs'          => array(
			 

				/** Location - ID */
				array(
					'label'        => esc_html__('Code ID', 'uamswp_medline'),
					'attr'         => 'code',
					'type'         => 'text',
					'description'  => 'ICD-10 Code',
				),

			 
			),
			 
			/** You can select which post types will show shortcode UI */
			'post_type'     => array( 'post', 'page', 'health-library' ), 
			)
		);
	}
 
	/**
	 * Display dining information for the [uamswp_medline] shortcode.
	 *
	 * [uamswp_medline loc=3 cat=7 type="list"]
	 * 
	 * @param array $atts
	 * 
	 * @return string
	 */
    public function display_shortcode( $atts ) {
		
		$attributes = (object) $atts;

		$code = '';
		if (isset($attributes->code)){
			$code = $attributes->code;
		}

		// ...attempt to make a response to medline. Note that you should replace your username here!
		if ( null == ( $json_response = $this->get_medline_request( $code ) ) ) {

			// ...display a message that the request failed
			$html = '
			<div id="medline-content">';
			$html .= '<!-- uamswp_medline ERROR - an empty host was supplied -->';
			$html .= '</div>
			<!-- /#medline-content -->';

			// ...otherwise, read the information provided by medline
		} else {

			$html = '<div id="medline-content '. $code .'">';
			foreach($json_response as $item){

				$Title = $item['entry'][0]["title"]["_value"];// ? 'ID: ' . $item["FoodID"] : '';
				$Link = $item['entry'][0]["link"][0]["href"];
				$Text = $item['entry'][0]["summary"]["_value"];
				$Author = $item['author']["name"]["_value"];
				$AuthorLink = $item['author']["uri"]["_value"];

				
				if(!empty($Title)){
					$html .= '<h2>' . $Title . "</h2>";
				//}
					$html .= '<div id="medline-text">'.$Text.'</div>';
					$html .= '<p>Source: <a href="'.$AuthorLink.'" target="_blank">'.$Author.'</a></p>';
				} else { //Reached end & no items matched
					$html .= "No items match";
				}
			} // end foreach
			$html .= '</div>
					<!-- /#medline-content -->';
		} // end if/else

		//$content .= $html;
 
        return $html;
 
    } // end display_shortcode
 
    /**
     * Attempts to request the locations JSON feed from dining
     *
     * @access public
     * @param  $location   The location for the dining JSON feed we're attempting to retrieve
     * @return $request    The user's JSON feed or null of the request failed
     */
    private function get_medline_request( $code ) {

		$url = 'https://connect.medlineplus.gov/service?mainSearchCriteria.v.c='. $code .'&knowledgeResponseType=application%2Fjson&mainSearchCriteria.v.dn=&mainSearchCriteria.v.cs=2.16.840.1.113883.6.90&informationRecipient.languageCode.c=en';
		$cache_key = 'medline_' . $code;
		$request = get_transient( $cache_key );

		if ( false === $request ) {
			$request = json_decode(wp_remote_retrieve_body( wp_remote_get( $url ) ), true);
	
			if ( is_wp_error( $request ) ) {
				// Cache failures for a short time, will speed up page rendering in the event of remote failure.
				set_transient( $cache_key, $request, MINUTE_IN_SECONDS * 15 );
			} else {
				// Success, cache for a longer time.
				set_transient( $cache_key, $request, HOUR_IN_SECONDS );
			}
		}
		return $request;
 
    } // end get_medline_request
	
}
UAMS_Syndicate_Shortcode_Medline::get_instance();