var debug = process.env.NODE_ENV !== 'production'
var webpack = require('webpack')
var path = require('path')
var ExtractTextPlugin = require('extract-text-webpack-plugin')

module.exports = {
    context: path.join(__dirname, 'src'),
    devtool: debug ? 'eval-source-map' : null,
    debug: debug,
    entry: './js/app',
    module: {
        loaders: [
            {
                test: /\.js?$/,
                exclude: /(node_modules|bower_components)/,
                loader: 'babel'
            },
            {
                test: /\.scss?$/,
                exclude: /(node_modules|bower_components)/,
                loader: ExtractTextPlugin.extract('style', 'css!sass')
            }
        ]
    },
    resolve: {
        alias: {
            Helper: path.resolve(__dirname, 'src', 'js/helper'),
            Config: path.resolve(__dirname, 'src', 'js/config'),
            GenDocx: path.resolve(__dirname, 'src', 'js/generateDocx'),
            Routes: path.resolve(__dirname, 'src', 'js/routes'),
            Actions: path.resolve(__dirname, 'src', 'js/actions'),
            Components: path.resolve(__dirname, 'src', 'js/components'),
            Containers: path.resolve(__dirname, 'src', 'js/containers'),
            Constants: path.resolve(__dirname, 'src', 'js/constants/ActionsTypes'),
        },
        extensions: ['', '.js', '.jsx']
    },
    output: {
        path: path.join(__dirname, '../public'),
        filename: 'app.min.js'
    },
    plugins: [
        new ExtractTextPlugin('css/style.min.css', { allChunks: true }),
        new webpack.DefinePlugin({'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'development')})
    ].concat(debug ? [] : [
        new webpack.optimize.DedupePlugin(),
        new webpack.optimize.OccurenceOrderPlugin(),
        new webpack.optimize.UglifyJsPlugin({ mangle: false, sourcemap: false }),
    ])
}
