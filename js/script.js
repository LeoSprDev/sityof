/**
 * Site Vitrine Psychologue - Script Principal
 * Fonctionnalités : Navigation smooth, animations au scroll, formulaire de contact
 * Respect des préférences d'accessibilité (prefers-reduced-motion)
 */

(function() {
    'use strict';

    // Variables globales
    let isReducedMotion = false;
    const navbar = document.getElementById('navbar');
    const navMenu = document.getElementById('nav-menu');
    const navHamburger = document.getElementById('nav-hamburger');
    const contactForm = document.getElementById('contact-form');
    const formMessage = document.getElementById('form-message');

    /**
     * Initialisation du site
     */
    function init() {
        checkReducedMotion();
        setupNavigation();
        setupScrollAnimations();
        setupContactForm();
        setupParallaxEffects();
        addAccessibilityFeatures();
        
        window.addEventListener('resize', debounce(handleResize, 100));
        console.log('Site psychologue initialisé avec succès');
    }

    /**
     * Vérifie les préférences de mouvement de l'utilisateur
     */
    function checkReducedMotion() {
        isReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        window.matchMedia('(prefers-reduced-motion: reduce)').addEventListener('change', (e) => {
            isReducedMotion = e.matches;
            if (isReducedMotion) {
                document.querySelectorAll('.animate-on-scroll').forEach(el => {
                    el.style.animation = 'none';
                    el.style.transition = 'none';
                });
            }
        });
    }

    /**
     * Configuration de la navigation
     */
    function setupNavigation() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', handleSmoothScroll);
        });

        if (navHamburger) {
            navHamburger.addEventListener('click', toggleMobileMenu);
        }

        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });

        let lastScrollY = window.scrollY;
        window.addEventListener('scroll', debounce(() => {
            handleNavbarScroll(lastScrollY);
            lastScrollY = window.scrollY;
        }, 10));

        document.addEventListener('keydown', handleKeyboardNavigation);
    }

    /**
     * Gestion du scroll smooth
     */
    function handleSmoothScroll(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
            const offsetTop = targetElement.offsetTop - 80;
            
            if (isReducedMotion) {
                window.scrollTo(0, offsetTop);
            } else {
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }

            updateActiveNavigation(targetId);
            
            setTimeout(() => {
                targetElement.focus({ preventScroll: true });
            }, isReducedMotion ? 0 : 500);
        }
    }

    /**
     * Gestion du menu mobile
     */
    function toggleMobileMenu() {
        const isActive = navMenu.classList.contains('active');
        
        navMenu.classList.toggle('active');
        navHamburger.classList.toggle('active');
        
        navMenu.setAttribute('aria-expanded', !isActive);
        navHamburger.setAttribute('aria-expanded', !isActive);
        
        document.body.style.overflow = !isActive ? 'hidden' : '';
    }

    /**
     * Ferme le menu mobile
     */
    function closeMobileMenu() {
        navMenu.classList.remove('active');
        navHamburger.classList.remove('active');
        navMenu.setAttribute('aria-expanded', 'false');
        navHamburger.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    /**
     * Gestion du scroll de la navbar
     */
    function handleNavbarScroll(lastScrollY) {
        const currentScrollY = window.scrollY;
        
        if (navbar) {
            if (currentScrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.boxShadow = 'none';
            }
        }

        updateActiveNavigationOnScroll();
    }

    /**
     * Met à jour la navigation active
     */
    function updateActiveNavigation(activeId) {
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === activeId) {
                link.classList.add('active');
            }
        });
    }

    /**
     * Met à jour la navigation active au scroll
     */
    function updateActiveNavigationOnScroll() {
        const sections = document.querySelectorAll('section[id]');
        const scrollPos = window.scrollY + 100;

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');

            if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                updateActiveNavigation(`#${sectionId}`);
            }
        });
    }

    /**
     * Configuration des animations au scroll
     */
    function setupScrollAnimations() {
        if (isReducedMotion) return;

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateElement(entry.target);
                }
            });
        }, observerOptions);

        const elementsToAnimate = [
            '.hero-content',
            '.hero-gallery',
            '.timeline-item',
            '.interet-card',
            '.approche-item',
            '.contact-info',
            '.contact-form-container'
        ];

        elementsToAnimate.forEach(selector => {
            document.querySelectorAll(selector).forEach(el => {
                el.classList.add('animate-on-scroll');
                observer.observe(el);
            });
        });
    }

    /**
     * Anime un élément selon son type
     */
    function animateElement(element) {
        if (isReducedMotion) {
            element.style.opacity = '1';
            return;
        }

        element.classList.add('animated');
        
        if (element.classList.contains('hero-content')) {
            element.classList.add('fade-left');
        } else if (element.classList.contains('hero-gallery')) {
            element.classList.add('fade-right');
        } else {
            element.classList.add('fade-up');
        }
    }

    /**
     * Configuration des effets parallax subtils
     */
    function setupParallaxEffects() {
        if (isReducedMotion) return;

        const parallaxElements = document.querySelectorAll('.gallery-item');
        
        window.addEventListener('scroll', debounce(() => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            
            parallaxElements.forEach((el, index) => {
                const speed = (index + 1) * 0.1;
                el.style.transform = `translateY(${rate * speed}px)`;
            });
        }, 16));
    }

    /**
     * Configuration du formulaire de contact
     */
    function setupContactForm() {
        if (!contactForm) return;

        contactForm.addEventListener('submit', handleFormSubmit);
        
        const inputs = contactForm.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', validateInput);
            input.addEventListener('input', clearValidationError);
        });
    }

    /**
     * Gestion de la soumission du formulaire
     */
    async function handleFormSubmit(e) {
        e.preventDefault();
        
        const submitBtn = contactForm.querySelector('.btn-submit');
        const formData = new FormData(contactForm);
        
        if (!validateForm()) {
            showFormMessage('Veuillez corriger les erreurs dans le formulaire.', 'error');
            return;
        }

        submitBtn.classList.add('loading');
        submitBtn.disabled = true;

        try {
            const response = await fetch('./php/contact.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showFormMessage('Votre message a été envoyé avec succès ! Je vous recontacterai dans les plus brefs délais.', 'success');
                contactForm.reset();
            } else {
                showFormMessage(result.message || 'Une erreur est survenue lors de l\'envoi.', 'error');
            }
        } catch (error) {
            console.error('Erreur lors de l\'envoi:', error);
            showFormMessage('Erreur de connexion. Veuillez réessayer plus tard.', 'error');
        } finally {
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        }
    }

    /**
     * Validation complète du formulaire
     */
    function validateForm() {
        let isValid = true;
        const requiredFields = contactForm.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!validateInput({ target: field })) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    /**
     * Validation d'un champ individuel
     */
    function validateInput(e) {
        const field = e.target;
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'Ce champ est requis.';
        }
        
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Veuillez saisir une adresse email valide.';
            }
        }
        
        if (field.type === 'tel' && value) {
            const phoneRegex = /^(?:(?:\+|00)33[\s.-]?|0)[1-9](?:[\s.-]?\d{2}){4}$/;
            if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                isValid = false;
                errorMessage = 'Veuillez saisir un numéro de téléphone valide.';
            }
        }

        showFieldError(field, isValid ? '' : errorMessage);
        return isValid;
    }

    /**
     * Efface les erreurs de validation lors de la saisie
     */
    function clearValidationError(e) {
        const field = e.target;
        if (field.value.trim()) {
            showFieldError(field, '');
        }
    }

    /**
     * Affiche une erreur sur un champ
     */
    function showFieldError(field, message) {
        const existingError = field.parentNode.querySelector('.field-error');
        
        if (existingError) {
            existingError.remove();
        }
        
        if (message) {
            field.style.borderColor = '#e53e3e';
            const errorElement = document.createElement('span');
            errorElement.className = 'field-error';
            errorElement.textContent = message;
            errorElement.style.cssText = 'color: #e53e3e; font-size: 0.875rem; margin-top: 0.25rem; display: block;';
            field.parentNode.appendChild(errorElement);
        } else {
            field.style.borderColor = '';
        }
    }

    /**
     * Affiche un message de statut du formulaire
     */
    function showFormMessage(message, type) {
        if (!formMessage) return;
        
        formMessage.textContent = message;
        formMessage.className = `form-message ${type}`;
        formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        if (type === 'success') {
            setTimeout(() => {
                formMessage.style.display = 'none';
            }, 5000);
        }
    }

    /**
     * Navigation au clavier
     */
    function handleKeyboardNavigation(e) {
        if (e.key === 'Escape' && navMenu.classList.contains('active')) {
            closeMobileMenu();
        }
        
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            const focusedElement = document.activeElement;
            const navLinks = Array.from(document.querySelectorAll('.nav-link'));
            const currentIndex = navLinks.indexOf(focusedElement);
            
            if (currentIndex !== -1) {
                e.preventDefault();
                const nextIndex = e.key === 'ArrowDown' 
                    ? (currentIndex + 1) % navLinks.length
                    : (currentIndex - 1 + navLinks.length) % navLinks.length;
                navLinks[nextIndex].focus();
            }
        }
    }

    /**
     * Fonctionnalités d'accessibilité supplémentaires
     */
    function addAccessibilityFeatures() {
        const skipLink = document.createElement('a');
        skipLink.href = '#main';
        skipLink.className = 'skip-link';
        skipLink.textContent = 'Aller au contenu principal';
        document.body.insertBefore(skipLink, document.body.firstChild);
        
        document.querySelectorAll('section').forEach((section, index) => {
            if (!section.getAttribute('aria-label')) {
                const title = section.querySelector('h2, h3');
                if (title) {
                    section.setAttribute('aria-label', title.textContent);
                }
            }
        });
    }

    /**
     * Gestion du redimensionnement
     */
    function handleResize() {
        if (window.innerWidth >= 768 && navMenu.classList.contains('active')) {
            closeMobileMenu();
        }
        
        if (!isReducedMotion) {
            requestAnimationFrame(() => {
                window.dispatchEvent(new Event('scroll'));
            });
        }
    }

    /**
     * Debounce function pour optimiser les performances
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Initialisation
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();