describe('Test the connexion', () => {
  it('Login with good credentials', () => {
    cy.visit('en/login');

    cy.get('input[name="username"]').type('leformateur');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();

    cy.location('pathname', 'en/home');
    cy.contains('Welcome to');
  });
  
  it('Login with bad credentials', () => {
    cy.visit('en/login');

    cy.get('input[name="username"]').type('leformateur');
    cy.get('input[name="password"]').type('Tests');
    cy.get('form').submit();

    cy.location('pathname', 'en/login');
    cy.get('.alert-danger').contains('Invalid credentials.');
  });
})