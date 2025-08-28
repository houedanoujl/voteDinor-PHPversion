-- Données d'exemple pour les candidats
INSERT INTO candidates (prenom, nom, email, whatsapp, description, photo_url, votes_count, status, created_at, updated_at) VALUES
('Adjoua', 'Kouassi', 'adjoua.kouassi@example.com', '+22507123456', 'Passionnée de cuisine traditionnelle ivoirienne depuis mon enfance. J\'adore revisiter les plats de grand-mère avec une touche moderne.', '/images/avatar-1.svg', 0, 'approved', NOW(), NOW()),

('Koffi', 'Assouan', 'koffi.assouan@example.com', '+22507234567', 'Chef cuisinier professionnel, spécialisé dans la fusion entre cuisine française et africaine. 15 ans d\'expérience.', '/images/avatar-2.svg', 0, 'approved', NOW(), NOW()),

('Fatou', 'Traoré', 'fatou.traore@example.com', '+22507345678', 'Amoureuse des saveurs authentiques de Côte d\'Ivoire. Je cuisine avec le cœur et partage mes recettes familiales.', '/images/avatar-3.svg', 0, 'approved', NOW(), NOW()),

('Moussa', 'Diabaté', 'moussa.diabate@example.com', '+22507456789', 'Étudiant en hôtellerie-restauration, passionné par l\'art culinaire et les traditions gastronomiques ivoiriennes.', '/images/avatar-4.svg', 0, 'approved', NOW(), NOW()),

('Aminata', 'Koné', 'aminata.kone@example.com', '+22507567890', 'Bloggeuse culinaire et photographe. J\'immortalise les plats traditionnels avec un œil artistique moderne.', '/images/avatar-5.svg', 0, 'approved', NOW(), NOW()),

('Ibrahim', 'Ouattara', 'ibrahim.ouattara@example.com', '+22507678901', 'Restaurateur depuis 10 ans, je perpétue les traditions culinaires tout en innovant pour les nouvelles générations.', '/images/avatar-6.svg', 0, 'pending', NOW(), NOW());

-- Utilisateur admin
INSERT INTO users (name, email, email_verified_at, password, created_at, updated_at) VALUES
('Jean-Luc Admin', 'jeanluc@bigfiveabidjan.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());