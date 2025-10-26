/**
 * Dark Mode Toggle Functionality
 * Handles switching between light and dark themes
 */

(function() {
    'use strict';

    // Check if dark mode is enabled
    function isDarkMode() {
        return document.documentElement.classList.contains('dark') || 
               localStorage.getItem('shadcn_dark_mode') === 'true';
    }

    // Helper function to set cookie
    function setCookie(name, value, days = 365) {
        try {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = date.toUTCString();
            // Include SameSite=Lax for better cross-site compatibility
            const secureFlag = window.location.protocol === 'https:' ? ';Secure' : '';
            document.cookie = `${name}=${value};expires=${expires};path=/;SameSite=Lax${secureFlag}`;
        } catch (e) {
            console.error('Failed to set cookie:', e);
        }
    }

    // Toggle dark mode
    function toggleDarkMode() {
        const isDark = isDarkMode();
        const newMode = !isDark;
        
        // Update DOM
        if (newMode) {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        }
        
        // Update localStorage
        localStorage.setItem('shadcn_dark_mode', newMode.toString());
        
        // Update cookie for server-side rendering (to prevent FOUC on next page load)
        const cookieValue = newMode ? 'dark' : 'light';
        setCookie('shadcn-theme-mode', cookieValue);
        
        // Update button icon
        updateToggleButton(newMode);
        
        // Dispatch custom event
        document.dispatchEvent(new CustomEvent('darkModeChanged', {
            detail: { isDark: newMode }
        }));
    }

    // Update toggle button icon
    function updateToggleButton(isDark) {
        const toggleButton = document.querySelector('.dark-mode-toggle') || document.querySelector('.dark-mode-toggle-block .dark-mode-toggle');
        if (!toggleButton) return;
        
        const icon = toggleButton.querySelector('svg');
        if (!icon) return;
        
        // Update icon based on current mode
        if (isDark) {
            toggleButton.setAttribute('aria-label', 'Switch to light mode');
        } else {
            toggleButton.setAttribute('aria-label', 'Switch to dark mode');
        }
    }

    // Helper function to get cookie value
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    // Initialize dark mode on page load
    function initDarkMode() {
        // Check for saved preference or default to light mode
        const cookieMode = getCookie('shadcn-theme-mode');
        const savedMode = localStorage.getItem('shadcn_dark_mode');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        let shouldBeDark = false;
        
        // Priority: cookie (set by inline script) > localStorage > system preference
        if (cookieMode === 'dark') {
            shouldBeDark = true;
        } else if (cookieMode === 'light') {
            shouldBeDark = false;
        } else if (savedMode !== null) {
            shouldBeDark = savedMode === 'true';
        } else if (prefersDark) {
            shouldBeDark = true;
        }
        
        // Apply dark mode if needed
        if (shouldBeDark) {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
        }
        
        // Update toggle button
        updateToggleButton(shouldBeDark);
    }

    // Handle system theme changes
    function handleSystemThemeChange(e) {
        // Only apply system preference if user hasn't manually set a preference
        if (localStorage.getItem('shadcn_dark_mode') === null) {
            if (e.matches) {
                document.documentElement.classList.add('dark');
                document.documentElement.style.colorScheme = 'dark';
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.style.colorScheme = 'light';
            }
            updateToggleButton(e.matches);
        }
    }

    // Add event listeners
    function addEventListeners() {
        // Dark mode toggle button (both old HTML and new block)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.dark-mode-toggle') || e.target.closest('.dark-mode-toggle-block .dark-mode-toggle')) {
                e.preventDefault();
                toggleDarkMode();
            }
        });
        
        // System theme change
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', handleSystemThemeChange);
        
        // Keyboard accessibility
        document.addEventListener('keydown', function(e) {
            if ((e.target.closest('.dark-mode-toggle') || e.target.closest('.dark-mode-toggle-block .dark-mode-toggle')) && (e.key === 'Enter' || e.key === ' ')) {
                e.preventDefault();
                toggleDarkMode();
            }
        });
    }

    // Initialize when DOM is ready
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initDarkMode();
                addEventListeners();
            });
        } else {
            initDarkMode();
            addEventListeners();
        }
    }

    // Start initialization
    init();

    // Expose functions globally for external use
    window.ShadcnWPTheme = {
        toggleDarkMode: toggleDarkMode,
        isDarkMode: isDarkMode,
        updateToggleButton: updateToggleButton
    };

})();
