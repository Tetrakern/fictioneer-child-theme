# Fictioneer Child Theme

A blank WordPress child theme for [Fictioneer](https://github.com/Tetrakern/fictioneer/).

## Action & Filter Examples

Some common examples of customization that have come up in the past. If you want to know more, take a look at the [action](https://github.com/Tetrakern/fictioneer/blob/main/ACTIONS.md) and [filter](https://github.com/Tetrakern/fictioneer/blob/main/FILTERS.md) references in the main repository. You are expected to know the basics of CSS, HTML, and coding (PHP) or consult one of the many free tutorials on the matter just a quick Internet search away.

* [Limit the Blog shortcode to specific roles](#limit-the-blog-shortcode-to-specific-roles)
* [Add site title or logo above main navigation](#add-site-title-or-logo-above-main-navigation)
* [Remove selected fields from the editor](#remove-selected-fields-from-the-editor)

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
* **field_60040fa3bc4f1:** Filter & Search ID

```php
function child_remove_acf_items( $fields ) {
  // Optional: Skip for administrators (or other roles)
  if ( current_user_can( 'administrator' ) ) {
    return $fields;
  }

  // Fields you want to remove
  $field_keys = ['field_619a91f85da9d', 'field_636d81d34cab1', 'field_621b5610818d2', 'field_60edba4ff33f8', 'field_60040fa3bc4f1'];

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
