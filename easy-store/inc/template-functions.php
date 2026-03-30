<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Mystery Themes
 * @subpackage Easy Store
 * @since 1.0.0
 */

/*--------------------- Custom body classes ------------------------*/
    /**
     * Adds custom classes to the array of body classes.
     *
     * @param array $classes Classes for the body element.
     * @return array
     */
    function easy_store_body_classes( $classes ) {

        global $post;

        // Adds a class of hfeed to non-singular pages.
        if ( ! is_singular() ) {
            $classes[] = 'hfeed';
        }

        /**
         * Sidebar option for post/page/archive
         *
         * @since 1.0.0
         */
        if ( 'post' === get_post_type() ) {
            $sidebar_meta_option = get_post_meta( $post->ID, 'easy_store_sidebar_layout', true );
        }

        if ( 'page' === get_post_type() ) {
            $sidebar_meta_option = get_post_meta( $post->ID, 'easy_store_sidebar_layout', true );
        }
         
        if ( is_home() ) {
            $home_id = get_option( 'page_for_posts' );
            $sidebar_meta_option = get_post_meta( $home_id, 'easy_store_sidebar_layout', true );
        }
        
        if ( empty( $sidebar_meta_option ) || is_archive() || is_search() ) {
            $sidebar_meta_option = 'default_sidebar';
        }

        if ( 'product' === get_post_type() ) {
            $page_id = wc_get_page_id( 'shop' );
            $sidebar_meta_option = get_post_meta( $page_id, 'easy_store_sidebar_layout', true );
        }

        $archive_sidebar        = easy_store_get_customizer_option_value( 'easy_store_archive_sidebar' );
        $post_default_sidebar   = easy_store_get_customizer_option_value( 'easy_store_global_post_sidebar' );
        $page_default_sidebar   = easy_store_get_customizer_option_value( 'easy_store_global_page_sidebar' );
        
        if ( $sidebar_meta_option == 'default_sidebar' ) {
            if ( is_single() ) {
                if ( $post_default_sidebar == 'right_sidebar' ) {
                    $classes[] = 'right-sidebar';
                } elseif ( $post_default_sidebar == 'left_sidebar' ) {
                    $classes[] = 'left-sidebar';
                } elseif ( $post_default_sidebar == 'no_sidebar' ) {
                    $classes[] = 'no-sidebar';
                } elseif ( $post_default_sidebar == 'no_sidebar_center' ) {
                    $classes[] = 'no-sidebar-center';
                }
            } elseif ( is_page() ) {
                if ( $page_default_sidebar == 'right_sidebar' ) {
                    $classes[] = 'right-sidebar';
                } elseif ( $page_default_sidebar == 'left_sidebar' ) {
                    $classes[] = 'left-sidebar';
                } elseif ( $page_default_sidebar == 'no_sidebar' ) {
                    $classes[] = 'no-sidebar';
                } elseif ( $page_default_sidebar == 'no_sidebar_center' ) {
                    $classes[] = 'no-sidebar-center';
                }
            } elseif ( $archive_sidebar == 'right_sidebar' ) {
                $classes[] = 'right-sidebar';
            } elseif ( $archive_sidebar == 'left_sidebar' ) {
                $classes[] = 'left-sidebar';
            } elseif ( $archive_sidebar == 'no_sidebar' ) {
                $classes[] = 'no-sidebar';
            } elseif ( $archive_sidebar == 'no_sidebar_center' ) {
                $classes[] = 'no-sidebar-center';
            }
        } elseif ( $sidebar_meta_option == 'right_sidebar' ) {
            $classes[] = 'right-sidebar';
        } elseif ( $sidebar_meta_option == 'left_sidebar' ) {
            $classes[] = 'left-sidebar';
        } elseif ( $sidebar_meta_option == 'no_sidebar' ) {
            $classes[] = 'no-sidebar';
        } elseif ( $sidebar_meta_option == 'no_sidebar_center' ) {
            $classes[] = 'no-sidebar-center';
        }
        
        $easy_store_site_layout = easy_store_get_customizer_option_value( 'easy_store_site_layout' );
        if ( !empty( $easy_store_site_layout ) ) {
            $classes[] = esc_attr( $easy_store_site_layout ).'-layout';
        }

        return $classes;
    }

    add_filter( 'body_class', 'easy_store_body_classes' );

