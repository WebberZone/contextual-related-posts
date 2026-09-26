const config = require( '@wordpress/scripts/config/eslint.config.cjs' );

module.exports = [
	...config,
	{
		files: [
			'includes/pro/blocks/src/advanced/**/*.js',
			'includes/pro/blocks/src/featured-image/**/*.js',
		],
		rules: {
			'import/no-unresolved': 'off',
			'import/no-extraneous-dependencies': 'off',
		},
	},
];
