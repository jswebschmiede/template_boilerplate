const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const ZipPlugin = require('zip-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const webpack = require('webpack');
const chalk = require('chalk');
const logSymbols = require('log-symbols');
const fs = require('fs');
const rimraf = require('rimraf');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');

let lastPercentage = 0;

// Change this to your Joomla URL
const siteUrl = 'http://127.0.0.1:6969/';

// WEBPACK_DEBUG=1 npm run dev  → verbose stats, no console clear, no progress spam
const isDebug = process.env.WEBPACK_DEBUG === '1';

const progressHandler = (percentage, message, ...args) => {
	const roundedPercentage = Math.round(percentage * 100);

	if (isDebug) {
		if (roundedPercentage !== lastPercentage || percentage === 1) {
			const details = args.filter(Boolean).join(' ');
			console.log(
				chalk.cyan(
					`[webpack] ${roundedPercentage}% ${message}${details ? ` ${details}` : ''}`,
				),
			);
			lastPercentage = roundedPercentage;
		}
		return;
	}

	// Only output if the percentage has changed
	if (roundedPercentage !== lastPercentage) {
		// Do not clear the console — that hides webpack/postcss errors and warnings
		if (percentage === 0) {
			console.log(chalk.blue(`${logSymbols.info} Build starting...`));
		} else if (percentage === 1) {
			console.log(chalk.green(`${logSymbols.success} Build completed!`));
		} else {
			const progressBar = createProgressBar(roundedPercentage);
			// \x1b[2K clears the line; padEnd alone fails because chalk ANSI codes inflate string length
			process.stdout.write(
				`\r\x1b[2K${chalk.yellow(
					`${logSymbols.warning} Progress: ${progressBar} ${roundedPercentage}%`,
				)} ${chalk.cyan(message)}`,
			);
			// Close the \r progress line so webpack stats start on a new line
			if (message === 'done') {
				process.stdout.write('\n');
			}
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
		{ from: 'src/theme.json', to: 'theme.json' },
		{ from: 'src/src', to: 'src' },
		{ from: 'src/vendor', to: 'vendor' },
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
			{
				from: 'dist/theme.json',
				to: path.join(joomlaPath, 'templates/template_boilerplate'),
			},
			{
				from: 'dist/src',
				to: path.join(joomlaPath, 'templates/template_boilerplate/src'),
			},
			{
				from: 'dist/vendor',
				to: path.join(joomlaPath, 'templates/template_boilerplate/vendor'),
			},
		);
	}

	return {
		mode: isProduction ? 'production' : 'development',
		devtool: isProduction ? 'source-map' : 'eval-source-map',
		entry: {
			app: './src/media/js/app.js',
			editor: './src/media/css/editor.css',
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
								// Inline Font Awesome webfonts referenced from editor @font-face rules.
								url: {
									filter: (assetUrl) =>
										/media\/vendor\/fontawesome-free\/webfonts\//.test(assetUrl),
								},
							},
						},
						{
							loader: 'postcss-loader',
							options: {
								// Surface PostCSS/Tailwind failures instead of emitting empty CSS
								postcssOptions: {
									config: path.resolve(__dirname, 'postcss.config.js'),
								},
							},
						},
					],
				},
				{
					test: /\.(woff|woff2|eot|ttf|otf)$/i,
					type: 'asset/inline',
				},
			],
		},
		plugins: [
			...(isDebug
				? []
				: [
					new webpack.ProgressPlugin({
						handler: progressHandler,
						modulesCount: 5000,
						profile: isDebug,
					}),
				]),
			new RemoveEmptyScriptsPlugin(),
			new MiniCssExtractPlugin({
				filename: 'media/css/[name].min.css',
			}),
			{
				apply(compiler) {
					const themeJsonPath = path.resolve(__dirname, 'src/theme.json');

					compiler.hooks.afterCompile.tap('ThemeJsonWatchDependency', (compilation) => {
						compilation.fileDependencies.add(themeJsonPath);
					});
				},
			},
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
			// Keep CSS readable while debugging empty/broken output
			minimize: isProduction,
		},
		infrastructureLogging: isDebug
			? { level: 'verbose' }
			: { level: 'info' },
		stats: isDebug
			? {
				all: false,
				errors: true,
				errorDetails: true,
				warnings: true,
				logging: 'verbose',
				loggingDebug: [/postcss/, /css/, /mini-css/, /sass/],
				assets: true,
				modules: true,
				moduleTrace: true,
				colors: true,
			}
			: {
				preset: 'errors-warnings',
				errorDetails: true,
				logging: 'warn',
				colors: true,
			},
	};
};
