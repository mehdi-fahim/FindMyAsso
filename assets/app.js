// FindMyAsso - Application principale
import './styles/app.css';

console.log('FindMyAsso - Application chargée !');

// Gestion des composants de l'application
class FindMyAssoApp {
    constructor() {
        this.init();
    }
    
    init() {
        this.initForms();
        this.initDonationButtons();
        this.initMobileMenu();
        this.initModals();
        this.initTabs();
        this.initScrollEffects();
        this.initFavorites();
    }
    
    initForms() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', this.handleFormSubmit.bind(this));
        });
    }
    
    handleFormSubmit(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');
            } else {
                field.classList.remove('error');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            this.showNotification('Veuillez remplir tous les champs obligatoires.', 'error');
        }
    }
    
    initDonationButtons() {
        const donationBtns = document.querySelectorAll('.donation-amount-btn');
        const customAmountInput = document.getElementById('customAmount');
        
        if (donationBtns.length > 0 && customAmountInput) {
            donationBtns.forEach(btn => {
                btn.addEventListener('click', this.handleDonationButtonClick.bind(this));
            });
            
            customAmountInput.addEventListener('input', this.handleCustomAmountInput.bind(this));
        }
    }
    
    handleDonationButtonClick(e) {
        const btn = e.currentTarget;
        const amount = btn.dataset.amount;
        
        // Retirer la sélection précédente
        document.querySelectorAll('.donation-amount-btn').forEach(b => {
            b.classList.remove('selected');
            b.style.borderColor = '#d1d5db';
            b.style.backgroundColor = 'transparent';
        });
        
        // Sélectionner le bouton cliqué
        btn.classList.add('selected');
        btn.style.borderColor = '#2563eb';
        btn.style.backgroundColor = '#dbeafe';
        
        // Mettre à jour le montant personnalisé
        const customAmountInput = document.getElementById('customAmount');
        if (customAmountInput) {
            customAmountInput.value = amount;
        }
    }
    
    handleCustomAmountInput() {
        document.querySelectorAll('.donation-amount-btn').forEach(b => {
            b.classList.remove('selected');
            b.style.borderColor = '#d1d5db';
            b.style.backgroundColor = 'transparent';
        });
    }
    
    initMobileMenu() {
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const mobileMenu = document.querySelector('.mobile-menu');
        
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('active');
            });
        }
    }
    
    initModals() {
        const modalTriggers = document.querySelectorAll('[data-modal]');
        const modals = document.querySelectorAll('.modal');
        const modalCloses = document.querySelectorAll('.modal-close');
        
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = trigger.dataset.modal;
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            });
        });
        
        modalCloses.forEach(close => {
            close.addEventListener('click', () => {
                const modal = close.closest('.modal');
                if (modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
        
        modals.forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
    }
    
    initTabs() {
        const tabTriggers = document.querySelectorAll('[data-tab]');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const tabId = trigger.dataset.tab;
                
                // Retirer la classe active de tous les onglets
                tabTriggers.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Ajouter la classe active à l'onglet sélectionné
                trigger.classList.add('active');
                const targetContent = document.getElementById(tabId);
                if (targetContent) {
                    targetContent.classList.add('active');
                }
            });
        });
    }
    
    initScrollEffects() {
        const nav = document.querySelector('.nav');
        if (nav) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 100) {
                    nav.classList.add('scrolled');
                } else {
                    nav.classList.remove('scrolled');
                }
            });
        }
        
        // Animation au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);
        
        const animatedElements = document.querySelectorAll('.animate-on-scroll');
        animatedElements.forEach(el => observer.observe(el));
    }
    
    initFavorites() {
        const favoriteBtns = document.querySelectorAll('.favorite-btn');
        favoriteBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const animalId = btn.dataset.animalId;
                
                // Toggle de l'état favori
                btn.classList.toggle('favorited');
                
                // Sauvegarder en localStorage
                const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
                if (btn.classList.contains('favorited')) {
                    if (!favorites.includes(animalId)) {
                        favorites.push(animalId);
                    }
                } else {
                    const index = favorites.indexOf(animalId);
                    if (index > -1) {
                        favorites.splice(index, 1);
                    }
                }
                localStorage.setItem('favorites', JSON.stringify(favorites));
            });
        });
        
        // Charger l'état des favoris au chargement
        const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
        favoriteBtns.forEach(btn => {
            const animalId = btn.dataset.animalId;
            if (favorites.includes(animalId)) {
                btn.classList.add('favorited');
            }
        });
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Afficher la notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        // Masquer et supprimer la notification
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
}

// Initialiser l'application quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    window.findMyAssoApp = new FindMyAssoApp();
});

// Export pour utilisation globale
export default FindMyAssoApp;
