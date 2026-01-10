const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CopyPlugin = require("copy-webpack-plugin");

class WebpackConfig {
  constructor(mode = 'development') {
    this.mode = mode;
    this.outputPath = path.resolve(__dirname, 'public');
  }

  get isProduction() {
    return this.mode === 'production';
  }

  build() {
    return {
      entry: {
        'imageslideshow': './_dev/admin/imageslideshow.ts',
        'migrate-mute': './_dev/admin/migrate-mute.js',
        'tinymce-plugin-filemanager': './_dev/admin/tinymce/plugin/filemanager.js',
        'tinymce-test': './_dev/admin/tinymce-test.ts',
        'upsert-slideshow': './_dev/admin/upsert-slideshow.ts',
        'upsert-slide': './_dev/admin/upsert-slide.ts',
        'front/imageslideshow': './_dev/front/imageslideshow.ts',
      },
      mode: this.mode,
      output: {
        path: this.outputPath,
        filename: this.#getDestinationFilename('.js'),
        pathinfo: !this.isProduction,
        clean: true
      },
      module: {
        rules: this.#buildRules(),
      },
      plugins: this.#buildPlugins(),
      devtool: this.isProduction
        // https://webpack.js.org/configuration/devtool/#for-production
        ? 'source-map'
        // https://webpack.js.org/configuration/devtool/#for-development
        : 'inline-source-map',
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

    plugins.push(
      new MiniCssExtractPlugin({filename: this.#getDestinationFilename('.css')}),
    )

    // plugins.push(new CopyPlugin({
    //   patterns: [
    //     {
    //       from: path.resolve(__dirname, 'node_modules/tinymce'),
    //       to: this.outputPath + '/tinymce'
    //     }
    //   ]
    // }))

    plugins.push(new CopyPlugin({
      patterns: [
        {
          from: path.resolve(__dirname, 'node_modules/tinymce/icons/default/icons.min.js'),
          to: this.outputPath + '/tinymce/icons/default/icons.min.js'
        },
        // {
        //   from: path.resolve(__dirname, 'node_modules/tinymce/skins/content/default'),
        //   to: this.outputPath + '/tinymce/skins/content/default'
        // },
        {
          from: path.resolve(__dirname, 'node_modules/tinymce/skins/content/default/*.min.css'),
          to: this.outputPath + '/tinymce/skins/content/default/[name][ext]'
        },
        // {
        //   from: path.resolve(__dirname, 'node_modules/tinymce/skins/ui/oxide'),
        //   to: this.outputPath + '/tinymce/skins/ui/oxide'
        // },
        {
          from: path.resolve(__dirname, 'node_modules/tinymce/skins/ui/oxide/*.min.css'),
          to: this.outputPath + '/tinymce/skins/ui/oxide/[name][ext]'
        },
      ]
    }))

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
