module.exports = {
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	rules: {
		'@wordpress/i18n-text-domain': [
			'error',
			{
				allowedTextDomain: 'trivia-challenge-block',
			},
		],
		'no-console': [
			'warn',
			{
				allow: [ 'warn', 'error' ],
			},
		],
	},
};
