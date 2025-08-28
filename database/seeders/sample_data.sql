-- Données d'exemple pour les candidats
INSERT INTO candidates (prenom, nom, email, whatsapp, description, photo_url, votes_count, status, created_at, updated_at) VALUES
('Adjoua', 'Kouassi', 'adjoua.kouassi@example.com', '+22507123456', 'Passionnée de cuisine traditionnelle ivoirienne depuis mon enfance. J\'adore revisiter les plats de grand-mère avec une touche moderne.', 'https://images.unsplash.com/photo-1494790108755-2616c78d5823?w=400&h=400&fit=crop&crop=face', 0, 'approved', NOW(), NOW()),

('Koffi', 'Assouan', 'koffi.assouan@example.com', '+22507234567', 'Chef cuisinier professionnel, spécialisé dans la fusion entre cuisine française et africaine. 15 ans d\'expérience.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face', 0, 'approved', NOW(), NOW()),

('Fatou', 'Traoré', 'fatou.traore@example.com', '+22507345678', 'Amoureuse des saveurs authentiques de Côte d\'Ivoire. Je cuisine avec le cœur et partage mes recettes familiales.', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop&crop=face', 0, 'approved', NOW(), NOW()),

('Moussa', 'Diabaté', 'moussa.diabate@example.com', '+22507456789', 'Étudiant en hôtellerie-restauration, passionné par l\'art culinaire et les traditions gastronomiques ivoiriennes.', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face', 0, 'approved', NOW(), NOW()),

('Aminata', 'Koné', 'aminata.kone@example.com', '+22507567890', 'Bloggeuse culinaire et photographe. J\'immortalise les plats traditionnels avec un œil artistique moderne.', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=400&fit=crop&crop=face', 0, 'approved', NOW(), NOW()),

('Ibrahim', 'Ouattara', 'ibrahim.ouattara@example.com', '+22507678901', 'Restaurateur depuis 10 ans, je perpétue les traditions culinaires tout en innovant pour les nouvelles générations.', 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop&crop=face', 0, 'pending', NOW(), NOW());

-- Utilisateur admin
INSERT INTO users (name, email, email_verified_at, password, created_at, updated_at) VALUES
('Jean-Luc Admin', 'jeanluc@bigfiveabidjan.com', NOW(), '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());