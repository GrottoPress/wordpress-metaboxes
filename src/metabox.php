<?php

/**
 * WordPress Metabox
 *
 * @package GrottoPress\WordPress\Metaboxes
 * @since 0.1.0
 *
 * @author GrottoPress (https://www.grottopress.com)
 * @author N Atta Kus Adusei (https://twitter.com/akadusei)
 */

declare ( strict_types = 1 );

namespace GrottoPress\WordPress\Metaboxes;

use GrottoPress\WordPress\Form\Field;
use \WP_Post;

if ( \defined( 'WPINC' ) ) :

/**
 * WordPress Metabox
 *
 * @since 0.1.0
 */
class Metabox {
	/**
     * Metabox ID
	 *
	 * @since 0.1.0
	 * @access protected
	 * 
	 * @var string $id Metabox ID
	 */
	protected $id;
	
	/**
     * Metabox title
	 *
	 * @since 0.1.0
	 * @access protected
	 * 
	 * @var string $title Metabox title
	 */
	protected $title;
	
	/**
     * Screen on which to show meta box
	 *
	 * @since 0.1.0
	 * @access protected
	 * 
	 * @var string|array|WP_Screen $screen Screen on which to show meta box
	 */
	protected $screen;
	
	/**
     * Context
	 *
	 * @since 0.1.0
	 * @access protected
	 * 
	 * @var string $context Metabox context
	 */
	protected $context;
	
	/**
     * Priority
	 *
	 * @since 0.1.0
	 * @access protected
	 * 
	 * @var string $context Priority
	 */
	protected $priority;
	
	/**
     * Fields
	 *
	 * @since 0.1.0
	 * @access protected
	 * 
	 * @var string $fields Fields
	 */
	protected $fields;
	
	/**
     * Notes
	 *
	 * @since 0.1.0
	 * @access protected
	 * 
	 * @var string $notes Notes added to bottom of meta boxes
	 */
	protected $notes;

	/**
	 * Constructor
	 *
	 * @var array $args Metabox arguments supplied as associative array
	 *
	 * @since 0.1.0
	 * @access public
	 */
	public function __construct( $args = [] ) {
	    $this->set_attributes( $args );
	    $this->sanitize_attributes();
	}

	/**
	 * Nonce
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @return string Nonce name.
	 */
	protected function nonce(): string {
		return '_wpnonce-' . $this->id;
	}

	/**
	 * Add meta box.
	 *
	 * @since 0.1.0
	 * @access public
	 */
	public function add() {
	    if ( ! $this->id ) {
	    	return;
	    }

        \add_meta_box( $this->id, $this->title, [ $this, 'render' ], $this->screen,
    		$this->context, $this->priority, $this->fields );
	}

	/**
	 * Do metabox callback.
	 *
	 * @var \WP_Post $post Post.
	 * @var array $fields Fields passed as arg to callback.
	 *
	 * @since 0.1.0
	 * @access protected
	 */
	protected function render( WP_Post $post, array $fields = [] ) {
        if ( ! $this->fields ) {
            return;
        }
        
        $html = \wp_nonce_field( \basename( __FILE__ ), $this->nonce(), true, false );
        
		foreach ( $this->fields as $key => $attr ) {
			$attr['id'] = isset( $attr['id'] ) ? \sanitize_title( $attr['id'] ) : '';
			$attr['name'] = empty( $attr['name'] ) ? $attr['id'] : $attr['name'];
			$attr['value'] = \get_post_meta( $post->ID, $attr['id'] );
			$attr['value'] = ( 1 === \count( $attr['value'] ) ? $attr['value'][0] : $attr['value'] );

			$html .= ( new Field( $attr ) )->render();
		}
		
		if ( $this->notes ) {
			$html .= $this->notes;
		}
		
		echo $html;
	}

	/**
	 * Save meta box.
	 *
	 * @var int $post_id Post ID.
	 *
	 * @since 0.1.0
	 * @access public
	 */
	public function save( int $post_id = 0 ) {
	    if ( $post_id < 1 ) {
			return;
		}
	    
	    if ( ! $this->fields ) {
			return;
		}

		if ( \defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		if (
			! \current_user_can( \get_post_type_object( \get_post_type( $post_id ) )
			->cap->edit_post, $post_id )
		) {
			return;
		}
		
		if (
			! isset( $_POST[ $this->nonce() ] )
			|| ! \wp_verify_nonce( $_POST[ $this->nonce() ], \basename( __FILE__ ) )
		) {
			return;
		}

		foreach ( $this->fields as $key => $attr ) {
			$attr['id'] = isset( $attr['id'] ) ? \sanitize_title( $attr['id'] ) : '';
			$content = isset( $_POST[ $attr['id'] ] ) ? ( array ) $_POST[ $attr['id'] ] : [];
			
			\delete_post_meta( $post_id, $attr['id'] );
			
			if ( empty( $content[0] ) ) {
				continue;
			}

			foreach ( $content as $new_meta_value ) {
				if ( isset( $attr['sanitize_callback'] ) ) {
					$new_meta_value = \call_user_func( $attr['sanitize_callback'], $new_meta_value );
				} else {
					$new_meta_value = \sanitize_text_field( $new_meta_value );
				}
				
				\add_post_meta( $post_id, $attr['id'], $new_meta_value, false );
			}
		}
	}

	/**
	 * Set attributes
	 *
	 * @var array $args	Arguments supplied to this object.
	 *
	 * @since 0.1.0
	 * @access protected
	 */
	protected function set_attributes( array $args ) {
		if ( ! $args ) {
			return;
		}

		$vars = \get_object_vars( $this );

		foreach ( $vars as $key => $value ) {
			$this->$key = $args[ $key ] ?? '';
		}
	}

	/**
	 * Sanitize attributes
	 *
	 * @since 0.1.0
	 * @access protected
	 */
	protected function sanitize_attributes() {
		$this->id = \sanitize_title( $this->id );
    	$this->title = \sanitize_text_field( $this->title );
    	// $this->screen = \sanitize_key( $this->screen );
	    $this->fields = ( array ) $this->fields;
	    // $this->notes = $this->notes, 'pre_user_description';

    	$this->context = ( \in_array( $this->context, [ 'normal', 'side', 'advanced' ] )
    	? $this->context : null );

    	$this->priority = ( \in_array( $this->priority, [ 'high', 'low' ] )
	        ? $this->priority : null );
	}
}

endif;
