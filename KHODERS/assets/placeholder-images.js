// Placeholder image generator for KHODERS website
// This script creates placeholder images using Canvas API

class PlaceholderImageGenerator {
    constructor() {
        this.colors = {
            primary: '#2A4E6D',
            secondary: '#F1B521',
            accent: '#E87B2A',
            success: '#28A745',
            light: '#F7F7F7'
        };
    }

    generateImage(width, height, text, bgColor = this.colors.primary, textColor = '#FFFFFF') {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        canvas.width = width;
        canvas.height = height;
        
        // Background
        ctx.fillStyle = bgColor;
        ctx.fillRect(0, 0, width, height);
        
        // Add gradient
        const gradient = ctx.createLinearGradient(0, 0, width, height);
        gradient.addColorStop(0, bgColor);
        gradient.addColorStop(1, this.adjustBrightness(bgColor, -20));
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, width, height);
        
        // Text
        ctx.fillStyle = textColor;
        ctx.font = `bold ${Math.min(width, height) / 8}px Arial`;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(text, width / 2, height / 2);
        
        return canvas.toDataURL();
    }

    adjustBrightness(hex, percent) {
        const num = parseInt(hex.replace("#", ""), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) + amt;
        const G = (num >> 8 & 0x00FF) + amt;
        const B = (num & 0x0000FF) + amt;
        return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
            (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
            (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
    }

    generateAllPlaceholders() {
        const placeholders = {
            // Hero images
            'qwe.png': this.generateImage(400, 300, 'KHODERS\nLOGO', this.colors.primary),
            'hero-bg.jpg': this.generateImage(1920, 1080, 'CODING\nCLUB', this.colors.accent),
            
            // About image
            'about-image.jpg': this.generateImage(500, 400, 'ABOUT\nKHODERS', this.colors.secondary),
            
            // Project images
            'project1.jpg': this.generateImage(400, 250, 'E-COMMERCE\nPLATFORM', this.colors.success),
            'project2.jpg': this.generateImage(400, 250, 'MOBILE\nAPP', this.colors.accent),
            'project3.jpg': this.generateImage(400, 250, 'AI\nCHATBOT', this.colors.primary),
            
            // Team images
            'team1.jpg': this.generateImage(300, 300, 'CODEO\nPM', this.colors.primary),
            'team2.jpg': this.generateImage(300, 300, 'ELVIS\nFRONTEND', this.colors.secondary),
            'team3.jpg': this.generateImage(300, 300, 'SUSSANA\nUI/UX', this.colors.accent),
            'team4.jpg': this.generateImage(300, 300, 'KAMAL\nFRONTEND', this.colors.success),
            
            // Testimonial images
            'testimonial1.jpg': this.generateImage(80, 80, 'SJ', this.colors.primary),
            'testimonial2.jpg': this.generateImage(80, 80, 'MC', this.colors.secondary),
            'testimonial3.jpg': this.generateImage(80, 80, 'ED', this.colors.accent),
            
            // Blog images
            'blog1.jpg': this.generateImage(400, 200, 'REACT\nGUIDE', this.colors.primary),
            'blog2.jpg': this.generateImage(400, 200, 'TECH\nJOBS', this.colors.secondary),
            'blog3.jpg': this.generateImage(400, 200, 'MACHINE\nLEARNING', this.colors.accent),
            
            // Portfolio images
            'portfolio1.jpg': this.generateImage(400, 300, 'E-LEARNING\nPLATFORM', this.colors.success),
            'portfolio2.jpg': this.generateImage(400, 300, 'TASK\nMANAGER', this.colors.primary),
            'portfolio3.jpg': this.generateImage(400, 300, 'WEATHER\nDASHBOARD', this.colors.accent)
        };

        return placeholders;
    }
}

// Initialize and create placeholder images when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const generator = new PlaceholderImageGenerator();
    const placeholders = generator.generateAllPlaceholders();
    
    // Replace missing images with placeholders
    Object.keys(placeholders).forEach(filename => {
        const images = document.querySelectorAll(`img[src*="${filename}"]`);
        images.forEach(img => {
            img.onerror = function() {
                this.src = placeholders[filename];
                this.onerror = null;
            };
            
            // Trigger error if image doesn't exist
            if (!img.complete || img.naturalHeight === 0) {
                img.src = placeholders[filename];
            }
        });
    });
});

export default PlaceholderImageGenerator;