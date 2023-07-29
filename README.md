# Fictioneer Child Theme

A blank WordPress child theme for [Fictioneer](https://github.com/Tetrakern/fictioneer/).

## Action & Filter Examples

Some common examples of customization that have come up in the past. If you want to know more, take a look at the [action](https://github.com/Tetrakern/fictioneer/blob/main/ACTIONS.md) and [filter](https://github.com/Tetrakern/fictioneer/blob/main/FILTERS.md) references in the main repository. You are expected to know the basics of CSS, HTML, and coding (PHP) or consult one of the many free tutorials on the matter just a quick Internet search away.

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
  /* Offsets the stickiness point of the navigation; might need breakpoints for mobile! */
  --nav-observer-offset: 34px;
}

.child-navigation-identity {
  /* Just an example, you need to style this yourself! */
  padding: 0 10px 10px 16px;
  margin: 0 auto;
  max-width: var(--site-width);
}
```

You can hide the default logo or site title by just not adding any or disabling it. If you want to remove the default header (including the header image) completely, put the following into your `functions.php`.

```php
function fictioneer_child_remove_default_header() {
  remove_action( 'fictioneer_site', 'fictioneer_site_header', 20 );
}
add_action( 'init', 'fictioneer_child_remove_default_header' );
```
