<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended('/');
        }

        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()
            ->withErrors(['email' => 'Identifiants invalides.'])
            ->onlyInput('email');
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Affiche le formulaire de mot de passe oublié.
     */
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Envoie le code de réinitialisation par email.
     */
    public function sendResetCode(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()
                ->withErrors(['email' => 'Aucun compte trouvé avec cet email.'])
                ->onlyInput('email');
        }

        // Générer un code à 6 chiffres
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Stocker le code dans password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Str::random(60),
                'code' => Hash::make($code),
                'code_expires_at' => now()->addMinutes(15),
                'created_at' => now(),
            ]
        );

        // Envoyer l'email avec le code
        Mail::to($user->email)->send(new PasswordResetCode($code, $user->email));

        // Stocker l'email dans la session (pas en flash pour qu'il persiste)
        $request->session()->put('password_reset_email', $user->email);

        return redirect()->route('password.code.verify')
            ->with('status', 'Un code de vérification a été envoyé à votre adresse email.');
    }

    /**
     * Affiche le formulaire de vérification du code.
     */
    public function showCodeVerificationForm(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()->route('password.forgot')
                ->with('error', 'Veuillez d\'abord demander un code de réinitialisation.');
        }

        return view('auth.code-verification', ['email' => $email]);
    }

    /**
     * Vérifie le code de réinitialisation.
     */
    public function verifyResetCode(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
        ], [
            'code.required' => 'Le code est requis.',
            'code.regex' => 'Le code doit contenir exactement 6 chiffres.',
        ]);

        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()->route('password.forgot')
                ->with('error', 'Session expirée. Veuillez recommencer.');
        }

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $resetToken || ! $resetToken->code) {
            return back()
                ->withErrors(['code' => 'Code invalide ou expiré.'])
                ->withInput();
        }

        // Vérifier l'expiration
        if (now()->greaterThan($resetToken->code_expires_at)) {
            return back()
                ->withErrors(['code' => 'Le code a expiré. Veuillez en demander un nouveau.'])
                ->withInput();
        }

        // Vérifier le code
        if (! Hash::check($request->code, $resetToken->code)) {
            return back()
                ->withErrors(['code' => 'Code incorrect.'])
                ->withInput();
        }

        // Code valide, rediriger vers la réinitialisation
        $request->session()->put('reset_code_verified', true);
        // L'email est déjà dans la session, pas besoin de le remettre

        return redirect()->route('password.reset');
    }

    /**
     * Affiche le formulaire de réinitialisation du mot de passe.
     */
    public function showResetPasswordForm(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('reset_code_verified')) {
            return redirect()->route('password.forgot')
                ->with('error', 'Veuillez d\'abord vérifier le code.');
        }

        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()->route('password.forgot')
                ->with('error', 'Session expirée. Veuillez recommencer.');
        }

        return view('auth.reset-password', ['email' => $email]);
    }

    /**
     * Réinitialise le mot de passe.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        if (! $request->session()->get('reset_code_verified')) {
            return redirect()->route('password.forgot')
                ->with('error', 'Session expirée. Veuillez recommencer.');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = $request->session()->get('password_reset_email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('password.forgot')
                ->with('error', 'Utilisateur introuvable.');
        }

        // Mettre à jour le mot de passe (le cast 'hashed' dans le modèle User gère automatiquement le hashage)
        $user->password = $request->password;
        $user->save();

        // Supprimer le token de réinitialisation
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        // Nettoyer la session
        $request->session()->forget(['reset_code_verified', 'password_reset_email']);

        return redirect()->route('login')
            ->with('status', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }

    /**
     * Renvoie un nouveau code de réinitialisation.
     */
    public function resendResetCode(Request $request): RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()->route('password.forgot')
                ->with('error', 'Veuillez d\'abord demander un code de réinitialisation.');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('password.forgot')
                ->with('error', 'Utilisateur introuvable.');
        }

        // Générer un nouveau code
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Mettre à jour le code
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Str::random(60),
                'code' => Hash::make($code),
                'code_expires_at' => now()->addMinutes(15),
                'created_at' => now(),
            ]
        );

        // Envoyer l'email
        Mail::to($user->email)->send(new PasswordResetCode($code, $user->email));

        return back()->with('status', 'Un nouveau code a été envoyé à votre adresse email.');
    }
}
