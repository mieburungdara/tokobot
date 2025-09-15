const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  mode: 'development', // Can be 'production' for optimized builds
  entry: {
    main: './src/index.js',
    styles: './src/styles.css',
  },
  output: {
    filename: '[name].[contenthash].js',
    path: path.resolve(__dirname, 'public/build'),
    clean: true,
  },
  module: {
    rules: [
      {
        test: /\.css$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader'],
      },
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '[name].[contenthash].css',
    }),
  ],
};