Cypress.Commands.add('getIframeBody', (iframeSelector) => {
    return cy
        .get(iframeSelector)
        .its('0.contentDocument.body')
        .should('not.be.empty')
        .then(cy.wrap)
})
