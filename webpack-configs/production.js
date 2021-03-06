const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const path = require("path");

const glob = require("glob-all");
const PurgecssPlugin = require("purgecss-webpack-plugin");

class TailwindExtractor {
  static extract(content) {
    return content.match(/[A-Za-z0-9-_:\/]+/g) || [];
  }
}

module.exports = () => ({
  output: {
    publicPath: "/wp-content/themes/apprenticeship-online-assessment-tool/src/"
  },
  module: {
    rules: [
      {
        test: /\.(sa|sc|c)ss$/,
        use: [
          MiniCssExtractPlugin.loader,
          "css-loader",
          "postcss-loader",
          "sass-loader"
        ]
      }
    ]
  },
  plugins: [
    new PurgecssPlugin({
      // Specify the locations of any files you want to scan for class names.
      paths: glob.sync(
        [
          path.join(__dirname, "../**/*.php"),
          path.join(__dirname, "../src/**/*.*")
        ],
        {
          nodir: true
        }
      ),
      enabled: true,
      only: ["app"],
      extractors: [
        {
          extractor: content => {
            return content.match(/[A-Za-z0-9-_:\/]+/g) || [];
          },
          extensions: ["html", "js", "php", "vue"]
        }
      ]
    }),
    new MiniCssExtractPlugin({ filename: "../css/[name].css" }),
    new OptimizeCSSAssetsPlugin({})
  ]
});
