// Placeholder image generator for KHODERS website
// This script creates placeholder images using canvas

function createPlaceholderImage(width, height, text, bgColor = '#2A4E6D', textColor = '#FFFFFF') {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    
    canvas.width = width;
    canvas.height = height;
    
    // Background
    ctx.fillStyle = bgColor;
    ctx.fillRect(0, 0, width, height);
    
    // Text
    ctx.fillStyle = textColor;
    ctx.font = `bold ${Math.min(width, height) / 4}px Arial`;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(text, width / 2, height / 2);
    
    return canvas.toDataURL('image/png');
}

// Generate placeholder images
const placeholders = {
    'qwe.png': createPlaceholderImage(100, 100, 'K', '#2A4E6D'),
    'team1.jpg': createPlaceholderImage(300, 300, 'CODEO', '#2A4E6D'),
    'team2.jpg': createPlaceholderImage(300, 300, 'ELVIS', '#E87B2A'),
    'team3.jpg': createPlaceholderImage(300, 300, 'SUSSANA', '#F1B521'),
    'team4.jpg': createPlaceholderImage(300, 300, 'KAMAL', '#2A4E6D'),
    'image-1.png': createPlaceholderImage(600, 400, 'PROJECT 1', '#2A4E6D'),
    'image-2.png': createPlaceholderImage(600, 400, 'PROJECT 2', '#E87B2A')
};

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = placeholders;
}

// Browser usage
if (typeof window !== 'undefined') {
    window.placeholders = placeholders;
}