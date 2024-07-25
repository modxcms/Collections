const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = (env, options) => {
    const isProd = options.mode === 'production';

    return {
        mode: options.mode,
        devtool: isProd ? false : 'source-map',

        entry: [
            './_build/assets/sass/collections.scss',
            './_build/assets/js/index.ts'
        ],

        output: {
            path: path.resolve(__dirname, './assets/components/collections/web'),
            library: 'FredCollections',
            libraryTarget: 'umd',
            libraryExport: 'default',
            filename: 'fred_integration.js'
        },

        module: {
            rules: [
                {
                    test: /\.ts$/,
                    use: 'ts-loader',
                    exclude: /node_modules/,
                },
                {
                    test: /\.js$/,
                    exclude: /(node_modules)/,
                    use: {
                        loader: 'babel-loader'
                    }
                },
                {
                    test: /\.(sa|sc|c)ss$/,
                    use: [
                        {
                            loader: MiniCssExtractPlugin.loader
                        },
                        {
                            loader: "css-loader",
                            options: {
                                url: false,
                                sourceMap: true
                            }
                        },
                        {
                            loader: "postcss-loader"
                        },
                        {
                            loader: "sass-loader",
                            options: {
                                implementation: require("sass")
                            }
                        }
                    ]
                }
            ]
        },

        resolve: {
            alias: {
            },
            extensions: [ '.ts', '.js' ],
            fallback: {
                "path": require.resolve("path-browserify"),
                "url":  require.resolve("url/"),
            }
        },

        plugins: [
            new MiniCssExtractPlugin({
                filename: "fred_integration.css"
            })
        ]
    };
};
