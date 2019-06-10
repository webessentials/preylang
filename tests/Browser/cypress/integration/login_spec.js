const mock = require('./../mock');
const config = require('./../config');

describe('Login page ', () => {
  it('Login page is accessible', () => {
    cy.wait
    cy.visit(config.URL_LOGIN);
    cy.contains('Prey Lang Admin');
  });

  it('Login form has a validation', () => {
    cy.visit(config.URL_LOGIN);
    cy.contains('Login').click();
    cy.url().should('include', config.URL_LOGIN);
    cy.contains('Prey Lang Admin');
  });

  it('Login with wrong credentials', () => {
    cy.visit(config.URL_LOGIN);
    cy.get(config.USERNAME_SELECTOR).type('wrong-username');
    cy.get(config.PASSWORD_SELECTOR).type('wrong-password');
    cy.contains('Login').click();
    cy.url().should('include', config.URL_LOGIN);
    cy.contains('Incorrect username.');
  });

  it('Login with valid credentials', () => {
    cy.visit(config.URL_LOGIN);
    cy.get(config.USERNAME_SELECTOR).type(mock.users.admin1.username);
    cy.get(config.PASSWORD_SELECTOR).type(mock.users.admin1.password);
    cy.contains('Login').click();
    cy.url().should('include', config.URL_HOME);
    cy.contains('Prey Lang');
    cy.contains('Dashboard');
  });
});
