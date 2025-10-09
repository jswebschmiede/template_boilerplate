# Template Boilerplate - Joomla Template

## Description

Template Boilerplate is a base template for Joomla, serving as a starting point for developing custom templates. It provides a pre-configured structure and integrates modern development tools for efficient Joomla template development.

> [!WARNING]
> because this template uses tailwind css, the basic styling of joomla components may be not shown correctly. you have to rewrite the css for the components.
> this is not a production ready template. it is a template for development purposes.

## Features

-   Pre-configured Webpack setup for efficient asset management
-   Integration of Tailwind CSS for modern, responsive styling
-   Automated build processes for development and production
-   Progress display during the build process
-   Automatic creation of ZIP archives for easy installation
-   Automatic copying of files to your Joomla installation

## Prerequisites

-   Node.js (version 21.5.0 or higher)
-   pnpm (can be installed globally with `npm install -g pnpm`)
-   Joomla 5.x or higher (tested with Joomla 5.0)
-   PHP 8.3 or higher (tested with PHP 8.3)

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
    ```

## Usage

### Development Mode

To work in development mode and benefit from automatic reloading and copying the files to your Joomla installation:

-   install the component in Joomla (see Production Mode)
-   configure the `webpack.config.js` file with the path to your Joomla installation (default is `../../joomla`)
-   folder structure should look like this. You can change the names of the folders, important is only the structur itself.

```
joomla_dev/
    - joomla/
    - joomla_components/
        - template_boilerplate/
```

-   start the development server:

```
pnpm run dev
```

### Production Mode

To create a production-ready version of your template:

```
pnpm run build
```

This creates an optimized version of the template and packages it into a ZIP file for installation in Joomla.

## Contributing

Contributions are welcome! Please create a pull request or open an issue for suggestions and bug reports.

## License

MIT License; see LICENSE.txt