/*--------------------- Ping back ----------------------------------*/
    /**
     * Add a pingback url auto-discovery header for singularly identifiable articles.
     */
    function easy_store_pingback_header() {
        if ( is_singular() && pings_open() ) {
            echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
        }
    }

    add_action( 'wp_head', 'easy_store_pingback_header' );

/*--------------------- Google Font --------------------------------*/
    /**
     * Register Google fonts for News Portal.
     *
     * @return string Google fonts URL for the theme.
     * @since 1.0.0
     */
    if ( ! function_exists( 'easy_store_fonts_url' ) ) :

        function easy_store_fonts_url() {
            $fonts_url = '';
            $font_families = array();

            /*
             * Translators: If there are characters in your language that are not supported
             * by Poppins, translate this to 'off'. Do not translate into your own language.
             */
            if ( 'off' !== _x( 'on', 'Poppins font: on or off', 'easy-store' ) ) {
                $font_families[] = 'Poppins:400,700,900';
            }

            if ( $font_families ) {
                $query_args = array(
                    'family' => urlencode( implode( '|', $font_families ) ),
                    'subset' => urlencode( 'latin,latin-ext' ),
                );

                $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
            }

            return $fonts_url;
        }

    endif;

/*--------------------- Enqueue style and scripts ------------------*/
    
    if ( ! function_exists( 'easy_store_admin_scripts_style' ) ) :

        /**
         * Enqueue Scripts and styles for admin
         *
         * @since 1.0.0
         */
        function easy_store_admin_scripts_style( $hook ) {

            if ( 'widgets.php' != $hook && 'edit.php' != $hook && 'post.php' != $hook && 'post-new.php' != $hook ) {
                return;
            }

            if ( function_exists( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
            }

            wp_enqueue_script( 'jquery-ui-button' );

            wp_enqueue_style( 'easy-store-admin-style', get_template_directory_uri() .'/assets/css/es-admin-styles.css', array(), EASY_STORE_VERSION );

            wp_enqueue_script( 'easy-store-admin-script', get_template_directory_uri() .'/assets/js/es-admin-scripts.js', array('jquery'), EASY_STORE_VERSION, true );
        }

    endif;

    add_action( 'admin_enqueue_scripts', 'easy_store_admin_scripts_style' );

    if ( ! function_exists( 'easy_store_scripts' ) ) :

        /**
         * Enqueue scripts and styles.
         */
        function easy_store_scripts() {

            wp_enqueue_style( 'easy-store-fonts', easy_store_fonts_url(), array(), null );

            wp_enqueue_style( 'lightslider-styles', get_template_directory_uri() .'/assets/library/lightslider/css/lightslider.css', array(), '1.1.6' );

            wp_enqueue_style( 'custom-scrollbar-styles', get_template_directory_uri() .'/assets/library/custom-scrollbar/jquery.mCustomScrollbar.min.css', array(), '1.0.0' );

            wp_enqueue_style( 'font-awesome', get_template_directory_uri() .'/assets/library/font-awesome/css/all.min.css', array(), '7.2.0' );

            wp_enqueue_style( 'easy-store-style', get_stylesheet_uri(), array(), EASY_STORE_VERSION );
            
            wp_enqueue_style( 'easy-store-responsive-style', get_template_directory_uri() .'/assets/css/es-responsive.css', array(), '1.0.0' );

            wp_enqueue_script( 'easy-store-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), EASY_STORE_VERSION, true );
            
            wp_enqueue_script( 'easy-store-combine-scripts', get_template_directory_uri() .'/assets/js/es-combine-scripts.js', array( 'jquery' ), EASY_STORE_VERSION, true );
            
            wp_enqueue_script( 'easy-store-sticky-sidebar-scripts', get_template_directory_uri() .'/assets/library/stickysidebar/theia-sticky-sidebar.js', array( 'jquery' ), EASY_STORE_VERSION, true );
            
            $easy_store_primary_menu_sticky = easy_store_get_customizer_option_value( 'easy_store_primary_menu_sticky' );
            
            if ( true == $easy_store_primary_menu_sticky ) {
                  wp_enqueue_script( 'jquery-sticky', get_template_directory_uri(). '/assets/library/sticky/jquery.sticky.js', array( 'jquery' ), '20150416', true );
            
                  wp_enqueue_script( 'np-sticky-menu-setting', get_template_directory_uri(). '/assets/library/sticky/sticky-setting.js', array( 'jquery-sticky' ), '20150309', true );
            }

            wp_register_script( 'easy-store-custom-script', get_template_directory_uri() .'/assets/js/es-custom-scripts.js', array( 'jquery' ), EASY_STORE_VERSION, true );

            $store_sticky_option = easy_store_get_customizer_option_value( 'easy_store_general_sidebar_sticky_option' );

            $store_sticky_option = ( true == $store_sticky_option ) ? 'show' : 'hide';

            wp_localize_script( 'easy-store-custom-script', 'mtObject', array(
                'store_sticky'  => $store_sticky_option,
            ) );
            
            wp_enqueue_script( 'easy-store-custom-script' );
            
            if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
                wp_enqueue_script( 'comment-reply' );
            }
        }

    endif;

    add_action( 'wp_enqueue_scripts', 'easy_store_scripts' );

/*--------------------- Font awesome icon array --------------------*/

    if ( ! function_exists( 'easy_store_font_awesome_icon_array' ) ) :

        /**
         * Define font awesome icons
         *
         * @return array();
         * @since 1.0.0
         */
        function easy_store_font_awesome_icon_array() {

            return array("fas fa-martini-glass","fas fa-music","fas fa-search","fas fa-envelope","fas fa-heart","fas fa-star","fas fa-user","fas fa-film","fas fa-th-large","fas fa-th","fas fa-th-list","fas fa-check","fas fa-xmark","fas fa-circle-xmark","fas fa-search-plus","fas fa-search-minus","fas fa-power-off","fas fa-signal","fas fa-gear","fas fa-gears","fas fa-trash","fas fa-home","fas fa-file","fas fa-clock","fas fa-road","fas fa-download","fas fa-circle-arrow-down","fas fa-circle-arrow-up","fas fa-inbox","fas fa-circle-play","fas fa-arrow-rotate-left","fas fa-arrow-rotate-right","fas fa-arrows-rotate","fas fa-list-alt","fas fa-lock","fas fa-flag","fas fa-headphones","fas fa-volume-off","fas fa-volume-down","fas fa-volume-up","fas fa-qrcode","fas fa-barcode","fas fa-tag","fas fa-tags","fas fa-book","fas fa-bookmark","fas fa-print","fas fa-camera","fas fa-font","fas fa-bold","fas fa-italic","fas fa-text-height","fas fa-text-width","fas fa-align-left","fas fa-align-center","fas fa-align-right","fas fa-align-justify","fas fa-list","fas fa-dedent","fas fa-outdent","fas fa-indent","fas fa-video-camera","fas fa-image","fas fa-pencil","fas fa-location-dot","fas fa-adjust","fas fa-tint","fas fa-edit","fas fa-pencil","fas fa-share-from-square","fas fa-square-check","fas fa-arrows","fas fa-step-backward","fas fa-fast-backward","fas fa-backward","fas fa-play","fas fa-pause","fas fa-stop","fas fa-forward","fas fa-fast-forward","fas fa-step-forward","fas fa-eject","fas fa-chevron-left","fas fa-chevron-right","fas fa-plus-circle","fas fa-minus-circle","fas fa-times-circle","fas fa-check-circle","fas fa-question-circle","fas fa-info-circle","fas fa-crosshairs","fas fa-circle-check","fas fa-ban","fas fa-arrow-left","fas fa-arrow-right","fas fa-arrow-up","fas fa-arrow-down","fas fa-share","fas fa-share","fas fa-expand","fas fa-compress","fas fa-plus","fas fa-minus","fas fa-asterisk","fas fa-exclamation-circle","fas fa-gift","fas fa-leaf","fas fa-fire","fas fa-eye","fas fa-eye-slash","fas fa-triangle-exclamation","fas fa-plane","fas fa-calendar","far fa-calendar","fas fa-calendar-plus","fas fa-calendar-minus","fas fa-calendar-xmark","fas fa-calendar-check","fas fa-random","fas fa-comment","fas fa-magnet","fas fa-chevron-up","fas fa-chevron-down","fas fa-retweet","fas fa-shopping-cart","fas fa-folder","fas fa-folder-open","fas fa-arrows-v","fas fa-arrows-h","fas fa-chart-bar","fab fa-square-twitter","fab fa-square-facebook","fas fa-camera-retro","fas fa-key","fas fa-comments","fas fa-thumbs-up","fas fa-thumbs-down","far fa-thumbs-up","far fa-thumbs-down","fas fa-star-half","far fa-heart","fas fa-sign-out","fab fa-square-linkedin","fab fa-linkedin","fas fa-thumb-tack","fas fa-external-link","fas fa-sign-in","fas fa-trophy","fab fa-square-github","fas fa-upload","far fa-lemon","fas fa-phone","fas fa-phone-square","fab fa-twitter","fab fa-facebook-f","fab fa-facebook","fab fa-github","fas fa-unlock","fas fa-credit-card","fas fa-rss","fas fa-square-rss","fas fa-hard-drive","fas fa-bullhorn","fas fa-bell","fas fa-certificate","fas fa-hand-point-right","fas fa-hand-point-left","fas fa-hand-point-up","fas fa-hand-point-down","fas fa-arrow-circle-left","fas fa-arrow-circle-right","fas fa-arrow-circle-up","fas fa-arrow-circle-down","fas fa-globe","fas fa-wrench","fas fa-tasks","fas fa-filter","fas fa-briefcase","fas fa-maximize","fas fa-users","fas fa-link","fas fa-cloud","fas fa-flask","fas fa-scissors","fas fa-copy","fas fa-paperclip","fas fa-save","fas fa-floppy-disk","fas fa-square","fas fa-bars","fas fa-list-ul","fas fa-list-ol","fas fa-strikethrough","fas fa-underline","fas fa-table","fas fa-magic","fas fa-truck","fab fa-pinterest","fab fa-square-pinterest","fab fa-pinterest-p","fab fa-square-google-plus","fab fa-google-plus","fas fa-money-bill-1","fas fa-caret-down","fas fa-caret-up","fas fa-caret-left","fas fa-caret-right","fas fa-columns","fas fa-sort","fas fa-sort-down","fas fa-sort-up","fas fa-rotate-left","fas fa-gavel","fas fa-gauge","fas fa-comment","fas fa-comments","fas fa-bolt","fas fa-sitemap","fas fa-umbrella","fas fa-clipboard","fas fa-lightbulb","fas fa-exchange","fas fa-cloud-download","fas fa-cloud-upload","fas fa-user-md","fas fa-stethoscope","fas fa-suitcase","fas fa-coffee","fas fa-cutlery","fas fa-file-lines","fas fa-hospital","fas fa-ambulance","fas fa-medkit","fas fa-fighter-jet","fas fa-beer","fas fa-h-square","fas fa-plus-square","fas fa-angle-double-left","fas fa-angle-double-right","fas fa-angle-double-up","fas fa-angle-double-down","fas fa-angle-left","fas fa-angle-right","fas fa-angle-up","fas fa-angle-down","fas fa-desktop","fas fa-laptop","fas fa-tablet","fas fa-mobile-screen","fas fa-mobile","far fa-circle","fas fa-quote-left","fas fa-quote-right","fas fa-spinner","fas fa-circle","fas fa-reply","fab fa-github-alt","far fa-face-smile","far fa-face-frown","far fa-face-meh","fas fa-gamepad","fas fa-keyboard","fas fa-flag-checkered","fas fa-terminal","fas fa-code","fas fa-reply-all","fas fa-star-half-stroke","fas fa-location-arrow","fas fa-crop","fas fa-code-fork","fas fa-link-slash","fas fa-question","fas fa-info","fas fa-exclamation","fas fa-superscript","fas fa-subscript","fas fa-eraser","fas fa-puzzle-piece","fas fa-microphone","fas fa-microphone-slash","fas fa-shield","fas fa-fire-extinguisher","fas fa-rocket","fab fa-maxcdn","fas fa-chevron-circle-left","fas fa-chevron-circle-right","fas fa-chevron-circle-up","fas fa-chevron-circle-down","fab fa-html5","fab fa-css3","fas fa-anchor","fas fa-unlock-alt","fas fa-bullseye","fas fa-ellipsis-h","fas fa-ellipsis-v","fas fa-play-circle","fas fa-ticket","fas fa-square-minus","fas fa-level-up","fas fa-level-down","fas fa-check-square","fas fa-pencil-square","fas fa-external-link-square","fas fa-share-square","fas fa-compass","fas fa-square-caret-down","fas fa-square-caret-up","fas fa-square-caret-left","fas fa-square-caret-right","fas fa-euro-sign","fas fa-sterling-sign","fas fa-dollar-sign","fas fa-indian-rupee-sign","fas fa-yen-sign","fas fa-ruble-sign","fas fa-won-sign","fab fa-bitcoin","fab fa-btc","fas fa-file","fas fa-file-text","fas fa-sort-alpha-asc","fas fa-sort-alpha-desc","fas fa-sort-amount-asc","fas fa-sort-amount-desc","fas fa-sort-numeric-asc","fas fa-sort-numeric-desc","fab fa-square-youtube","fab fa-youtube","fab fa-xing","fab fa-square-xing","fab fa-dropbox","fab fa-stack-overflow","fab fa-instagram","fab fa-flickr","fab fa-adn","fab fa-bitbucket","fab fa-tumblr","fab fa-square-tumblr","fas fa-long-arrow-down","fas fa-long-arrow-up","fas fa-long-arrow-left","fas fa-long-arrow-right","fab fa-apple","fab fa-windows","fab fa-android","fab fa-linux","fab fa-dribbble","fab fa-skype","fab fa-foursquare","fab fa-trello","fas fa-female","fas fa-male","fab fa-gratipay","fas fa-sun","fas fa-moon","fas fa-archive","fas fa-bug","fab fa-vk","fab fa-weibo","fab fa-renren","fab fa-pagelines","fab fa-stack-exchange","fas fa-circle-dot","fas fa-wheelchair","fab fa-square-vimeo","fas fa-turkish-lira-sign","fas fa-square-plus","fas fa-space-shuttle","fab fa-slack","fas fa-envelope-square","fab fa-wordpress","fab fa-openid","fas fa-building-columns","fas fa-graduation-cap","fab fa-yahoo","fab fa-google","fab fa-reddit","fab fa-square-reddit","fab fa-reddit-alien","fab fa-stumbleupon-circle","fab fa-stumbleupon","fab fa-delicious","fab fa-digg","fab fa-pied-piper","fab fa-pied-piper-alt","fab fa-drupal","fab fa-joomla","fas fa-language","fas fa-fax","fas fa-building","fas fa-child","fas fa-paw","fas fa-spoon","fas fa-cube","fas fa-cubes","fab fa-behance","fab fa-square-behance","fab fa-steam","fab fa-square-steam","fas fa-recycle","fas fa-car","fas fa-car","fas fa-taxi","fas fa-taxi","fas fa-tree","fab fa-spotify","fab fa-deviantart","fab fa-soundcloud","fas fa-database","fas fa-file-pdf","fas fa-file-word","fas fa-file-excel","fas fa-file-powerpoint","fas fa-file-image","fas fa-file-zipper","fas fa-file-audio","fas fa-file-video","fas fa-file-code","fab fa-vine","fab fa-codepen","fab fa-jsfiddle","fas fa-life-ring","fas fa-circle-notch","fab fa-rebel","fab fa-empire","fab fa-square-git","fab fa-git","fab fa-y-combinator","fab fa-hacker-news","fab fa-tencent-weibo","fab fa-qq","fab fa-weixin","fas fa-paper-plane","fas fa-history","fas fa-header","fas fa-paragraph","fas fa-sliders","fas fa-share-alt","fas fa-share-alt-square","fas fa-bomb","fas fa-futbol","fas fa-tty","fas fa-binoculars","fas fa-plug","fab fa-slideshare","fab fa-twitch","fab fa-yelp","fas fa-newspaper","fas fa-wifi","fas fa-calculator","fab fa-cc-paypal","fab fa-paypal","fab fa-google-wallet","fab fa-cc-visa","fab fa-cc-mastercard","fab fa-cc-discover","fab fa-cc-amex","fab fa-cc-stripe","fas fa-bell-slash","fas fa-trash","fas fa-copyright","fas fa-at","fas fa-eyedropper","fas fa-paint-brush","fas fa-birthday-cake","fas fa-area-chart","fas fa-pie-chart","fas fa-line-chart","fab fa-lastfm","fab fa-square-lastfm","fas fa-toggle-off","fas fa-toggle-on","fas fa-bicycle","fas fa-bus","fab fa-ioxhost","fab fa-angellist","fas fa-closed-captioning","fas fa-shekel-sign","fab fa-buysellads","fab fa-connectdevelop","fab fa-dashcube","fab fa-forumbee","fab fa-leanpub","fab fa-sellsy","fab fa-shirtsinbulk","fab fa-simplybuilt","fab fa-skyatlas","fas fa-cart-plus","fas fa-cart-arrow-down","fas fa-diamond","fas fa-ship","fas fa-user-secret","fas fa-motorcycle","fas fa-street-view","fas fa-heartbeat","fas fa-venus","fas fa-mars","fas fa-mercury","fas fa-transgender-alt","fas fa-venus-double","fas fa-mars-double","fas fa-venus-mars","fas fa-mars-stroke","fas fa-mars-stroke-v","fas fa-mars-stroke-h","fas fa-neuter","fas fa-genderless","fab fa-whatsapp","fas fa-server","fas fa-user-plus","fas fa-user-times","fas fa-hotel","fas fa-bed","fab fa-viacoin","fas fa-train","fas fa-subway","fab fa-medium","fab fa-optin-monster","fab fa-opencart","fab fa-expeditedssl","fas fa-battery-4","fas fa-battery-full","fas fa-battery-3","fas fa-battery-three-quarters","fas fa-battery-2","fas fa-battery-half","fas fa-battery-empty","fas fa-mouse-pointer","fas fa-i-cursor","fas fa-object-group","fas fa-object-ungroup","fas fa-note-sticky","fab fa-cc-jcb","fab fa-cc-diners-club","fas fa-clone","fas fa-balance-scale","fas fa-hourglass","fas fa-hourglass-start","fas fa-hourglass-half","fas fa-hourglass-end","fas fa-hand-back-fist","fas fa-hand","fas fa-hand-scissors","fas fa-hand-lizard","fas fa-hand-spock","fas fa-hand-pointer","fas fa-hand-peace","fas fa-trademark","fas fa-registered","fab fa-creative-commons","fab fa-gg","fab fa-gg-circle","fab fa-odnoklassniki","fab fa-square-odnoklassniki","fab fa-get-pocket","fab fa-wikipedia-w","fab fa-safari","fab fa-chrome","fab fa-firefox","fab fa-opera","fab fa-internet-explorer","fas fa-tv","fab fa-contao","fab fa-500px","fab fa-amazon","fas fa-industry","fas fa-map-pin","fas fa-map-signs","fas fa-map","fas fa-comment-dots","fab fa-houzz","fab fa-vimeo","fab fa-black-tie","fab fa-fonticons","fab fa-edge","fas fa-credit-card-alt","fab fa-codiepie","fab fa-modx","fab fa-fort-awesome","fab fa-usb","fab fa-product-hunt","fab fa-mixcloud","fab fa-scribd","fas fa-pause-circle","fas fa-stop-circle","fas fa-shopping-bag","fas fa-shopping-basket","fas fa-hashtag","fab fa-bluetooth","fab fa-bluetooth-b","fas fa-percent","fab fa-gitlab","fab fa-wpbeginner","fab fa-wpforms","fab fa-envira","fas fa-universal-access","fas fa-wheelchair-alt","fas fa-blind","fas fa-audio-description","fas fa-volume-control-phone","fas fa-braille","fas fa-assistive-listening-systems","fas fa-hands-asl-interpreting","fas fa-ear-deaf","fab fa-glide","fab fa-glide-g","fas fa-hands","fas fa-low-vision","fab fa-viadeo","fab fa-square-viadeo","fab fa-snapchat","fab fa-snapchat-ghost","fab fa-square-snapchat","fab fa-first-order","fab fa-yoast","fab fa-themeisle","fab fa-google-plus","fas fa-font-awesome");
        }

    endif;

    if ( ! function_exists( 'easy_store_font_awesome_social_icon_array' ) ) :

        /**
         * Define font awesome social media icons
         *
         * @return array();
         * @since 1.0.0
         */
        function easy_store_font_awesome_social_icon_array() {
            return array( "fab fa-facebook-square","fab fa-facebook-f","fab fa-facebook","fab fa-facebook-messenger","fab fa-twitter-square","fab fa-twitter","fab fa-x-twitter","fab fa-yahoo","fab fa-google","fab fa-google-wallet","fab fa-google-plus","fab fa-instagram","fab fa-square-linkedin","fab fa-linkedin-in","fab fa-linkedin","fab fa-pinterest-p","fab fa-pinterest","fab fa-pinterest-square","fab fa-google-plus-square","fab fa-google-plus","fab fa-square-youtube","fab fa-youtube","fab fa-vimeo","fab fa-vimeo-v","fab fa-tiktok","fab fa-qq","fab fa-weixin","fab fa-wordpress","fab fa-wordpress-simple","fab fa-wpbeginner","fab fa-snapchat","fab fa-disqus","fab fa-threads","fab fa-telegram","fab fa-viber","fab fa-whatsapp","fab fa-redhat","fab fa-reddit","fab fa-discord","fab fa-quora","fab fa-teamspeak","fab fa-line","fab fa-kickstarter","fab fa-tumblr" );
        }
        
    endif;

/*--------------------- Font awesome icon converter --------------------*/

    if ( ! function_exists( 'easy_store_convert_fa4_to_fa7' ) ) :

        function easy_store_convert_fa4_to_fa7( $icon ) {

            // Remove old prefix
            $icon = str_replace( 'fa ', '', $icon );

            // Basic rename mappings (important ones)
            $map = array(
                'fa-remove' => 'fa-xmark',
                'fa-close'  => 'fa-xmark',
                'fa-times'  => 'fa-xmark',
                'fa-gear'   => 'fa-gear',
                'fa-cog'    => 'fa-gear',
                'fa-photo'  => 'fa-image',
                'fa-picture-o' => 'fa-image',
                'fa-file-o' => 'fa-file',
                'fa-clock-o' => 'fa-clock',
                'fa-arrow-circle-o-down' => 'fa-circle-down',
                'fa-arrow-circle-o-up'   => 'fa-circle-up',
            );

            if ( isset( $map[$icon] ) ) {
                $icon = $map[$icon];
            }

            // Detect brand icons
            $brands = array(
                'fa-facebook','fa-facebook-f','fa-twitter','fa-youtube','fa-linkedin',
                'fa-instagram','fa-pinterest','fa-google','fa-github','fa-vimeo',
                'fa-dribbble','fa-skype','fa-android','fa-apple','fa-windows'
            );

            if ( in_array( $icon, $brands ) ) {
                return 'fa-brands ' . str_replace('fa-', 'fa-', $icon);
            }

            // Default to solid
            return 'fa-solid ' . str_replace('fa-', 'fa-', $icon);
        }

    endif;

/*--------------------- Get categories list ------------------------*/

    if ( ! function_exists( 'easy_store_categories_lists' ) ) :

        /**
         * categories list
         *
         * @return array();
         */
        function easy_store_categories_lists() {
            $easy_store_cat_args = array(
                'type'        => 'post',
                'child_of'    => 0,
                'orderby'     => 'name',
                'order'       => 'ASC',
                'hide_empty'  => 1,
                'taxonomy'    => 'category',
            );
            $easy_store_categories = get_categories( $easy_store_cat_args );
            $easy_store_categories_lists = array();
            foreach( $easy_store_categories as $category ) {
                $easy_store_categories_lists[esc_attr( $category->slug )] = esc_html( $category->name );
            }
            return $easy_store_categories_lists;
        }
        
    endif;

/*--------------------- Get innerpage header bg image --------------*/

    if ( ! function_exists( 'easy_store_inner_header_bg_image' ) ) :

        /**
         * Background image for inner page header
         *
         * @since 1.0.0
         */
        function easy_store_inner_header_bg_image( $input ) {

            $image_attr = array();

            if ( empty( $image_attr ) ) {

                // Fetch from Custom Header Image.
                $image = get_header_image();
                if ( ! empty( $image ) ) {
                    $image_attr['url']    = $image;
                    $image_attr['width']  = get_custom_header()->width;
                    $image_attr['height'] = get_custom_header()->height;
                }
            }

            if ( ! empty( $image_attr ) ) {
                $input .= 'background-image:url(' . esc_url( $image_attr['url'] ) . ');';
                $input .= 'background-size:cover;';
            }

            return $input;
        }

    endif;

    add_filter( 'easy_store_inner_header_style_attribute', 'easy_store_inner_header_bg_image' );

/*--------------------- Breadcrumb for innerpages ------------------*/

    if ( ! function_exists( 'easy_store_inner_breadcrumb' ) ) :

        /**
         * Breadcrumb in innerpages
         *
         * @since 1.0.0
         */
        function easy_store_inner_breadcrumb() {

            $easy_store_breadcrumb_option = easy_store_get_customizer_option_value( 'easy_store_breadcrumb_option' );

            if ( false === $easy_store_breadcrumb_option ) {
                return;
            }

            if ( ! function_exists( 'breadcrumb_trail' ) ) {
                require_once get_template_directory() . '/inc/es-breadcrumbs.php';
            }

            $breadcrumb_args = array(
                'container'   => 'div',
                'show_browse' => false,
            );
            breadcrumb_trail( $breadcrumb_args );

        }

    endif;

/*--------------------- Update wishlist count with Ajax ------------*/

    if ( defined( 'YITH_WCWL' ) && ! function_exists( 'easy_store_yith_wcwl_ajax_update_count' ) ):

        /**
         * Update count for using ajax
         */
        function easy_store_yith_wcwl_ajax_update_count() {
            wp_send_json( array(
            'count' => yith_wcwl_count_all_products()
            ) );
        }

    endif;

    add_action( 'wp_ajax_easy_store_yith_wcwl_update_wishlist_count', 'easy_store_yith_wcwl_ajax_update_count' );
    add_action( 'wp_ajax_nopriv_easy_store_yith_wcwl_update_wishlist_count', 'easy_store_yith_wcwl_ajax_update_count' );

/*--------------------- Social media content -----------------------*/

    if ( ! function_exists( 'easy_store_social_media_content' ) ) :

        /**
         * Display content of social media as repeater field
         *
         * @since 1.0.0
         */
        function easy_store_social_media_content() {
            $get_social_media_icons = easy_store_get_customizer_option_value( 'social_media_icons' );
            $get_decode_social_media = json_decode( $get_social_media_icons );
            if ( !empty( $get_decode_social_media ) ) {
                echo '<div class="es-social-icons-wrapper">';
                foreach ( $get_decode_social_media as $single_icon ) {
                    $icon_class = $single_icon->mt_item_social_icon;
                    $icon_url = $single_icon->mt_item_url;
                    if ( !empty( $icon_url ) ) {
                        echo '<span class="social-link"><a href="'. esc_url( $icon_url ) .'" target="_blank"><i class="'. esc_attr( $icon_class ) .'"></i></a></span>';
                    }
                }
                echo '</div><!-- .es-social-icons-wrapper -->';
            }
        }

    endif;

/*--------------------- minify css ---------------------------------*/

    if ( ! function_exists( 'easy_store_minify_css' ) ) {

        /**
         * Minify CSS
         *
         * @since 1.0.1
         */
        function easy_store_minify_css( $css = '' ) {

            // Return if no CSS
            if ( ! $css ) return;

            // Normalize whitespace
            $css = preg_replace( '/\s+/', ' ', $css );

            // Remove ; before }
            $css = preg_replace( '/;(?=\s*})/', '', $css );

            // Remove space after , : ; { } */ >
            $css = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $css );

            // Remove space before , ; { }
            $css = preg_replace( '/ (,|;|\{|})/', '$1', $css );

            // Strips leading 0 on decimal values (converts 0.5px into .5px)
            $css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );

            // Strips units if value is 0 (converts 0px to 0)
            $css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );

            // Trim
            $css = trim( $css );

            // Return minified CSS
            return $css;

        }

    }

