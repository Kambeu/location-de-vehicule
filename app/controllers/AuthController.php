<?php

require_once APP_ROOT . '/app/models/UserModel.php';

/**
 * AuthController — Connexion, inscription, déconnexion.
 */
class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // ================================================================
    //  CONNEXION
    // ================================================================
    public function login(): void
    {
        $this->redirectIfLoggedIn('home');

        $errors          = [];
        $rememberedEmail = htmlspecialchars($_COOKIE['remember_email'] ?? '');

        if ($this->isPost()) {
            $email    = $this->post('email');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            if (empty($email) || empty($password)) {
                $errors[] = "Veuillez remplir tous les champs.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'adresse email est invalide.";
            } else {
                $user = $this->userModel->findByEmail($email);

                if (!$user || !password_verify($password, $user['MOT_DE_PASSE'])) {
                    $errors[] = "Email ou mot de passe incorrect.";
                } else {
                    Session::setUser($user);

                    if ($remember) {
                        setcookie('remember_email', $email, time() + (30 * 24 * 3600), '/', '', false, true);
                    } else {
                        setcookie('remember_email', '', time() - 3600, '/');
                    }

                    $prenom = htmlspecialchars($user['PRENOM']);
                    Session::setFlash('success', "Bienvenue, {$prenom} !");

                    if (($user['ROLE'] ?? 'client') === 'admin') {
                        $this->redirect('admin-dashboard');
                    } else {
                        $this->redirect('home');
                    }
                }
            }
        }

        $this->renderPartial('auth/login', [
            'page_title'      => "Connexion — " . APP_NAME,
            'errors'          => $errors,
            'rememberedEmail' => $rememberedEmail,
        ]);
    }

    // ================================================================
    //  INSCRIPTION
    // ================================================================
    public function register(): void
    {
        $this->redirectIfLoggedIn('home');

        $errors = [];
        $old    = [];

        if ($this->isPost()) {

            // Récupération
            $old = [
                'nom'       => $this->post('nom'),
                'prenom'    => $this->post('prenom'),
                'email'     => $this->post('email'),
                'telephone' => $this->post('telephone'),
            ];
            $password        = $_POST['password']         ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            // Validations
            if (empty($old['nom']))
                $errors[] = "Le nom est obligatoire.";
            if (empty($old['prenom']))
                $errors[] = "Le prénom est obligatoire.";
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL))
                $errors[] = "L'adresse email est invalide.";
            if (strlen($password) < 8)
                $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
            if ($password !== $passwordConfirm)
                $errors[] = "Les mots de passe ne correspondent pas.";
            if (!empty($old['telephone']) && !preg_match('/^\d{9}$/', $old['telephone']))
                $errors[] = "Le numéro de téléphone doit contenir exactement 9 chiffres.";

            // Vérifier unicité email
            if (empty($errors) && $this->userModel->emailExists($old['email']))
                $errors[] = "Cette adresse email est déjà utilisée.";

            // Insertion
            if (empty($errors)) {
                $success = $this->userModel->create([
                    'nom'          => $old['nom'],
                    'prenom'       => $old['prenom'],
                    'email'        => $old['email'],
                    'mot_de_passe' => $password,
                    'telephone'    => !empty($old['telephone']) ? $old['telephone'] : null,
                ]);

                if ($success) {
                    Session::setFlash('success', "Compte créé avec succès ! Connectez-vous.");
                    $this->redirect('login');
                } else {
                    $errors[] = "Erreur lors de la création du compte. Vérifiez les logs PHP.";
                }
            }
        }

        $this->renderPartial('auth/register', [
            'page_title' => "Inscription — " . APP_NAME,
            'errors'     => $errors,
            'old'        => $old,
        ]);
    }

    // ================================================================
    //  DÉCONNEXION
    // ================================================================
    public function logout(): void
    {
        if (Session::isLoggedIn()) {
            Session::destroy();
        }
        setcookie('remember_email', '', time() - 3600, '/');
        Session::start();
        Session::setFlash('success', "Vous avez été déconnecté.");
        $this->redirect('login');
    }
}
