# Development / Release Notes

This plugin’s release process is automated through GitHub Actions, specifically `.github/workflows/build-plugin-release.yml`, and is triggered by pushing a version tag such as `v1.5.1`.

## Release process

1. Make changes on a feature branch
2. Update the plugin header version in `nu_global_elements.php`
3. Create a PR, then merge the feature branch into `main`
4. Create and push a version tag such as:

   ```bash
   git tag -a v1.5.1 -m "Patch version 1.5.1"
   git push origin v1.5.1
   ```

5. The release workflow will then:
   - verify that the tag points to a commit reachable from `main`
   - verify that the plugin header version matches the tag version
   - build `global-elements-wordpress.zip` with a stable top-level plugin folder
   - create or update the GitHub release for that tag
   - upload the packaged plugin zip to the release
   - generate the public updater manifest from `manifest/info.template.json`
   - publish the generated manifest to the `gh-pages` branch

## Plugin metadata

The plugin header in `nu_global_elements.php` is the canonical source of truth for release metadata used by the workflow, including:

- plugin name
- version
- author
- author URI
- minimum supported WordPress version
- minimum supported PHP version

Update the plugin header before tagging a release.

## Manifest publishing

The updater manifest is published at:

```text
https://its-digital-technology.github.io/global-elements-wordpress/manifest/info.json
```

It is generated during the release workflow from `manifest/info.template.json`.

## GitHub Pages configuration

GitHub Pages should be configured to publish from:

- **Source:** Deploy from a branch
- **Branch:** `gh-pages`
- **Folder:** `/ (root)`

The release workflow updates the `gh-pages` branch automatically. It is treated as generated output and should not be edited manually.

## Notes

- Do not commit changes directly to `main`.
- The packaged plugin zip intentionally excludes repo-only artifacts such as workflow files, manifest source files, and Pages-only files.
- The workflow force-pushes `gh-pages` intentionally because it is treated as generated output.
