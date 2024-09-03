beforeEach(() => {
    cy.visit('http://localhost:8000')
})

it('renders the editor', () => {
    cy.get('.paver__container').should('be.visible')
    cy.get('.paver__editor').should('be.visible')
    cy.get('.paver__sidebar').should('be.visible')
})

it('renders the editor frame', () => {
    cy.getIframeBody('.paver__editor').within(() => {
        cy.get('.paver__editor-root').eq(0).should('be.visible')
        cy.get('.paver__editor-frame').eq(0).should('be.visible')
    })
})
