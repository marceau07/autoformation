describe('Prueba la página del curso', () => {
  it('Para un pasante', () => {
    cy.visit('es/login');
    cy.get('input[name="username"]').type('lestagiaire');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();

    cy.visit('es/modules');

    cy.location('pathname', 'es/modules');
    cy.get('#liste_modules a').first().click();
    cy.contains('Cursos');
  });

  it('Para un entrenador', () => {
    cy.visit('es/login');
    cy.get('input[name="username"]').type('leformateur');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();
    cy.visit('es/modules');

    cy.location('pathname', 'es/modules');
    cy.get('#liste_modules a').first().click();
    cy.contains('Cursos');
  });

  it('Para un coordinador', () => {
    cy.visit('es/login');
    cy.get('input[name="username"]').type('lecoordinateur');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();
    cy.visit('es/modules');

    cy.location('pathname', 'es/modules');
    cy.get('#liste_modules a').first().click();
    cy.contains('Cursos');
  });

  it('Para un responsable', () => {
    cy.visit('es/login');
    cy.get('input[name="username"]').type('leresponsable');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();
    cy.visit('es/modules');

    cy.location('pathname', 'es/modules');
    cy.get('#liste_modules a').first().click();
    cy.contains('Cursos');
  });
})