/*--------------------- Archive title prefix -----------------------*/

    if ( ! function_exists( 'easy_store_archive_title_prefix' ) ) :
            
        /**
         * Archive title prefix
         *
         * @since 1.2.0
         */
        function easy_store_archive_title_prefix( $title ) {

            $title_prefix_enable = easy_store_get_customizer_option_value( 'easy_store_archive_title_prefix_option' );

            if ( false === $title_prefix_enable ) {
                return preg_replace( '/^\w+: /', '', $title );
            } else {
                return $title;
            }
            
        }

    endif;

    add_filter( 'get_the_archive_title', 'easy_store_archive_title_prefix' );

/*--------------------- Convert settings id value ------------------*/

    add_action( 'init', 'easy_store_convert_show_hide_to_boolean' );

    function easy_store_convert_show_hide_to_boolean() {

        $easy_store_new_setup = get_theme_mod( 'easy_store_new_toggle_setup', false );

        if ( true !== $easy_store_new_setup ) {

            $req_settigns_ids = array(
                'easy_store_block_base_widget_editor_option'    => 'hide',
                'easy_store_top_header_option'                  => 'hide',
                'easy_store_header_cart_option'                 => 'show',
                'easy_store_header_wishlist_option'             => 'show',
                'easy_store_primary_menu_sticky'                => 'show',
                'easy_store_store_sidebar_sticky_option'        => 'show',
            );

            foreach( $req_settigns_ids as $key => $value ) {

                $easy_store_setting_value = get_theme_mod( $key, $value );
                if ( ! is_bool( $easy_store_setting_value ) ) {
                    if ( 'hide' == $easy_store_setting_value ) {
                        set_theme_mod( $key, false );
                    } else {
                        set_theme_mod( $key, true );
                    }
                }

            }

            set_theme_mod( 'easy_store_new_toggle_setup', true );

        }
    }