describe('Tester la page cours', () => {
  it('Pour un stagiaire', () => {
    cy.visit('fr/login');
    cy.get('input[name="username"]').type('lestagiaire');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();

    cy.visit('fr/modules');

    cy.location('pathname', 'fr/modules');
    cy.get('#liste_modules a').first().click();
    cy.contains('Les cours');
  });

  it('Pour un formateur', () => {
    cy.visit('fr/login');
    cy.get('input[name="username"]').type('leformateur');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();
    cy.visit('fr/modules');

    cy.location('pathname', 'fr/modules');
    cy.get('#liste_modules a').first().click();
    cy.contains('Les cours');
  });

  it('Pour un coordinateur', () => {
    cy.visit('fr/login');
    cy.get('input[name="username"]').type('lecoordinateur');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();
    cy.visit('fr/modules');

    cy.location('pathname', 'fr/modules');
    cy.get('#liste_modules a').first().click();
    cy.contains('Les cours');
  });

  it('Pour un responsable', () => {
    cy.visit('fr/login');
    cy.get('input[name="username"]').type('leresponsable');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();
    cy.visit('fr/modules');

    cy.location('pathname', 'fr/modules');
    cy.get('#liste_modules a').first().click();
    cy.contains('Les cours');
  });
})