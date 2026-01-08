const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

class WebpackConfig {
  constructor(mode = 'development') {
    this.mode = mode;
  }

  get isProduction() {
    return this.mode === 'production';
  }

  build() {
    return {
      entry: {
        'imageslideshow': './_dev/admin/imageslideshow.ts',
        'imageslideshow-form': './_dev/admin/imageslideshow-form.ts',
      },
      mode: this.mode,
      output: {
        path: path.resolve(__dirname, 'public'),
        filename: this.#getDestinationFilename('.js'),
        pathinfo: !this.isProduction,
      },
      module: {
        rules: this.#buildRules(),
      },
      plugins: this.#buildPlugins(),
    }
  }

  #getDestinationFilename(append = '') {
    return (this.isProduction ? '[name].[contenthash]' : '[name]') + append
  }

  #buildRules() {
    const rules = [];
    rules.push({
      test: /\.(js|jsx|ts|tsx)?$/,
      exclude: /(node_modules)/,
      resolve: {
        fullySpecified: false,
        extensions: ['.js', '.ts'],
      },
      use: {
        loader: 'esbuild-loader',
        options: {
          loader: 'ts',
          target: 'es2015',
        },
      },
    });

    rules.push({
      test: /\.(sa|sc|c)ss$/,
      use: [
        MiniCssExtractPlugin.loader,
        {
          loader: 'css-loader',
          options: {
            sourceMap: !this.isProduction
          }
        },
        {
          loader: 'postcss-loader',
          options: {
            sourceMap: !this.isProduction
          }
        },
        {
          loader: 'sass-loader',
          options: {
            sourceMap: true,
          }
        }
      ]
    })

    return rules;
  }

  #buildPlugins() {
    const plugins = [];

    // plugins.push(new MiniCssExtractPlugin({
    //   filename: path.join('..', 'css', this.#getDestinationFilename('.css')),
    //   chunkFilename: path.join('..', 'css', this.#getDestinationFilename('.css'))
    // }))

    plugins.push(
      new MiniCssExtractPlugin({filename: this.#getDestinationFilename('.css')}),
    )

    return plugins;
  }
}

module.exports = (env, argv) => {
  const wpc = new WebpackConfig(argv.mode);

  const config = wpc.build();
  // console.log(config);
  // process.exit();
  return config;
};
