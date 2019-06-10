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

const LOGIN_URL = configure.apiBaseUrl + '/api/login';
const POST_IMPACT_URL = configure.apiBaseUrl + '/api/impact';

describe('Impact API test:', function () {
  it('Post impact to API', function () {
    return frisby.post(LOGIN_URL, {
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
      .post(POST_IMPACT_URL, {
        phoneId : '358918058705592',
        category : 'Category 1 Test',
        subCategory1 : 'Sub Category 1 Test',
        subCategory2 : 'Sub Category 2 Test',
        subCategory3 : 'Sub Category 3 Test',
        subCategory4 : 'Sub Category 4 Test',
        subCategory5 : 'Sub Category 5 Test',
        victimType : '9',
        reason : 'Test only.',
        numberOfItems : '2',
        type : 'visual',
        patrollerNote : 'Test Patroller Note Test',
        excluded : '1',
      })
      .expect('status', 201)
      .expect('jsonTypes', {
        'data' : {
          'message': 'success',
        }
      });
    });
  });
});

