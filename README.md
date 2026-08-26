# Bojaco Gravity Forms Compatibility (MU Plugin)

This is a WordPress Must-Use (MU) plugin that ensures Gravity Forms submit button CSS compatibility by overriding namespaced implementations of the `add_custom_css_classes` filter on the `gform_submit_button` hook.

## How It Works

1. **Targeted Detection:** The plugin inspects the callbacks registered on the WordPress `gform_submit_button` hook during `init`, `wp_loaded`, and at an extremely early priority (`-9999`) on `gform_submit_button` itself.
2. **Namespace Agnostic:** It searches for any filter callback whose function name ends with `add_custom_css_classes`, regardless of its namespace (e.g. `jcore\add_custom_css_classes` or others).
3. **Seamless Replacement:** When a match is found, the original callback is unregistered and replaced with `bojaco\custom_btn_class` at the exact same priority to preserve execution order.
4. **Robust HTML Processing:** The replacement function uses WordPress's native `WP_HTML_Processor` to reliably add the `btn` class to the submit button's HTML markup without regex or string hazards.

## Installation

As an MU plugin, this file must be placed in your WordPress installation's `wp-content/mu-plugins/` directory:

```bash
wp-content/mu-plugins/compat-gravity-forms.php
```
