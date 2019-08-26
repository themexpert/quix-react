const mix = require('laravel-mix');

mix
    .react('build.js', 'dist/')
    .webpackConfig({
      module: {
        rules: [
          {
            test: /\.twig$/,
            use: {
              loader: 'text-loader',
              options: {
                // See options section below
              },
            }
          }
        ]
      }
    })
    .sourceMaps();
