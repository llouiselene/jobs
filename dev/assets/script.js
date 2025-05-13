document.addEventListener('DOMContentLoaded', function() {
    console.log("Le script principal est chargé");

    // Charger Chart.js dynamiquement si nécessaire
    function loadChartJS() {
        return new Promise((resolve, reject) => {
            if (typeof Chart !== 'undefined') {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Échec du chargement de Chart.js'));
            document.head.appendChild(script);
        });
    }

    // Initialiser les graphiques après le chargement de Chart.js
    function initializeCharts() {
        // Masquer les images de remplacement
        document.querySelectorAll('.graph-container img').forEach(img => img.style.display = 'none');
        
        // Afficher les conteneurs de graphiques
        document.querySelectorAll('.chart-container').forEach(container => {
            container.style.display = 'block';
            container.style.height = '250px'; // Hauteur fixe pour éviter les problèmes de dimensionnement
            container.style.width = '100%';
        });

        const ctxOffres = document.getElementById('offresParJour');
        const ctxEntreprises = document.getElementById('entreprisesRecrutement');
        const ctxnoteentreprise = document.getElementById('noteentreprise');
        const ctxposteetudiant = document.getElementById('posteetudiant');

        if (ctxOffres) {
            new Chart(ctxOffres, {
                type: 'bar',
                data: {
                    labels: ['1', '3', '5', '7', '9', '11', '13', '15', '17', '19', '21', '23', '25', '27', '29'],
                    datasets: [{
                        label: 'Offres publiées',
                        data: [100, 120, 80, 0, 0, 100, 0, 100, 0, 0, 100, 0, 0, 10, 400, 0, 1, 0, 300, 500, 1000, 200, 500, 1000, 200],
                        backgroundColor: '#ffffff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'logarithmic',
                            min: 1,
                            ticks: {
                                color: 'white' // Couleur des étiquettes adaptée au thème sombre
                            }
                        },
                        x: {
                            ticks: {
                                color: 'white' // Couleur des étiquettes adaptée au thème sombre
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: 'white' // Couleur de la légende adaptée au thème sombre
                            }
                        }
                    }
                }
            });
        }

        if (ctxEntreprises) {
            new Chart(ctxEntreprises, {
                type: 'bar',
                data: {
                    labels: ['simen','razer','ritogames','tempète de neige'],
                    datasets: [{
                        label: 'Offres par entreprise',
                        data: [100, 120, 80, 0, 0, 100, 0, 100, 0, 0, 100, 0, 0, 10, 400, 0, 1, 0, 300, 500, 1000, 200, 500, 1000, 200],
                        backgroundColor: '#ffffff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'logarithmic',
                            min: 1,
                            ticks: {
                                color: 'white' // Couleur des étiquettes adaptée au thème sombre
                            }
                        },
                        x: {
                            ticks: {
                                color: 'white' // Couleur des étiquettes adaptée au thème sombre
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: 'white' // Couleur de la légende adaptée au thème sombre
                            }
                        }
                    }
                }
            });
        }

        if (ctxnoteentreprise) {
            new Chart(ctxnoteentreprise, {
                type: 'bar',
                data: {
                    labels: ['simen','razer','ritogames','tempète de neige'],
                    datasets: [{
                        label: 'Offres par entreprise',
                        data: [100, 120, 80, 0, 0, 100, 0, 100, 0, 0, 100, 0, 0, 10, 400, 0, 1, 0, 300, 500, 1000, 200, 500, 1000, 200],
                        backgroundColor: '#ffffff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'logarithmic',
                            min: 1,
                            ticks: {
                                color: 'white' // Couleur des étiquettes adaptée au thème sombre
                            }
                        },
                        x: {
                            ticks: {
                                color: 'white' // Couleur des étiquettes adaptée au thème sombre
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: 'white' // Couleur de la légende adaptée au thème sombre
                            }
                        }
                    }
                }
            });
        }

        if (ctxposteetudiant) {
            new Chart(ctxposteetudiant, {
                type: 'bar',
                data: {
                    labels: ['simen','razer','ritogames','tempète de neige'],
                    datasets: [{
                        label: 'Offres par entreprise',
                        data: [100, 120, 80, 0, 0, 100, 0, 100, 0, 0, 100, 0, 0, 10, 400, 0, 1, 0, 300, 500, 1000, 200, 500, 1000, 200],
                        backgroundColor: '#ffffff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'logarithmic',
                            min: 1,
                            ticks: {
                                color: 'white' // Couleur des étiquettes adaptée au thème sombre
                            }
                        },
                        x: {
                            ticks: {
                                color: 'white' // Couleur des étiquettes adaptée au thème sombre
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: 'white' // Couleur de la légende adaptée au thème sombre
                            }
                        }
                    }
                }
            });
        }
    }

    // Gestion du Menu Déroulant
    const menuIcon = document.getElementById('menuIcon');
    const dropdownMenu = document.getElementById('dropdownMenu');
    if (menuIcon && dropdownMenu) {
        menuIcon.addEventListener('click', () => {
            menuIcon.classList.toggle('show');
            dropdownMenu.classList.toggle('show');
        });

        dropdownMenu.addEventListener('click', (event) => {
            if (event.target.tagName === 'A') {
                const url = event.target.getAttribute('data-url');
                if (url) window.location.href = url;
            }
        });
    }

    // Gestion de la Recherche 
    const searchIcon = document.getElementById('searchIcon');
    const searchInput = document.getElementById('searchInput');
    if (searchIcon && searchInput) {
        searchIcon.addEventListener('click', (event) => {
            searchInput.classList.toggle('show');
            event.stopPropagation();
        });
        searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                console.log('Recherche pour :', searchInput.value);
            }
        });
    }

    // Gestion des Notations par Étoiles
    const ratingGroups = document.querySelectorAll('.rating');
    ratingGroups.forEach((group, index) => {
        const stars = group.querySelectorAll('.fa-star');
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                console.log(`Étoile cliquée dans le groupe ${index + 1}:`, rating);
                updateStars(stars, rating);
            });
        });
    });

    function updateStars(stars, rating) {
        stars.forEach(star => {
            star.classList.toggle('active', star.getAttribute('data-rating') <= rating);
        });
    }

    // Gestion des Clics Hors Menu et Recherche 
    window.addEventListener('click', (event) => {
        if (dropdownMenu && !event.target.closest('.menu-icon-container')) {
            dropdownMenu.classList.remove('show');
            menuIcon?.classList.remove('show');
        }
        if (searchInput && !event.target.closest('#searchIcon, #searchInput')) {
            searchInput.classList.remove('show');
        }
    });

    // Charger Chart.js et initialiser les graphiques
    loadChartJS()
        .then(initializeCharts)
        .catch(error => {
            console.error('Erreur de chargement des graphiques:', error);
        });
});


