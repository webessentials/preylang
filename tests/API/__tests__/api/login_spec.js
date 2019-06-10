const configure = require('./../configure');
const frisby = require('frisby');
const mock = require('./../mock');

/*
 * Global set up
 */
frisby.globalSetup({
    request: {
        headers: {
            'Content-Type': 'application/json'
        }
    }
});

const loginURL = configure.apiBaseUrl + '/api/login';
const mockURL = configure.apiBaseUrl + '/api/mock';

describe('Login API test:', function () {
    it('Get validated access token', function () {
        return frisby.post(loginURL,{
            api_key : mock.phones.we.imei,
            api_secret: mock.phones.we.password
        })
            .expect('status', 200)
            .expect('json', 'token_type', 'Bearer')
            .then(function (res) {
                const accessToken = res.json.access_token;
                return frisby.setup({
                    request: {
                        headers: {
                            'Authorization': 'Bearer ' + accessToken
                        }
                    }
                })
                    .get(mockURL)
                    .expect('status', 200)
                    .expect('json', 'message', 'Valid Access Token')
            });
    });
});
