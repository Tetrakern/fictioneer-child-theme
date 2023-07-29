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

### Add site title or logo above navigation

Copy and paste the `partials/_navigation.php` from the main theme to `partials/_navigation.php` in your child theme. This overrides the main partial with a customized version, but be aware that changes to the main partial will not carry over. You will have to keep track of that yourself. Modify the HTML and CSS (in your child theme style) as needed. Note that this example is just the foundation and not ready to be used.

```php
<nav id="full-navigation" class="main-navigation" aria-label="Main Navigation">
  <div id="nav-observer-sticky" class="nav-observer"></div>
  <div class="main-navigation__background"></div>
  <!-- START CUSTOM MARKUP -->
  <div class="child-navigation-identity">Site Title/Logo</div>
  <!-- END CUSTOM MARKUP -->
  <div class="main-navigation__wrapper">
    <div class="main-navigation__left">
      <?php
        wp_nav_menu(
          array(
            'theme_location' => 'nav_menu',
            'menu_class' => 'main-navigation__list',
            'container' => '',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'
          )
        );
      ?>
    </div>
    <div class="main-navigation__right">
      <?php get_template_part( 'partials/_icon-menu', null, array( 'location' => 'in-navigation' ) ); ?>
      <label for="mobile-menu-toggle" class="mobile-menu-button follows-alert-number">
        <?php fictioneer_icon( 'fa-bars', 'off' ); ?>
        <?php fictioneer_icon( 'fa-xmark', 'on' ); ?>
      </label>
    </div>
  </div>
</nav>
```

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
