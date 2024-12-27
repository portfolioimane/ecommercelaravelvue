const mix = require('laravel-mix');
const webpack = require('webpack');
const dotenv = require('dotenv');

// Load environment variables from .env file
dotenv.config();

// Webpack Mix Configuration
mix.js('resources/js/app.js', 'public/js')
    .vue() // Ensure the Vue loader is enabled
    .css('resources/css/app.css', 'public/css')
    .webpackConfig({
        plugins: [
            new webpack.DefinePlugin({
                // Define Vue feature flags here
                '__VUE_PROD_HYDRATION_MISMATCH_DETAILS__': JSON.stringify(true),
                'process.env': {
                   VUE_APP_STRIPE_KEY: JSON.stringify(process.env.STRIPE_PUBLIC_KEY),
                   VUE_APP_PAYPAL_CLIENT_ID: JSON.stringify(process.env.PAYPAL_CLIENT_ID),

                },
            }),
        ],
    });
