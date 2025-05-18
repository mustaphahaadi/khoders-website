# Khoders World - Frontend Assignments

Welcome to **Khoders World**, our campus coding club!

Each member has been assigned a standalone **HTML/CSS page or UI component** to design from scratch. You'll each have your **own folder** where you can work on your `index.html` and `style.css`.

I, **Codeo (Project Manager)**, will combine all components into a single website later.

---

## 🎨 Shared Color Palette

To maintain visual consistency across all pages, please use the following **CSS variables** in your `style.css`.

Add this at the top of your CSS file:

```css
/* PRIMARY COLORS */
:root {
  --primary-color: #2A4E6D;     /* Deep Blue */
  --secondary-color: #F1B521;   /* Gold Accent */
  --accent-color: #E87B2A;      /* Warm Orange */
  
  /* NEUTRALS */
  --white: #FFFFFF;
  --light-gray: #F7F7F7;
  --gray: #CCCCCC;
  --dark-gray: #333333;
  --black: #000000;

  /* STATES */
  --success: #28A745;
  --warning: #FFC107;
  --danger: #DC3545;
}
```

## 📂 Component Assignments & Git Workflow

Each team member has their assigned component folder. Here's how to work on your part:

1. **Create and Switch to Your Branch:**
   ```bash
   git checkout -b your-folder-name
   ```
   For example, if you're working on the navbar:
   ```bash
   git checkout -b kamal-navbar
   ```

2. **Work in Your Assigned Folder:**
   - abonopaya-contact: Contact form component
   - addo-error: Error page component
   - akua-projects: Projects showcase component
   - amankwah-testimony: Testimonials component
   - comfort-careers: Careers section component
   - derrick-social: Social media integration component
   - dompreh-blog: Blog section component
   - elvis-home: Home page hero section
   - evans-services: Services section component
   - frederick-login: Login page component
   - gyawu-register: Registration page component
   - kamal-navbar: Navigation bar component
   - mensah-portfolio: Portfolio section component
   - nadia-team: Team members component
   - sussana-about: About us section component
   - william-footer: Footer component

3. **Required Files in Your Folder:**
   - `index.html`
   - `style.css`

4. **Git Workflow:**
   ```bash
   # Add your changes
   git add .
   
   # Commit your changes
   git commit -m "Your meaningful commit message"
   
   # Push to your branch
   git push origin your-folder-name
   ```

## 💻 Development Guidelines

1. **HTML Requirements:**
   - Use semantic HTML5 elements
   - Ensure proper indentation
   - Add meaningful comments
   - Include proper meta tags

2. **CSS Requirements:**
   - Use the provided color variables
   - Write responsive CSS (mobile-first approach)
   - Use consistent naming conventions
   - Minimize redundant code

3. **Best Practices:**
   - Test on different screen sizes
   - Validate your HTML and CSS
   - Optimize images and assets
   - Follow accessibility guidelines

Remember to regularly commit your changes and keep your code clean and well-documented!

