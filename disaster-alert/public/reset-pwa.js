// PWA Reset Script - Run in Browser Console
// This script clears all PWA dismissal flags for testing

console.log('🔧 PWA Reset Script');
console.log('Clearing all PWA dismissal flags...');

// Clear all PWA-related localStorage items
const pwaDismissalKeys = [
    'pwa-dismissed',
    'pwa-dismissed-dashboard', 
    'pwa-dismissed-alerts',
    'pwa-dismissed-shelters',
    'pwa-dismissed-requests'
];

pwaDismissalKeys.forEach(key => {
    if (localStorage.getItem(key)) {
        localStorage.removeItem(key);
        console.log(`✅ Cleared: ${key}`);
    } else {
        console.log(`ℹ️  Not found: ${key}`);
    }
});

console.log('🎉 PWA Reset Complete!');
console.log('💡 Refresh the page to see PWA install banners again');

// Provide quick function to call
window.resetPWA = function() {
    pwaDismissalKeys.forEach(key => localStorage.removeItem(key));
    console.log('🔄 PWA flags reset! Refresh to see banners.');
};

console.log('💡 You can also call resetPWA() anytime to clear flags');