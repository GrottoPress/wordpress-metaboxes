<?php

/**
 * Metaboxes
 *
 * @package GrottoPress\WordPress\Metaboxes
 * @since 0.1.0
 *
 * @author GrottoPress <info@grottopress.com>
 * @author N Atta Kus Adusei
 */

declare (strict_types = 1);

namespace GrottoPress\WordPress\Metaboxes;

use GrottoPress\WordPress\Metaboxes\Metabox;
use \WP_Post;

/**
 * Metaboxes
 *
 * @since 0.1.0
 */
trait Metaboxes
{
    /**
     * Run setup
     *
     * @see http://omfgitsnater.com/2013/05/adding-meta-boxes-to-attachments-in-wordpress/
     *
     * @since 0.1.0
     * @access public
     */
    public function setup()
    {
        \add_action('add_meta_boxes', [$this, 'add'], 10, 2);
        \add_action('save_post', [$this, 'save']);
        \add_action('edit_attachment', [$this, 'save']);
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
    public function add(string $post_type, WP_Post $post)
    {
        if (!($metaboxes = $this->metaboxes($post))) {
            return;
        }
        
        foreach ($metaboxes as $id => $attr) {
            $this->metabox($attr)->add();
        }
    }

    /**
     * Save meta boxes as custom fields.
     *
     * @param integer $post_id Post ID.
     *
     * @since 0.1.0
     * @access public
     *
     * @action save_post
     * @action edit_attachment
     */
    public function save(int $post_id)
    {
        if (!($metaboxes = $this->metaboxes(\get_post($post_id)))) {
            return;
        }
        
        foreach ($metaboxes as $id => $attr) {
            $this->metabox($attr)->save($post_id);
        }
    }

    /**
     * Meta boxes.
     *
     * Override this in child classes to build your metaboxes.
     *
     * @param \WP_Post $post Post.
     *
     * @since 0.1.0
     * @access protected
     *
     * @return array Metaboxes.
     */
    abstract protected function metaboxes(WP_Post $post): array;

    /**
     * Get metabox
     *
     * @param array $args
     *
     * @since 0.1.0
     * @access protected
     *
     * @return Metabox
     */
    protected function metabox(array $args): Metabox
    {
        return new Metabox($args);
    }
}