document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("passwordForm");

    // Ne exécute le code que si le formulaire existe
    if (form) {
        const password = document.getElementById("password");
        const confirmPassword = document.getElementById("confirmPassword");
        const message = document.getElementById("message");
        const inputs = document.querySelectorAll(".form-group input");
        const email = inputs[3];
        const phone = inputs[4];

        form.addEventListener("submit", function (event) {
            event.preventDefault();

            let valid = true;
            let errorMessage = "";

            // Vérification des champs vides
            inputs.forEach(input => {
                if (input.value.trim() === "") {
                    valid = false;
                    errorMessage = "Tous les champs doivent être remplis.";
                }
            });

            // Validation email
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(email.value)) {
                valid = false;
                errorMessage = "L'adresse e-mail n'est pas valide.";
            }

            // Validation téléphone
            const phoneRegex = /^\d{10}$/;
            if (!phoneRegex.test(phone.value)) {
                valid = false;
                errorMessage = "Le numéro de téléphone doit contenir exactement 10 chiffres.";
            }

            // Validation mot de passe
            const passwordValue = password.value;
            const confirmPasswordValue = confirmPassword.value;
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (passwordValue !== confirmPasswordValue) {
                valid = false;
                errorMessage = "Les mots de passe ne correspondent pas.";
            }

            if (!passwordRegex.test(passwordValue)) {
                valid = false;
                errorMessage = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
            }

            // Affichage du résultat
            if (message) {
                message.textContent = errorMessage;
                message.style.color = valid ? "green" : "red";
            }

            if (valid) {
                form.submit(); // Soumet le formulaire si tout est valide
            }
        });
    }
});


document.addEventListener('DOMContentLoaded', function() {
    const formConnexion = document.getElementById('form-connexion-standard');
    const emailInput = document.getElementById('courriel');
    const emailValidationMessage = document.querySelector('.email-validation-message');
    const alertDivContainer = document.querySelector('.form-connexion'); // Le conteneur du formulaire pour insérer les alertes

    // Validation email en temps réel
    if (emailInput) {
        emailInput.addEventListener('input', function () {
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+.[a-zA-Z]{2,4}$/;

            if (emailInput.value === '') {
                emailValidationMessage.textContent = '';
                emailValidationMessage.className = 'email-validation-message';
            } else if (!emailRegex.test(emailInput.value)) {
                emailValidationMessage.textContent = 'Format d email invalide';
                emailValidationMessage.className = 'email-validation-message error';
                emailInput.setCustomValidity('Format d email invalide');
            } else {
                emailValidationMessage.textContent = 'Format d email valide';
                emailValidationMessage.className = 'email-validation-message valid';
                emailInput.setCustomValidity('');
            }
        });
    }
})

// Gestion de la barre de recherche
document.addEventListener('DOMContentLoaded', function() {
    // Pour chaque barre de recherche de la page (si jamais tu en as plusieurs)
    document.querySelectorAll('.search-block').forEach(function(block) {
        const searchIcon = block.querySelector('.search-icon');
        // Le formulaire qui suit la search-block ou qui est dans le même parent
        let searchForm = block.parentNode.querySelector('.search-form');
        if (!searchForm) {
            // Si pas trouvé, essaie dans le document (fallback)
            searchForm = document.getElementById('searchForm');
        }
        if (searchIcon && searchForm) {
            searchIcon.addEventListener('click', function(e) {
                e.stopPropagation();
                if (searchForm.style.display === 'block') {
                    searchForm.style.display = 'none';
                } else {
                    searchForm.style.display = 'block';
                    // Focus sur le premier champ texte du formulaire
                    const firstInput = searchForm.querySelector('input[type="text"], input:not([type])');
                    if (firstInput) firstInput.focus();
                }
            });
            // Fermer si on clique ailleurs
            document.addEventListener('click', function(event) {
                if (!searchForm.contains(event.target) && !searchIcon.contains(event.target)) {
                    searchForm.style.display = 'none';
                }
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.closest('.wishlist-heart')) {
            e.preventDefault();
            const heart = e.target.closest('.wishlist-heart');
            const icon = heart.querySelector('i');
            const offreId = heart.getAttribute('data-id');

            fetch('?uri=wishlist_toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id_offre=' + encodeURIComponent(offreId)
            })
                .then(response => response.text())
                .then(data => {
                    console.log('Réponse serveur:', data); // Ajoute cette ligne
                    if (data === 'OK') {
                        window.location.reload();
                    }
                });
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const submitButton = document.querySelector('.bouton-fixe');
    if (submitButton) {
        submitButton.addEventListener('click', function() {
            document.getElementById('entrepriseForm').submit();
        });
    }
});



