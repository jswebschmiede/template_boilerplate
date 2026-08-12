# Template Boilerplate - Joomla Template

## Description

Template Boilerplate is a base template for Joomla, serving as a starting point for developing custom templates. It provides a pre-configured structure and integrates modern development tools for efficient Joomla template development.

> [!WARNING]
> because this template uses tailwind css, the basic styling of joomla components may be not shown correctly. you have to rewrite the css for the components.
> this is not a production ready template. it is a template for development purposes.

## Features

- Pre-configured Webpack setup for efficient asset management
- Integration of Tailwind CSS for modern, responsive styling
- Automated build processes for development and production
- Progress display during the build process
- Automatic creation of ZIP archives for easy installation
- Automatic copying of files to your Joomla installation
- `theme.json` design tokens injected as `:root` CSS variables for shared styling across modules and components

## Prerequisites

- Node.js (version 21.5.0 or higher)
- pnpm (can be installed globally with `npm install -g pnpm`)
- Composer (for PHP dependencies such as color conversion)
- Joomla 5.x or higher (tested with Joomla 5.0)
- PHP 8.3 or higher (tested with PHP 8.3)

## Installation

1. Clone the repository:

    ```
    git clone https://github.com/jswebschmiede/template_boilerplate.git
    ```

2. Navigate to the project directory:

    ```
    cd template_boilerplate
    ```

3. Install dependencies:

    ```
    pnpm install
    composer install --no-dev
    ```

## theme.json

The goal is that design tokens from the template (colors, typography, containers, …) are available as global CSS custom properties on `:root`. Modules and components can reuse the same variables so frontend styling stays consistent with the active template theme.

### How it works

1. Edit tokens in [`src/theme.json`](src/theme.json) (WordPress-like structure under `settings`).
2. [`ThemeHelper`](src/src/Helper/ThemeHelper.php) parses the JSON on each request and injects them via `$wa->addInlineStyle()` in [`logic.php`](src/logic.php).
3. Emitted variables use the naming scheme `--tpl--style--global--{category}--{slug}` (e.g. `--tpl--style--global--color--primary`).
4. Hex colors in `theme.json` are converted to `oklch(…)` at runtime (`ozdemirburak/iris` in `src/vendor`).
5. [`src/media/css/vendor/theme.css`](src/media/css/vendor/theme.css) maps Tailwind `@theme` tokens to those variables with `var(--tpl--style--global--…)`.

### Usage in modules / components

```css
.my-module {
	color: var(--color-primary);
	/* or the runtime token directly: */
	color: var(--tpl--style--global--color--primary);
	max-width: var(--tpl--style--global--container--content);
}
```

Tailwind utilities such as `bg-primary` / `text-foreground` also resolve through the same tokens when wired in `theme.css`.

### Adding tokens

- Add any scalar under `settings.color`, `settings.typography.*`, or `settings.custom.*` in `theme.json` — the helper maps them dynamically (no PHP allowlist per key).
- For new tokens that should drive Tailwind utilities (`bg-brand`, …), also add a matching entry in `theme.css`.
- **Breakpoints** used by `@variant` / responsive utilities must stay as static lengths in `@theme` (CSS variables are not valid inside `@media`). Runtime `--tpl--…--breakpoint--*` vars are fine for JS or non-media CSS.

### Composer / vendor

PHP dependencies are installed into `src/vendor` (`composer.json` → `vendor-dir`). Run `composer install --no-dev` before packaging so Iris is included with the template.

## Usage

### Development Mode

To work in development mode and benefit from automatic reloading and copying the files to your Joomla installation:

- install the component in Joomla (see Production Mode)
- configure the `webpack.config.js` file with the path to your Joomla installation (default is `../../joomla`)
- folder structure should look like this. You can change the names of the folders, important is only the structur itself.

```
joomla_dev/
    - joomla/
    - joomla_components/
        - template_boilerplate/
```

- start the development server:

```
pnpm run dev
```

### Production Mode

To create a production-ready version of your template:

```
composer install --no-dev
pnpm run build
```

This creates an optimized version of the template and packages it into a ZIP file for installation in Joomla.

After installing or updating the template in Joomla, clear `administrator/cache/autoload_psr4.php` (or reinstall) so the template namespace for `ThemeHelper` is registered.

## Contributing

Contributions are welcome! Please create a pull request or open an issue for suggestions and bug reports.

## License

MIT License; see LICENSE.txt
