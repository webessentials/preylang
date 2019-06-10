var apiBaseURL = 'http://local-preylang-laravel.wehost.asia';

/*
 * Read the environment variable pasted from the command line.
 *    Ex: API_BASE_URL=https://demo-preylang.wehost.asia
 */
if (process.env['API_BASE_URL'] !== undefined) {
    apiBaseURL = process.env['API_BASE_URL']
}

exports.apiBaseUrl = apiBaseURL;
