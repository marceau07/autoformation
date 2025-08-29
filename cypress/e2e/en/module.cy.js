describe('Test the module page', () => {
  it('For a trainee', () => {
    cy.visit('en/login');
    cy.get('input[name="username"]').type('lestagiaire');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();

    cy.visit('en/modules');

    cy.location('pathname', 'en/modules');
    cy.contains('Modules');
  });

  it('For a trainer', () => {
    cy.visit('en/login');
    cy.get('input[name="username"]').type('leformateur');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();
    cy.visit('en/modules');

    cy.location('pathname', 'en/modules');
    cy.contains('Modules');
  });

  it('For a coordinator', () => {
    cy.visit('en/login');
    cy.get('input[name="username"]').type('lecoordinateur');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();
    cy.visit('en/modules');

    cy.location('pathname', 'en/modules');
    cy.contains('Modules');
  });

  it('For a responsible', () => {
    cy.visit('en/login');
    cy.get('input[name="username"]').type('leresponsable');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();
    cy.visit('en/modules');

    cy.location('pathname', 'en/modules');
    cy.contains('Modules');
  });
})