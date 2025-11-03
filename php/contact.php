<?php
/**
 * Script de Contact - Site Vitrine Psychologue
 * Traitement sécurisé des formulaires de contact
 * Envoi d'emails sans base de données
 */

// Configuration de sécurité
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Gestion CORS pour les requêtes AJAX
$allowed_origins = [
    'https://leosprdev.github.io',
    'https://sityof.github.io',
    'http://localhost',
    'http://127.0.0.1'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion des requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Configuration email - À PERSONNALISER
$config = [
    'to_email' => 'contact@sarahmartin-psy.fr',
    'to_name' => 'Dr. Sarah Martin',
    'from_email' => 'noreply@sarahmartin-psy.fr',
    'from_name' => 'Site Web - Contact',
    'smtp_host' => 'smtp.example.com', // À configurer selon votre hébergeur
    'smtp_port' => 587,
    'smtp_username' => 'noreply@sarahmartin-psy.fr',
    'smtp_password' => 'votre_mot_de_passe_smtp', // À sécuriser avec des variables d'environnement
    'use_smtp' => false // Mettre à true pour utiliser SMTP
];

/**
 * Fonction de validation et nettoyage des données
 */
function validateAndSanitize($data) {
    $errors = [];
    $clean_data = [];
    
    // Validation du nom
    if (empty($data['name'])) {
        $errors[] = 'Le nom est requis';
    } else {
        $clean_data['name'] = trim(strip_tags($data['name']));
        if (strlen($clean_data['name']) > 100) {
            $errors[] = 'Le nom ne peut pas dépasser 100 caractères';
        }
    }
    
    // Validation de l'email
    if (empty($data['email'])) {
        $errors[] = 'L\'email est requis';
    } else {
        $email = filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $errors[] = 'L\'adresse email n\'est pas valide';
        } else {
            $clean_data['email'] = $email;
        }
    }
    
    // Validation du téléphone (optionnel)
    if (!empty($data['phone'])) {
        $phone = preg_replace('/[^0-9+\s\-\.]/', '', $data['phone']);
        if (strlen($phone) > 20) {
            $errors[] = 'Le numéro de téléphone est trop long';
        } else {
            $clean_data['phone'] = $phone;
        }
    } else {
        $clean_data['phone'] = '';
    }
    
    // Validation du sujet
    if (empty($data['subject'])) {
        $errors[] = 'Le sujet est requis';
    } else {
        $clean_data['subject'] = trim(strip_tags($data['subject']));
        if (strlen($clean_data['subject']) > 200) {
            $errors[] = 'Le sujet ne peut pas dépasser 200 caractères';
        }
    }
    
    // Validation du message
    if (empty($data['message'])) {
        $errors[] = 'Le message est requis';
    } else {
        $clean_data['message'] = trim(strip_tags($data['message']));
        if (strlen($clean_data['message']) > 2000) {
            $errors[] = 'Le message ne peut pas dépasser 2000 caractères';
        }
        if (strlen($clean_data['message']) < 10) {
            $errors[] = 'Le message doit contenir au moins 10 caractères';
        }
    }
    
    // Vérification du consentement
    if (empty($data['consent'])) {
        $errors[] = 'Vous devez accepter le traitement de vos données personnelles';
    }
    
    return ['errors' => $errors, 'data' => $clean_data];
}

/**
 * Protection anti-spam basique
 */
function isSpam($data) {
    // Vérification du temps de soumission (honeypot temporel)
    if (isset($_SESSION['form_start_time'])) {
        $time_taken = time() - $_SESSION['form_start_time'];
        if ($time_taken < 3) { // Moins de 3 secondes = probablement un bot
            return true;
        }
    }
    
    // Vérification de mots-clés suspects
    $spam_keywords = ['viagra', 'casino', 'loan', 'mortgage', 'bitcoin', 'crypto'];
    $content = strtolower($data['message'] . ' ' . $data['subject']);
    
    foreach ($spam_keywords as $keyword) {
        if (strpos($content, $keyword) !== false) {
            return true;
        }
    }
    
    // Vérification d'URLs suspectes dans le message
    if (preg_match_all('/https?:\/\//', $data['message']) > 2) {
        return true;
    }
    
    return false;
}

/**
 * Limitation du taux de requêtes (rate limiting)
 */
function checkRateLimit() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $rate_limit_file = sys_get_temp_dir() . '/contact_rate_limit_' . md5($ip);
    
    if (file_exists($rate_limit_file)) {
        $data = json_decode(file_get_contents($rate_limit_file), true);
        $current_time = time();
        
        // Réinitialise le compteur toutes les heures
        if ($current_time - $data['timestamp'] > 3600) {
            $data = ['count' => 0, 'timestamp' => $current_time];
        }
        
        // Maximum 5 messages par heure par IP
        if ($data['count'] >= 5) {
            return false;
        }
        
        $data['count']++;
    } else {
        $data = ['count' => 1, 'timestamp' => time()];
    }
    
    file_put_contents($rate_limit_file, json_encode($data));
    return true;
}

/**
 * Création du contenu email HTML
 */
