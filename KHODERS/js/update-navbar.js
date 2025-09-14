// Quick script to update navbar consistency across all pages
const fs = require('fs');
const path = require('path');

const files = [
    'blog.html', 'careers.html', 'contact.html', 'events.html', 
    'faq.html', 'projects.html', 'register.html', 'team.html',
    'privacy-policy.html', 'terms-of-service.html', 'code-of-conduct.html'
];

const oldNavbar = `<ul class="nav-menu" role="menubar">`;
const newNavbar = `<ul class="nav-menu" id="nav-menu" role="menubar">`;

const oldHamburger = `<button class="hamburger" aria-label="Toggle navigation menu" aria-expanded="false">`;
const newHamburger = `<button class="hamburger" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="nav-menu">`;

files.forEach(file => {
    try {
        if (fs.existsSync(file)) {
            let content = fs.readFileSync(file, 'utf8');
            content = content.replace(oldNavbar, newNavbar);
            content = content.replace(oldHamburger, newHamburger);
            fs.writeFileSync(file, content);
            console.log(`Updated ${file}`);
        }
    } catch (error) {
        console.log(`Error updating ${file}:`, error.message);
    }
});

console.log('Navbar update complete!');