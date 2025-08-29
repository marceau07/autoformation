describe('Pruebe la conexión', () => {
  it('Conectando con buena información', () => {
    cy.visit('es/login');

    cy.get('input[name="username"]').type('leformateur');
    cy.get('input[name="password"]').type('T3sts!');
    cy.get('form').submit();

    cy.location('pathname', 'es/home');
    cy.contains('Bienvenido a');
  });
  
  it('Iniciar sesión con información incorrecta', () => {
    cy.visit('es/login');

    cy.get('input[name="username"]').type('leformateur');
    cy.get('input[name="password"]').type('Tests');
    cy.get('form').submit();

    cy.location('pathname', 'es/login');
    cy.get('.alert-danger').contains('Credenciales no válidas.');
  });
})