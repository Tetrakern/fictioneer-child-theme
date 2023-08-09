# Fictioneer Child Theme

A blank WordPress child theme for [Fictioneer](https://github.com/Tetrakern/fictioneer/).

## Action & Filter Examples

Some common examples of customization that have come up in the past. If you want to know more, take a look at the [action](https://github.com/Tetrakern/fictioneer/blob/main/ACTIONS.md) and [filter](https://github.com/Tetrakern/fictioneer/blob/main/FILTERS.md) references in the main repository. You are expected to know the basics of CSS, HTML, and coding (PHP) or consult one of the many free tutorials on the matter just a quick Internet search away.

* [Limit the Blog shortcode to specific roles](#limit-the-blog-shortcode-to-specific-roles)
* [Add site title or logo above main navigation](#add-site-title-or-logo-above-main-navigation)
* [Remove selected fields from the editor](#remove-selected-fields-from-the-editor)
* [Tabula Rasa](#tabula-rasa)
* [Flush (Persistent) Object Caches](#flush-persistent-object-caches)

### Limit the Blog shortcode to specific roles

Put the following into your `functions.php`.

```php
function child_limit_blog_shortcode_to_roles( $query_args ) {
  # Optional: Only apply the filter to a specific page, etc.
  if ( ! is_page( 'your-page-slug' ) ) {
    return $query_args;
  }

  # Step 1: Get users with the specified role(s)
  $users = get_users( array( 'role__in' => ['author'] ) );

  # Step 2: Extract the user IDs
  $user_ids = wp_list_pluck( $users, 'ID' );

  # Step 3: Modify query arguments
  $query_args['author__in'] = $user_ids;

  # Step 4: Return modified query arguments
  return $query_args;
}
add_filter( 'fictioneer_filter_shortcode_blog_query_args', 'child_limit_blog_shortcode_to_roles', 10 );
```

### Add site title or logo above main navigation

Put the following into your `functions.php` to add custom HTML above the main navigation.

```php
function child_add_identity_above_navigation( $args ) {
  ?>
  <div class="child-navigation-identity">Site Title/Logo</div>
  <?php
}
add_action( 'fictioneer_navigation_top', 'child_add_identity_above_navigation' );
```

Put the following custom CSS into your theme style file. Note that this example is just the foundation and not ready to be used, you will need to adjust the style to your needs, account for mobile viewports, etc.

```css
body {
  /* Offsets the stickiness point of the navigation on mobile! */
  --nav-observer-offset: 32px; /* Replace with mobile height of the new element! */
}

@media only screen and (min-width: 1024px) {
  body {
    /* Offsets the stickiness point of the navigation on desktop! */
    --nav-observer-offset: 64px; /* Replace with desktop height of the new element! */
  }
}

.main-navigation {
  margin-top: 0 !important; /* This is now covered by the new element! */
}

/* Mobile */
.child-navigation-identity {
  /* Adjust as needed! */
  padding: 12px 10px 10px 16px;
  margin: 0 auto;
  max-width: var(--site-width);
  height: 32px;
}

/* Desktop */
@media only screen and (min-width: 1024px) {
  .child-navigation-identity {
    /* Adjust as needed! */
    height: 64px;
  }
}
```

You can hide the default logo or site title by just not adding any or disabling it. If you want to remove the default header (including the header image) completely, put the following into your `functions.php`.

```php
function child_remove_default_header() {
  remove_action( 'fictioneer_site', 'fictioneer_site_header', 20 );
}
add_action( 'init', 'child_remove_default_header' );
```

### Remove selected fields from the editor

If you want to remove fields from the editor, perhaps to limit certain user roles, you need to hook into the internal [Advanced Custom Fields](https://www.advancedcustomfields.com/resources/) plugin. The field names can be acquired by inspecting the HTML of the editor; a few usual suspects are listed below. Note that this alone will not actually remove the fields, just prevent them from being rendered. Put this into your `functions.php`.

* **field_619a91f85da9d:** Sticky in lists
* **field_636d81d34cab1:** Custom Story CSS
* **field_621b5610818d2:** Custom CSS
* **field_60edba4ff33f8:** ePUB Custom CSS

```php
function child_remove_acf_items( $fields ) {
  // Optional: Skip for administrators (or other roles)
  if ( current_user_can( 'administrator' ) ) {
    return $fields;
  }

  // Fields you want to remove
  $field_keys = ['field_619a91f85da9d', 'field_636d81d34cab1', 'field_621b5610818d2', 'field_60edba4ff33f8'];

  // Remove the fields from the fields array
  foreach ( $fields as $key => &$field ) {
    if ( in_array( $field['key'], $field_keys ) ) {
      unset( $fields[$key] );
    }
  }

  // Return modified fields array
  return $fields;
}
add_filter( 'acf/pre_render_fields', 'child_remove_acf_items', 9999 );
```

If you want to remove the SEO meta box, use the following.

```php
function child_remove_seo_meta_box() {
  // Optional: Skip for administrators (or other roles)
  if ( current_user_can( 'administrator' ) ) {
    return;
  }

  // Remove the meta box action
  remove_action( 'add_meta_boxes', 'fictioneer_add_seo_metabox', 10 );
}
add_action( 'admin_init', 'child_remove_seo_meta_box' );
```

### Tabula Rasa

While the theme already restricts non-administrator roles, you may want to go one step further and make doubly sure that the admin panel does not expose sensitive data or functions. This will also account for custom roles added. Put the following into your `functions.php`.

```php
function child_admin_screen_tabula_rasa() {
  // Setup
  $screen = get_current_screen();
  $base = $screen->id;
  $admin_menus = ['tools', 'export', 'import', 'site-health', 'export-personal-data', 'erase-personal-data', 'themes', 'customize', 'nav-menus', 'theme-editor', 'users', 'user-new', 'options-general'];

  // Administration
  if ( ! current_user_can( 'manage_options' ) && in_array( $base, $admin_menus ) ) {
    wp_die( __( 'Access denied.', 'fictioneer' ) );
  }

  // Comments
  if ( ! current_user_can( 'moderate_comments' ) && in_array( $base, ['edit-comments', 'comment'] ) ) {
    wp_die( __( 'Access denied.', 'fictioneer' ) );
  }
}

function child_admin_menu_tabula_rasa() {
  // Administration
  if ( ! current_user_can( 'manage_options' ) ) {
    remove_menu_page( 'index.php' );
    remove_menu_page( 'tools.php' );
    remove_menu_page( 'plugins.php' );
    remove_menu_page( 'themes.php' );
  }

  // Comments
  if ( ! current_user_can( 'moderate_comments' ) ) {
    remove_menu_page( 'edit-comments.php' );
  }
}

function child_admin_dashboard_tabula_rasa() {
  global $wp_meta_boxes;

  // Administration
  if ( ! current_user_can( 'manage_options' ) ) {
    $wp_meta_boxes['dashboard']['normal']['core'] = [];
    $wp_meta_boxes['dashboard']['side']['core'] = [];

    remove_action( 'welcome_panel', 'wp_welcome_panel' );
  }
}

function child_admin_bar_tabula_rasa() {
  global $wp_admin_bar;

  // Comments
  if ( ! current_user_can( 'moderate_comments' ) ) {
    $wp_admin_bar->remove_node( 'comments' );
  }
}

function child_admin_upload_media_size_tabula_rasa( $bytes ) {
  // Maximum upload file size
  if ( ! current_user_can( 'manage_options' ) ) {
    return 1024 * 1024 * 5; // 5 MB
  }
}

function child_admin_upload_media_type_tabula_rasa( $file ) {
  // Setup
  $filetype = wp_check_filetype( $file['name'] );
  $mime_type = $filetype['type'];

  // Limit upload file types
  if ( ! current_user_can( 'manage_options' ) ) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/gif', 'application/pdf', 'image/svg+xml'];

    if ( ! in_array( $mime_type, $allowed_types ) ){
      $file['error'] = __( 'You are not allowed to upload files of this type.', 'fictioneer' );
    }
  }

  return $file;
}

add_action( 'admin_menu', 'child_admin_menu_tabula_rasa', 9999 );
add_action( 'current_screen', 'child_admin_screen_tabula_rasa', 9999 );
add_action( 'wp_dashboard_setup', 'child_admin_dashboard_tabula_rasa', 9999 );
add_action( 'admin_bar_menu', 'child_admin_bar_tabula_rasa', 9999 );
add_filter( 'upload_size_limit', 'child_admin_upload_media_size_tabula_rasa', 9999 );
add_filter( 'wp_handle_upload_prefilter', 'child_admin_upload_media_type_tabula_rasa', 9999 );
```

### Flush (Persistent) Object Caches

If you are using a persistent object cache (Redis, Memcached, etc.), especially in combination with page caching, you may encounter some stale content. This can happen because Fictioneer’s posts are heavily intertwined — stories have chapters, chapters have a list of chapters in the same story, shortcodes show posts from the whole site, and so forth. If any part of that chain is not properly invalidated, page caches might be regenerated with outdated queries pulled from the object cache.

This is not easily resolved, but the theme tries to make it work by going medieval on every associated post or shortcode it knows about. There is a small apocalypse happening every time you save a post. But it might not be enough, so here are some additional measures. This is fine for sites that are not updated minutely or faster. Put the following into your `functions.php`.

```php
function child_redis_me_this( $post_id ) {
  // Do not when...
  if (
    ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ||
    wp_is_post_autosave( $post_id ) ||
    wp_is_post_revision( $post_id ) ||
    in_array( get_post_status( $post_id ), ['auto-draft'] )
  ) {
    return;
  }

  // Nuclear option
  wp_cache_flush();
}
add_action( 'save_post', 'child_redis_me_this', 9 );
add_action( 'untrash_post', 'child_redis_me_this', 9 );
add_action( 'trashed_post', 'child_redis_me_this', 9 );
add_action( 'delete_post', 'child_redis_me_this', 9 );
```

It might also help to disable the shortcode Transients. Put the following into your `functions.php`.
```php
define( 'FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION', -1 );
```

### Restrict Page Templates

If you want to prevent non-administrators from assigning custom page templates, put the following into your `functions.php`. You can allow certain templates by adding their names (as `'name.php' => true`) to the constant.

```php
// Add templates ('name' => true) to the array you want to allow,
// otherwise leave empty to disable the template dropdown.
define( 'CHILD_ALLOWED_PAGE_TEMPLATES', [] );

/**
 * Filters the page template selection for non-administrators
 *
 * @param array $templates  Array of templates ('name' => true).
 *
 * @return array Array of allowed templates.
 */

function child_restrict_page_templates( $templates ) {
  // Filter selection for all non-administrators...
  if ( ! current_user_can( 'manage_options' ) ) {
    return array_intersect_key( $templates, CHILD_ALLOWED_PAGE_TEMPLATES );
  }

  // ... otherwise change nothing
  return $templates;
}
add_filter( 'theme_page_templates', 'child_restrict_page_templates' );


/**
 * Makes sure only allowed templates are selected
 *
 * @param int $post_id  ID of the saved post.
 */

function child_enforce_restricted_page_templates( $post_id ) {
  // Do nothing if...
  if (
    current_user_can( 'manage_options' ) ||
    get_post_type( $post_id ) !== 'page'
  ) {
    return;
  }

  // Get currently selected template
  $selected_template = get_post_meta( $post_id, '_wp_page_template', true );

  // Remove if not allowed
  if ( ! in_array( $selected_template, CHILD_ALLOWED_PAGE_TEMPLATES ) ) {
    update_post_meta( $post_id, '_wp_page_template', '' );
  }
}
add_action( 'save_post', 'child_enforce_restricted_page_templates' );
```