function createEmailContent($data) {
    $html = '
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nouveau message de contact</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #68d391; color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
            .field { margin-bottom: 20px; }
            .label { font-weight: bold; color: #2f855a; display: block; margin-bottom: 5px; }
            .value { background: white; padding: 10px; border-radius: 4px; border-left: 4px solid #68d391; }
            .message { background: white; padding: 15px; border-radius: 4px; border-left: 4px solid #68d391; white-space: pre-wrap; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class="header">
            <h2>Nouveau message de contact</h2>
            <p>Site Web - Dr. Sarah Martin</p>
        </div>
        <div class="content">
            <div class="field">
                <span class="label">Nom :</span>
                <div class="value">' . htmlspecialchars($data['name']) . '</div>
            </div>
            <div class="field">
                <span class="label">Email :</span>
                <div class="value"><a href="mailto:' . htmlspecialchars($data['email']) . '">' . htmlspecialchars($data['email']) . '</a></div>
            </div>';
            
    if (!empty($data['phone'])) {
        $html .= '
            <div class="field">
                <span class="label">Téléphone :</span>
                <div class="value"><a href="tel:' . htmlspecialchars($data['phone']) . '">' . htmlspecialchars($data['phone']) . '</a></div>
            </div>';
    }
    
    $html .= '
            <div class="field">
                <span class="label">Sujet :</span>
                <div class="value">' . htmlspecialchars($data['subject']) . '</div>
            </div>
            <div class="field">
                <span class="label">Message :</span>
                <div class="message">' . htmlspecialchars($data['message']) . '</div>
            </div>
        </div>
        <div class="footer">
            <p>Message reçu le ' . date('d/m/Y à H:i:s') . '</p>
            <p>Adresse IP : ' . $_SERVER['REMOTE_ADDR'] . '</p>
        </div>
    </body>
    </html>';
    
    return $html;
}

/**
 * Envoi d'email via SMTP ou fonction mail() PHP
 */
function sendEmail($to, $subject, $html_content, $from_email, $from_name, $reply_to = null) {
    global $config;
    
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
    
    if ($reply_to) {
        $headers[] = 'Reply-To: ' . $reply_to;
    }
    
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    
    if ($config['use_smtp']) {
        // Configuration SMTP (nécessite une bibliothèque comme PHPMailer en production)
        // Pour cet exemple, on utilise la fonction mail() standard
        return mail($to, $subject, $html_content, implode("\r\n", $headers));
    } else {
        // Utilisation de la fonction mail() PHP standard
        return mail($to, $subject, $html_content, implode("\r\n", $headers));
    }
}

/**
 * Logging des tentatives (optionnel)
 */
function logAttempt($data, $success = true, $error = '') {
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'success' => $success,
        'error' => $error,
        'email' => $data['email'] ?? '',
        'subject' => $data['subject'] ?? ''
    ];
    
    $log_file = __DIR__ . '/contact_log.json';
    $logs = [];
    
    if (file_exists($log_file)) {
        $logs = json_decode(file_get_contents($log_file), true) ?? [];
    }
    
    $logs[] = $log_entry;
    
    // Garde seulement les 1000 dernières entrées
    if (count($logs) > 1000) {
        $logs = array_slice($logs, -1000);
    }
    
    file_put_contents($log_file, json_encode($logs, JSON_PRETTY_PRINT));
}

// === TRAITEMENT PRINCIPAL ===

try {
    // Démarrage de session pour les protections
    session_start();
    
    // Vérification du rate limiting
    if (!checkRateLimit()) {
        http_response_code(429);
        echo json_encode([
            'success' => false, 
            'message' => 'Trop de tentatives. Veuillez patienter avant de renvoyer un message.'
        ]);
        exit();
    }
    
    // Récupération et validation des données
    $validation = validateAndSanitize($_POST);
    
    if (!empty($validation['errors'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Données invalides : ' . implode(', ', $validation['errors'])
        ]);
        logAttempt($_POST, false, 'Validation failed: ' . implode(', ', $validation['errors']));
        exit();
    }
    
    $clean_data = $validation['data'];
    
    // Vérification anti-spam
    if (isSpam($clean_data)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Message détecté comme spam.'
        ]);
        logAttempt($clean_data, false, 'Spam detected');
        exit();
    }
    
    // Préparation de l'email
    $email_subject = '[Contact Site Web] ' . $clean_data['subject'];
    $email_content = createEmailContent($clean_data);
    
    // Envoi de l'email
    $email_sent = sendEmail(
        $config['to_email'],
        $email_subject,
        $email_content,
        $config['from_email'],
        $config['from_name'],
        $clean_data['email']
    );
    
    if ($email_sent) {
        // Succès
        echo json_encode([
            'success' => true, 
            'message' => 'Votre message a été envoyé avec succès ! Je vous recontacterai dans les plus brefs délais.'
        ]);
        logAttempt($clean_data, true);
        
        // Nettoyage de session
        unset($_SESSION['form_start_time']);
        
    } else {
        // Erreur d'envoi
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Erreur lors de l\'envoi du message. Veuillez réessayer plus tard ou me contacter directement.'
        ]);
        logAttempt($clean_data, false, 'Email sending failed');
    }
    
} catch (Exception $e) {
    // Gestion des erreurs inattendues
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Une erreur technique est survenue. Veuillez réessayer plus tard.'
    ]);
    
    // Log de l'erreur (ne pas exposer les détails au client)
    error_log('Contact form error: ' . $e->getMessage());
    logAttempt($_POST ?? [], false, 'Exception: ' . $e->getMessage());
}
?>