const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const ZipPlugin = require('zip-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const webpack = require('webpack');
const chalk = require('chalk');
const logSymbols = require('log-symbols');
const fs = require('fs');
const rimraf = require('rimraf');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');

let lastPercentage = 0;

// Change this to your Joomla URL
const siteUrl = 'http://127.0.0.1:6969/';

const progressHandler = (percentage, message) => {
    // Round to 2 decimal places
    const roundedPercentage = Math.round(percentage * 100);

    // Only output if the percentage has changed
    if (roundedPercentage !== lastPercentage) {
        // Clear console
        process.stdout.write('\x1Bc');

        if (percentage === 0) {
            console.log(chalk.blue(`${logSymbols.info} Build starting...`));
        } else if (percentage === 1) {
            console.log(chalk.green(`${logSymbols.success} Build completed!`));
        } else {
            const progressBar = createProgressBar(roundedPercentage);
            console.log(
                chalk.yellow(
                    `${logSymbols.warning} Progress: ${progressBar} ${roundedPercentage}%`,
                ),
            );
            console.log(chalk.cyan(`${logSymbols.info} Status: ${message}`));
        }

        lastPercentage = roundedPercentage;
    }
};

// Function to create a visual progress bar
const createProgressBar = (percentage) => {
    const width = 20;
    const filledWidth = Math.round(width * (percentage / 100));
    const emptyWidth = width - filledWidth;
    return chalk.green('█'.repeat(filledWidth)) + chalk.gray('█'.repeat(emptyWidth));
};

const cleanDirectories = (directories) => {
    directories.forEach((dir) => {
        if (fs.existsSync(dir)) {
            rimraf.sync(`${dir}/*`);
        }
    });
};

module.exports = (env, argv) => {
    const isProduction = argv.mode === 'production';
    const joomlaPath = path.resolve(__dirname, '../../joomla');

    const copyPatterns = [
        {
            from: 'src/html',
            to: 'html',
        },
        {
            from: 'src/language',
            to: 'language',
        },
        {
            from: 'src/media',
            to: 'media',
            globOptions: {
                ignore: ['**/*.js', '**/*.css'], // Ignores JS and CSS files
            },
        },
        {
            from: 'src/*.php',
            to: '[name][ext]',
        },
        { from: 'src/joomla.asset.json', to: 'joomla.asset.json' },
        { from: 'src/templateDetails.xml', to: 'templateDetails.xml' },
    ];

    const directoriesToClean = [
        path.join(joomlaPath, 'templates/template_boilerplate'),
        path.join(joomlaPath, 'media/templates/site/template_boilerplate'),
    ];

    if (!isProduction) {
        console.log(chalk.red(`${logSymbols.error} Cleaning directories...`));

        // Clean directories before build
        cleanDirectories(directoriesToClean);

        copyPatterns.push(
            {
                from: 'dist/templateDetails.xml',
                to: path.join(joomlaPath, 'templates/template_boilerplate'),
            },
            {
                from: 'dist/joomla.asset.json',
                to: path.join(joomlaPath, 'templates/template_boilerplate'),
            },
            {
                from: 'dist/html',
                to: path.join(joomlaPath, 'templates/template_boilerplate/html'),
            },
            {
                from: 'dist/media',
                to: path.join(joomlaPath, 'media/templates/site/template_boilerplate'),
            },
            {
                from: 'dist/language/**/*.ini',
                to: ({ context, absoluteFilename }) => {
                    const relativePath = path.relative(context, absoluteFilename);
                    const parts = relativePath.split(path.sep);
                    const lang = parts[parts.length - 2]; // take the second last part of the path as language code
                    return path.join(joomlaPath, 'language', lang, path.basename(absoluteFilename));
                },
                noErrorOnMissing: true,
                force: true,
            },
            {
                from: 'dist/*.php',
                to: ({ context, absoluteFilename }) => {
                    const fileName = path.basename(absoluteFilename);
                    return path.join(joomlaPath, 'templates/template_boilerplate', fileName);
                },
            },
        );
    }

    return {
        mode: isProduction ? 'production' : 'development',
        devtool: isProduction ? 'source-map' : 'eval-source-map',
        entry: {
            site: './src/media/js/site.js',
        },
        output: {
            filename: 'media/js/[name].bundle.js',
            path: path.resolve(__dirname, 'dist'),
            clean: true,
        },
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                    },
                },
                {
                    test: /\.css$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        {
                            loader: 'css-loader',
                            options: {
                                url: false,
                            },
                        },
                        'postcss-loader',
                    ],
                },
            ],
        },
        plugins: [
            new webpack.ProgressPlugin({
                handler: progressHandler,
                modulesCount: 5000, // Default value
                profile: false, // Disables detailed profiling
            }),
            new MiniCssExtractPlugin({
                filename: 'media/css/[name].min.css',
            }),
            new CopyPlugin({
                patterns: copyPatterns,
            }),
            ...(isProduction
                ? [
                      new ZipPlugin({
                          path: path.resolve(__dirname, 'dist/zip'),
                          filename: 'template_boilerplate.zip',
                          extension: 'zip',
                          fileOptions: {
                              mtime: new Date(),
                              mode: 0o100664,
                              compress: true,
                              forceZip64Format: false,
                          },
                      }),
                  ]
                : [
                      new BrowserSyncPlugin(
                          {
                              proxy: siteUrl,
                              port: 3000,
                              ui: {
                                  port: 3001,
                              },
                              files: [
                                  'dist/**/*.php',
                                  'dist/**/*.js',
                                  'dist/**/*.css',
                                  'dist/**/*.html',
                              ],
                              reloadDelay: 0,
                              open: false,
                          },
                          {
                              reload: false,
                          },
                      ),
                  ]),
        ],
        optimization: {
            minimizer: [
                // For webpack@5 you can use the `...` syntax to extend existing minimizers (i.e. `terser-webpack-plugin`)
                `...`,
                new CssMinimizerPlugin({
                    minimizerOptions: {
                        preset: [
                            'default',
                            {
                                discardComments: { removeAll: true },
                            },
                        ],
                    },
                }),
            ],
            minimize: true,
        },
        stats: 'errors-only', // Shows only errors in the console output
    };
};
