<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\{
    RedirectResponse,
    Request,
    Response
};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\{
    AbstractLoginFormAuthenticator,
    Passport\Badge\CsrfTokenBadge,
    Passport\Badge\RememberMeBadge,
    Passport\Badge\UserBadge,
    Passport\Credentials\PasswordCredentials,
    Passport\Passport
};
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UtilisateurAuthentificatorAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    /**
     * Authenticate the user based on request credentials
     */
    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');

        // Store the last username in session for future use (like pre-filling the form)
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * Handle successful authentication
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        // Redirect to the target path if it exists in session
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Default redirect after login
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    /**
     * Get the login URL
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}