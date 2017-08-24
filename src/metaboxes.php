<?php

/**
 * Metaboxes
 *
 * @package GrottoPress\WordPress\Metaboxes
 * @since 0.1.0
 *
 * @author GrottoPress (https://www.grottopress.com)
 * @author N Atta Kus Adusei (https://twitter.com/akadusei)
 */

declare ( strict_types = 1 );

namespace GrottoPress\WordPress\Metaboxes;

use GrottoPress\WordPress\Metaboxes\Metabox;
use \WP_Post;

if ( \defined( 'WPINC' ) ) :

/**
 * Metaboxes
 *
 * @since 0.1.0
 */
trait Metaboxes {
    /**
     * Boxes
     *
     * @since 0.1.0
     * @access protected
     * 
     * @var array $boxes Boxes.
     */
    protected $metaboxes = null;

    /**
     * Run setup
     *
     * @see http://omfgitsnater.com/2013/05/adding-meta-boxes-to-attachments-in-wordpress/
     *
     * @since 0.1.0
     * @access public
     */
    public function setup() {
        \add_action( 'add_meta_boxes', [ $this, 'add' ], 10, 2 );
        \add_action( 'save_post', [ $this, 'save' ] );
        \add_action( 'edit_attachment', [ $this, 'save' ] );
    }

    /**
     * Add meta boxes.
     *
     * Create one or more meta boxes to be displayed 
     * on the editor screens.
     *
     * @action add_meta_boxes
     *
     * @since 0.1.0
     * @access public
     */
    public function add( string $post_type, WP_Post $post ) {
        if ( null === $this->metaboxes ) {
            $this->metaboxes = $this->metaboxes( $post );
        }

        if ( ! $this->metaboxes ) {
            return;
        }
        
        foreach ( $this->metaboxes as $id => $attr ) {
            ( new Metabox( $attr ) )->add();
        }
    }

    /**
     * Save meta boxes as custom fields.
     *
     * @var integer $post_id Post ID.
     *
     * @since 0.1.0
     * @access public
     *
     * @action save_post
     * @action edit_attachment
     */
    public function save( int $post_id ) {
        if ( null === $this->metaboxes ) {
            $this->metaboxes = $this->metaboxes( \get_post( $post_id ) );
        }

        if ( ! $this->metaboxes ) {
            return;
        }
        
        foreach ( $this->metaboxes as $id => $attr ) {
            ( new Metabox( $attr ) )->save( $post_id );
        }
    }

    /**
     * Meta boxes.
     *
     * Override this in child classes to build your metaboxes.
     *
     * @var \WP_Post $post Post.
     *
     * @since 0.1.0
     * @access protected
     *
     * @return array Metaboxes.
     */
    abstract protected function metaboxes( WP_Post $post ): array;
}

endif;
