# WordPress Metaboxes

## Description

A library to set up metaboxes in WordPress.

## Usage

Install via composer:

`composer require grottopress/wordpress-metaboxes`

Use thus:

    <?php

    use GrottoPress\WordPress\Metaboxes\Metaboxes;

    // Extend the `Metaboxes` class

    class My_Metaboxes_Class extends Metaboxes {
        // Define your boxes with the `metaboxes` method
        protected function metaboxes( WP_Post $post ): array {
            $boxes = [];

            if ( \is_post_type_hierarchical( $post->post_type ) ) {
                $boxes[] = [
                    'id' => 'my-metabox-1',
                    'title' => \esc_html__( 'My Metabox 1' ),
                    'context' => 'side',
                    'priority' => 'default',
                    'screen' => 'page',
                    'fields' => [
                        [
                            'id' => 'my-metabox-1-field-1',
                            'type' => 'select',
                            'choices' => [
                                'left' => \esc_html__( 'Left' ),
                                'right' => \esc_html__( 'Right' ),
                            ],
                            'label' => \esc_html__( 'Select direction' ),
                            'label_pos' => 'before_field', // or 'after_field'
                        ],
                    ],
                    'notes' => '<p>' . \esc_html__( 'Just a super cool layout metabox example' ) . '</p>',
                ];
            }

            $boxes[] = [
                'id' => 'my-metabox-2',
                'title' => \esc_html__( 'My Metabox 2' ),
                'screen' => 'post',
                'fields' => [
                    [
                        'id' => 'my-metabox-2-field-1',
                        'type' => 'text',
                        'label' => \esc_html__( 'My first field' ),
                        'label_pos' => 'before_field', // or 'after_field'
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    [
                        'id' => 'my-metabox-2-field-2',
                        'type' => 'email',
                        'label' => \esc_html__( 'My second field' ),
                        'label_pos' => 'before_field', // or 'after_field'
                        'sanitize_callback' => 'sanitize_email',
                    ],
                    [
                        'id' => 'my-metabox-2-field-3',
                        'type' => 'url',
                        'label' => \esc_html__( 'My third field' ),
                        'label_pos' => 'before_field', // or 'after_field'
                        'sanitize_callback' => 'esc_url',
                    ]
                ],
                'notes' => '<p>' . \esc_html__( 'My awesome metabox example.' ) . '</p>',
            ];

            return $boxes;
        }

    }

    // Add your metaboxes to WordPress
    $metaboxes = new My_Metaboxes_Class();
    $metaboxes->setup();
