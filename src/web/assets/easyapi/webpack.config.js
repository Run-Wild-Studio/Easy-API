/* jshint esversion: 6 */
/* globals module, require */
const {getConfig} = require('@runwildstudio/webpack');
const CopyWebpackPlugin = require('copy-webpack-plugin');

module.exports = getConfig({
  context: __dirname,
  config: {
    entry: {
      EasyApi: './EasyApi.js',
    },
    plugins: [
      new CopyWebpackPlugin({
        patterns: [
          {
            from: './img/**/*',
            to: '.',
          },
        ],
      }),
    ],
  },
});
