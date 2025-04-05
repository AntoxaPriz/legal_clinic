const path = require('path');

module.exports = {
    mode: 'production', // Включает tree-shaking и минификацию
    entry: {
        main: './web/js/main.js',
        core: './web/js/core.js',
        nav: './web/js/nav.js',
        profile: './web/js/profile.js',
        cases: './web/js/cases.js',
        tasks: './web/js/tasks.js',
        clients: './web/js/clients.js',
        documents: './web/js/documents.js',
        billing: './web/js/billing.js',
        history: './web/js/history.js',
        reports: './web/js/reports.js',
        settings: './web/js/settings.js',
        contact: './web/js/contact.js',
        help: './web/js/help.js',
        admin: './web/js/admin.js'
    },
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, 'web/js/dist'),
        clean: true // Очищает папку dist перед сборкой
    },
    optimization: {
        usedExports: true // Включает tree-shaking
    }
};