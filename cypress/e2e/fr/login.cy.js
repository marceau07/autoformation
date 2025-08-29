describe('Tester la connexion', () => {
  it('Connexion avec de bonnes informations', () => {
    cy.visit('fr/login');

    cy.get('input[name="username"]').type('leformateur');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();

    cy.location('pathname', 'fr/home');
    cy.contains('Bienvenue sur la');
  });
  
  it('Connexion avec de mauvaises informations', () => {
    cy.visit('fr/login');

    cy.get('input[name="username"]').type('leformateur');
    cy.get('input[name="password"]').type('Tests');
    cy.get('form').submit();

    cy.location('pathname', 'fr/login');
    cy.get('.alert-danger').contains('Identifiants invalides.');
  });
})