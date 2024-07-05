const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const MiniCSSExtractPlugin = require( 'mini-css-extract-plugin' );
const path = require( 'path' );

const assetsConfig = {
    ...defaultConfig,
    entry: {
        'script': ['./assets/src/js/script.js']
    },
    output: {
        ...defaultConfig.output,
        filename: './js/[name].js',
        path: path.resolve( process.cwd(), 'assets/dist' ),
    },
    module: {
        rules: [
            {
                test: /\.css$/i,
                use: [
                    "style-loader",
                    "css-loader",
                    {
                        loader: "postcss-loader",
                        options: {
                            postcssOptions: {
                                plugins: [
                                    [
                                        "postcss-preset-env",
                                        {
                                            // Options
                                        },
                                    ],
                                ],
                            },
                        },
                    },
                ],
            },
        ],
    },

    plugins: [
        new MiniCSSExtractPlugin(
            {
                filename: "./css/[name].css"
            }
        ),
    ],
}

module.exports = [assetsConfig];