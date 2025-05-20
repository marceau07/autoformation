describe('Test the login', () => {
  it('Login with good credentials', () => {
    cy.visit('fr/login');

    cy.get('input[name="username"]').type('leformateur');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();

    // On s'assure qu'on est redirigé vers la page d'accueil
    cy.location('pathname', 'fr/home');
    cy.contains('Bienvenue sur la');
  });
  it('Login with bad credentials', () => {
    cy.visit('fr/login');

    cy.get('input[name="username"]').type('leformateur');
    cy.get('input[name="password"]').type('Tests');
    cy.get('form').submit();

    // On s'assure qu'on est redirigé vers la page d'accueil
    cy.location('pathname', 'fr/login');
    cy.contains('Identifiants invalides.');
  });
})