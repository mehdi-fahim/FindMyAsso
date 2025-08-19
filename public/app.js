// FindMyAsso - JavaScript principal

document.addEventListener('DOMContentLoaded', function() {
    console.log('FindMyAsso chargé !');
    
    // Gestion des formulaires (uniquement ceux marqués data-validate)
    const forms = document.querySelectorAll('form[data-validate="1"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Validation basique
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
                alert('Veuillez remplir tous les champs obligatoires.');
            }
        });
    });
    
    // Gestion des boutons de montant de don
    const donationBtns = document.querySelectorAll('.donation-amount-btn');
    const customAmountInput = document.getElementById('customAmount');
    
    if (donationBtns.length > 0 && customAmountInput) {
        donationBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Retirer la sélection précédente
                donationBtns.forEach(b => {
                    b.classList.remove('selected');
                    b.style.borderColor = '#d1d5db';
                    b.style.backgroundColor = 'transparent';
                });
                
                // Sélectionner le bouton cliqué
                this.classList.add('selected');
                this.style.borderColor = '#2563eb';
                this.style.backgroundColor = '#dbeafe';
                
                // Mettre à jour le montant personnalisé
                const amount = this.dataset.amount;
                customAmountInput.value = amount;
            });
        });
        
        // Réinitialiser la sélection si l'utilisateur tape un montant personnalisé
        customAmountInput.addEventListener('input', function() {
            donationBtns.forEach(b => {
                b.classList.remove('selected');
                b.style.borderColor = '#d1d5db';
                b.style.backgroundColor = 'transparent';
            });
        });
    }
    
    // Gestion du menu mobile
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
        });
    }
    
    // Gestion des modales
    const modalTriggers = document.querySelectorAll('[data-modal]');
    const modals = document.querySelectorAll('.modal');
    const modalCloses = document.querySelectorAll('.modal-close');
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.dataset.modal;
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    });
    
    modalCloses.forEach(close => {
        close.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
    
    // Fermer la modale en cliquant à l'extérieur
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
    
    // Gestion des onglets
    const tabTriggers = document.querySelectorAll('[data-tab]');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const tabId = this.dataset.tab;
            
            // Retirer la classe active de tous les onglets
            tabTriggers.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Ajouter la classe active à l'onglet sélectionné
            this.classList.add('active');
            const targetContent = document.getElementById(tabId);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
    
    // Gestion du scroll pour la navigation
    const nav = document.querySelector('.nav');
    if (nav) {
        window.addEventListener('scroll', function() {
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
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    animatedElements.forEach(el => observer.observe(el));
    
    // Gestion des favoris
    const favoriteBtns = document.querySelectorAll('.favorite-btn');
    favoriteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const animalId = this.dataset.animalId;
            
            // Toggle de l'état favori
            this.classList.toggle('favorited');
            
            // Sauvegarder en localStorage
            const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
            if (this.classList.contains('favorited')) {
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
});

// Fonctions utilitaires
function showNotification(message, type = 'info') {
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

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

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

// Export des fonctions pour utilisation globale
window.FindMyAsso = {
    showNotification,
    formatDate,
    debounce
};
