// webpack.config.js
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const path = require('path');

module.exports = {
  mode: 'production',
  entry: {
      "main": './js/main.js'
  },
  module: {
    rules: [
      {test: /\.vue$/, loader: 'vue-loader'},
      {test: /\.css$/,use: ['vue-style-loader', 'css-loader', 'sass-loader']},
      {test: /\.(woff|woff2|eot|ttf|otf)$/, use: ['file-loader']},
      {test: /\.(png|svg|jpg|gif)$/, use: {"loader": 'file-loader', options: {esModule: false}}},
    ]
  },
  output: {
      filename: '[name].bundle.js',
      chunkFilename: '[name].bundle.js',
      publicPath: 'dist/',
      path: path.resolve(__dirname, 'dist'),
  },
  plugins: [
    new VueLoaderPlugin()
  ]
}
