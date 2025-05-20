describe('Test the module page Trainee', () => {
  beforeEach(() => {
    cy.visit('fr/login');
    cy.get('input[name="username"]').type('lestagiaire');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();
  });

  it('For a trainee', () => {
    cy.visit('fr/modules');

    cy.location('pathname', 'http://localhost:8000/fr/modules');
    cy.contains('Les modules');
  });
})

describe('Test the module page Trainer', () => {
  beforeEach(() => {
    cy.visit('fr/login');
    cy.get('input[name="username"]').type('leformateur');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();
  });

  it('For a trainee', () => {
    cy.visit('fr/modules');

    cy.location('pathname', 'http://localhost:8000/fr/modules');
    cy.contains('Les modules');
  });
})