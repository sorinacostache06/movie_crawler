<?php
namespace AppBundle\Controller;
use MongoDB\Driver\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LocaleController extends Controller
{
    /**
     * @param string $locale
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function switchLanguageAction($locale = 'en')
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $this->get('session')->set('_locale', $locale);
        return $this->redirect($request->headers->get('referer'));
    }
}