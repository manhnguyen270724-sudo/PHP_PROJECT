/**
 * Search Page JavaScript
 * Professional client-side functionality
 * 
 * @author MyLiShop Team
 * @version 2.0
 */

(function() {
    'use strict';

    // ===============================================
    // CONSTANTS & CONFIGURATION
    // ===============================================
    
    const CONFIG = {
        SCROLL_THRESHOLD: 300,
        ANIMATION_DURATION: 500,
        ANIMATION_DELAY_INCREMENT: 50
    };

    // ===============================================
    // DOM ELEMENTS
    // ===============================================
    
    const elements = {
        sortSelect: document.getElementById('sortSelect'),
        productGrid: document.getElementById('productGrid'),
        backToTop: document.querySelector('.back-to-top'),
        productCards: null // Will be populated later
    };

    // ===============================================
    // SORT FUNCTIONALITY
    // ===============================================
    
    /**
     * Sort products based on selected criteria
     * @param {string} sortValue - The sort option value
     */
    function sortProducts(sortValue) {
        if (!elements.productGrid) return;
        
        const products = Array.from(elements.productGrid.querySelectorAll('.product-card'));
        
        products.sort((a, b) => {
            switch(sortValue) {
                case 'price-asc':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                
                case 'price-desc':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                
                case 'name-asc':
                    return a.dataset.name.localeCompare(b.dataset.name, 'vi');
                
                case 'name-desc':
                    return b.dataset.name.localeCompare(a.dataset.name, 'vi');
                
                default:
                    return 0;
            }
        });
        
        // Re-append sorted products with animation
        products.forEach((product, index) => {
            // Remove existing animation
            product.style.animation = 'none';
            
            // Trigger reflow to restart animation
            void product.offsetWidth;
            
            // Add staggered animation
            setTimeout(() => {
                product.style.animation = `fadeInUp ${CONFIG.ANIMATION_DURATION}ms ease forwards`;
                elements.productGrid.appendChild(product);
            }, index * CONFIG.ANIMATION_DELAY_INCREMENT);
        });
        
        console.log(`Products sorted by: ${sortValue}`);
    }

    /**
     * Initialize sort functionality
     */
    function initSort() {
        if (elements.sortSelect && elements.productGrid) {
            elements.sortSelect.addEventListener('change', function() {
                const sortValue = this.value;
                sortProducts(sortValue);
                
                // Save preference to sessionStorage
                try {
                    sessionStorage.setItem('searchSortPreference', sortValue);
                } catch (e) {
                    console.warn('Could not save sort preference:', e);
                }
            });
            
            // Restore previous sort preference
            try {
                const savedSort = sessionStorage.getItem('searchSortPreference');
                if (savedSort && savedSort !== 'default') {
                    elements.sortSelect.value = savedSort;
                    sortProducts(savedSort);
                }
            } catch (e) {
                console.warn('Could not restore sort preference:', e);
            }
        }
    }

    // ===============================================
    // KEYWORD HIGHLIGHTING
    // ===============================================
    
    /**
     * Highlight search keyword in product names
     * @param {string} keyword - The search keyword to highlight
     */
    function highlightKeyword(keyword) {
        if (!keyword || keyword.trim() === '') return;
        
        const productNames = document.querySelectorAll('.product-name');
        const escapedKeyword = keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${escapedKeyword})`, 'gi');
        
        productNames.forEach(nameElement => {
            const originalText = nameElement.textContent;
            const highlightedHTML = originalText.replace(
                regex, 
                '<mark style="background: #ffeb3b; padding: 2px 4px; border-radius: 3px; font-weight: 700;">$1</mark>'
            );
            
            if (originalText !== highlightedHTML) {
                nameElement.innerHTML = highlightedHTML;
            }
        });
        
        console.log(`Highlighted keyword: "${keyword}"`);
    }

    /**
     * Get search keyword from page
     * @returns {string} The search keyword
     */
    function getSearchKeyword() {
        const keywordElement = document.querySelector('.search-keyword');
        return keywordElement ? keywordElement.textContent.trim() : '';
    }

    // ===============================================
    // BACK TO TOP FUNCTIONALITY
    // ===============================================
    
    /**
     * Show/hide back to top button based on scroll position
     */
    function handleBackToTopVisibility() {
        if (!elements.backToTop) return;
        
        if (window.pageYOffset > CONFIG.SCROLL_THRESHOLD) {
            elements.backToTop.style.display = 'flex';
            elements.backToTop.style.opacity = '1';
        } else {
            elements.backToTop.style.opacity = '0';
            setTimeout(() => {
                if (window.pageYOffset <= CONFIG.SCROLL_THRESHOLD) {
                    elements.backToTop.style.display = 'none';
                }
            }, 300);
        }
    }

    /**
     * Scroll to top smoothly
     * @param {Event} e - Click event
     */
    function scrollToTop(e) {
        e.preventDefault();
        
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        
        // Focus on main content for accessibility
        const mainContent = document.querySelector('main') || document.querySelector('.container');
        if (mainContent) {
            mainContent.focus();
        }
    }

    /**
     * Initialize back to top functionality
     */
    function initBackToTop() {
        if (elements.backToTop) {
            // Initially hide
            elements.backToTop.style.display = 'none';
            
            // Listen to scroll events (throttled)
            let scrollTimeout;
            window.addEventListener('scroll', () => {
                if (scrollTimeout) {
                    window.cancelAnimationFrame(scrollTimeout);
                }
                
                scrollTimeout = window.requestAnimationFrame(() => {
                    handleBackToTopVisibility();
                });
            });
            
            // Listen to click events
            elements.backToTop.addEventListener('click', scrollToTop);
            
            // Check initial position
            handleBackToTopVisibility();
        }
    }

    // ===============================================
    // PRODUCT CARD INTERACTIONS
    // ===============================================
    
    /**
     * Add loading state to product card when cart button clicked
     * @param {Event} e - Click event
     */
    function handleAddToCart(e) {
        const button = e.currentTarget;
        const card = button.closest('.product-card');
        
        if (card) {
            // Add loading state
            card.classList.add('loading');
            
            // Show feedback
            const icon = button.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-shopping-cart');
                icon.classList.add('fa-check');
                
                setTimeout(() => {
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-shopping-cart');
                    card.classList.remove('loading');
                }, 1000);
            }
        }
    }

    /**
     * Initialize product card interactions
     */
    function initProductCards() {
        const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');
        
        addToCartButtons.forEach(button => {
            button.addEventListener('click', handleAddToCart);
        });
    }

    // ===============================================
    // KEYBOARD NAVIGATION
    // ===============================================
    
    /**
     * Handle keyboard navigation for product grid
     * @param {KeyboardEvent} e - Keyboard event
     */
    function handleKeyboardNavigation(e) {
        if (!elements.productGrid) return;
        
        const focusedElement = document.activeElement;
        const isProductCard = focusedElement.closest('.product-card');
        
        if (!isProductCard) return;
        
        const cards = Array.from(elements.productGrid.querySelectorAll('.product-card'));
        const currentIndex = cards.indexOf(isProductCard);
        
        let nextIndex = -1;
        
        switch(e.key) {
            case 'ArrowRight':
                nextIndex = currentIndex + 1;
                break;
            case 'ArrowLeft':
                nextIndex = currentIndex - 1;
                break;
            case 'ArrowDown':
                // Assuming 4 columns on desktop
                nextIndex = currentIndex + 4;
                break;
            case 'ArrowUp':
                nextIndex = currentIndex - 4;
                break;
            default:
                return;
        }
        
        // Navigate to next card if valid
        if (nextIndex >= 0 && nextIndex < cards.length) {
            e.preventDefault();
            const nextCard = cards[nextIndex];
            const nextLink = nextCard.querySelector('a');
            if (nextLink) {
                nextLink.focus();
            }
        }
    }

    /**
     * Initialize keyboard navigation
     */
    function initKeyboardNavigation() {
        if (elements.productGrid) {
            document.addEventListener('keydown', handleKeyboardNavigation);
        }
    }

    // ===============================================
    // ANALYTICS & TRACKING
    // ===============================================
    
    /**
     * Track search analytics (placeholder for future implementation)
     * @param {string} keyword - Search keyword
     * @param {number} resultCount - Number of results
     */
    function trackSearch(keyword, resultCount) {
        // Example: Send to analytics service
        console.log('Search tracked:', {
            keyword: keyword,
            results: resultCount,
            timestamp: new Date().toISOString()
        });
        
        // Uncomment when analytics is set up:
        // if (typeof gtag !== 'undefined') {
        //     gtag('event', 'search', {
        //         search_term: keyword,
        //         search_results: resultCount
        //     });
        // }
    }

    // ===============================================
    // PERFORMANCE MONITORING
    // ===============================================
    
    /**
     * Log performance metrics
     */
    function logPerformance() {
        if (window.performance && window.performance.timing) {
            const perfData = window.performance.timing;
            const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
            
            console.log('Page Performance:', {
                pageLoadTime: pageLoadTime + 'ms',
                domContentLoaded: (perfData.domContentLoadedEventEnd - perfData.navigationStart) + 'ms'
            });
        }
    }

    // ===============================================
    // INITIALIZATION
    // ===============================================
    
    /**
     * Initialize all functionality when DOM is ready
     */
    function init() {
        console.log('Search page initialized');
        
        // Initialize features
        initSort();
        initBackToTop();
        initProductCards();
        initKeyboardNavigation();
        
        // Highlight search keyword
        const keyword = getSearchKeyword();
        if (keyword) {
            highlightKeyword(keyword);
            
            // Track search
            const resultCount = elements.productGrid ? 
                elements.productGrid.querySelectorAll('.product-card').length : 0;
            trackSearch(keyword, resultCount);
        }
        
        // Log performance metrics (development only)
        if (console && console.log) {
            window.addEventListener('load', logPerformance);
        }
    }

    // ===============================================
    // EVENT LISTENERS
    // ===============================================
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ===============================================
    // EXPORT FOR TESTING (Optional)
    // ===============================================
    
    // Expose functions for testing if needed
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = {
            sortProducts,
            highlightKeyword,
            scrollToTop
        };
    }

})();