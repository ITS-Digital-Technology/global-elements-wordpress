# NU Global Elements - kernl(ui) - WordPress Plugin

## Introduction

A WordPress plugin developed by the Northeastern University ITS Web Solutions team. Loads the latest version of [kernl(ui) Global Elements](https://northeastern.netlify.app/pattern-library/page-chrome/global-elements/) to your website, including the code for the [TrustArc](https://trustarc.com/) cookie consent manager.

The global elements package includes a few elements that should be included on almost all Northeastern sites. They have dynamic content and fixed designs for consistency across sites. TrustArc allows users to set their cookie preferences, and is now required on all Northestern University websites.

[Learn more about the kernl(ui) design system](https://northeastern.netlify.app/)

## Requirements
- **Global Header.** In order for the global header to display, the active theme must support a call to `wp_body_open()` after the opening `<body>` tag:
    ```php
        <body class="nu__body-class">
            <?php wp_body_open(); ?>

            ...
    ```
- **Conflicts.** If older versions of the NU Global elements are active on the site, changes to the theme files or settings in a plugin may be needed in order to prevent old and new global elements from showing.

## Installation

1. Download the latest version of the plugin from [GitHub](https://github.com/ITS-Digital-Technology/global-elements-wordpress/releases/latest/download/global-elements-wordpress.zip).
2. Add the new plugin through WP Admin, uploading the `.zip` file from step 1.
3. Activate the new plugin once it has finished uploading.
4. If you need to stop this plugin from displaying the global header, footer, or TrustArc elements, you can do so from the settings page for this plugin in WP Admin.
5. If you need more detailed instructions, see this [KB article](https://service.northeastern.edu/tech?id=kb_article_view&sysparm_article=KB000022192).