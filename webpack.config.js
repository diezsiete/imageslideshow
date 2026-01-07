const path = require('path');

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
        'slideshow': './_dev/admin/imageslideshow.ts'
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

    return rules;
  }

  #buildPlugins() {
    const plugins = [];
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
